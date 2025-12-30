<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:owner']);
    }

    public function index(Request $request)
    {
        $search = $request->get('q');

        $users = User::query()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('users.index', compact('users', 'search'));
    }

    public function create()
    {
        $roles = ['owner', 'kasir', 'admin_gudang'];
        return view('users.create', compact('roles'));
    }

    public function store(UserStoreRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_active' => true,
        ]);

        $user->syncRoles([$data['role']]);

        $this->logAction('USER_CREATE', $request->user()->id, $user->id, [
            'role' => $data['role'],
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $roles = ['owner', 'kasir', 'admin_gudang'];
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        $data = $request->validated();

        $newActive = array_key_exists('is_active', $data)
            ? (bool) $data['is_active']
            : (bool) $user->is_active;
        $this->guardOwnerCount($user, $data['role'] ?? $user->roles->first()?->name, $newActive);

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'is_active' => $newActive,
        ];

        if (!empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $user->update($payload);

        $currentRole = $user->roles->first()?->name;
        if ($currentRole !== $data['role']) {
            $user->syncRoles([$data['role']]);
            $this->logAction('USER_ROLE_CHANGE', $request->user()->id, $user->id, [
                'from' => $currentRole,
                'to' => $data['role'],
            ]);
        }

        $this->logAction('USER_UPDATE', $request->user()->id, $user->id, [
            'is_active' => $payload['is_active'],
            'password_reset' => !empty($data['password']),
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function disable(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Tidak dapat menonaktifkan diri sendiri.');
        }

        $this->guardOwnerCount($user, $user->roles->first()?->name, false);

        $user->update(['is_active' => false]);

        $this->logAction('USER_DISABLE', $request->user()->id, $user->id, []);

        return back()->with('success', 'User dinonaktifkan.');
    }

    protected function guardOwnerCount(User $target, string $newRole, bool $newActive): void
    {
        $isOwnerNow = $target->hasRole('owner');
        $willBeOwner = $newRole === 'owner';

        if ($isOwnerNow && (!$willBeOwner || !$newActive)) {
            $activeOwners = User::role('owner')->where('is_active', true)->count();
            if ($activeOwners <= 1) {
                throw ValidationException::withMessages([
                    'role' => 'Tidak boleh menghilangkan owner terakhir.',
                ]);
            }
        }
    }

    protected function logAction(string $action, int $actorId, ?int $targetId, array $meta = []): void
    {
        AuditLog::create([
            'actor_user_id' => $actorId,
            'target_user_id' => $targetId,
            'action' => $action,
            'meta' => $meta,
        ]);
    }
}
