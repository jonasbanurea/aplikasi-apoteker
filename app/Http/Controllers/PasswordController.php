<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordChangeRequest;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function edit()
    {
        return view('profile.password');
    }

    public function update(PasswordChangeRequest $request)
    {
        $user = $request->user();

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $user->update([
            'password' => Hash::make($request->input('new_password')),
        ]);

        Auth::logoutOtherDevices($request->input('new_password'));

        AuditLog::create([
            'actor_user_id' => $user->id,
            'target_user_id' => $user->id,
            'action' => 'CHANGE_PASSWORD',
            'meta' => [],
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}
