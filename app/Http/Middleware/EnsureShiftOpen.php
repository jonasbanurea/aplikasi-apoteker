<?php

namespace App\Http\Middleware;

use App\Models\Shift;
use Closure;
use Illuminate\Http\Request;

class EnsureShiftOpen
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $openShift = Shift::where('user_id', $user->id)
            ->whereNull('closed_at')
            ->latest('opened_at')
            ->first();

        if (!$openShift) {
            return redirect()->route('shifts.create')->with('error', 'Buka shift terlebih dahulu sebelum melakukan transaksi.');
        }

        $request->attributes->set('open_shift', $openShift);

        return $next($request);
    }
}
