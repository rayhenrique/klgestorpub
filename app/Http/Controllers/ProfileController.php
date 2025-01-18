<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) {
                if (!Hash::check($value, Auth::user()->password)) {
                    $fail('A senha atual está incorreta.');
                }
            }],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'current_password.required' => 'A senha atual é obrigatória.',
            'password.required' => 'A nova senha é obrigatória.',
            'password.confirmed' => 'A confirmação da nova senha não corresponde.',
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('profile.edit')
            ->with('success', 'Senha alterada com sucesso!');
    }
} 