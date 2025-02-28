<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ChangePasswordController extends Controller
{
    public function show()
    {
        return view('auth.change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) {
                if (!Hash::check($value, auth()->user()->password)) {
                    $fail('Le mot de passe actuel est incorrect.');
                }
            }],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ]);

        $user = auth()->user();
        $user->password = Hash::make($request->password);
        $user->password_changed = true;
        $user->save();

        Log::info('Password changed', [
            'user_id' => $user->id,
            'password_changed' => $user->password_changed
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Votre mot de passe a été changé avec succès.');
    }
}
