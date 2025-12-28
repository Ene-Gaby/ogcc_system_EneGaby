<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PasswordController extends Controller
{
    public function change()
{
    return view('auth.password-change');
}
    
    public function update(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|string|min:8|confirmed',
    ]);

    if (!Hash::check($request->current_password, Auth::user()->password)) {
        return back()->withErrors(['current_password' => 'La contraseña actual no coincide.']);
    }

    Auth::user()->update([
        'password' => Hash::make($request->new_password),
    ]);

    return redirect()->route('home')->with('success', 'Contraseña actualizada exitosamente.');
}
}
