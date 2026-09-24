<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingrese un correo electrónico válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'Su cuenta se encuentra inactiva. Comuníquese con el Administrador.',
                ]);
            }

            $request->session()->regenerate();

            AuditLog::logAction(
                'INICIO_SESION',
                "El usuario {$user->name} ({$user->email}) inició sesión.",
                $user
            );

            return redirect()->intended(route('dashboard'))
                ->with('success', "¡Bienvenido/a, {$user->name}!");
        }

        throw ValidationException::withMessages([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            AuditLog::logAction(
                'CIERRE_SESION',
                "El usuario {$user->name} cerró sesión.",
                $user
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('info', 'Ha cerrado sesión correctamente.');
    }

    public function profile()
    {
        return view('auth.profile', [
            'user' => Auth::user(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'current_password' => ['nullable', 'required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'current_password.current_password' => 'La contraseña actual no es correcta.',
            'new_password.min' => 'La nueva contraseña debe tener al menos 6 caracteres.',
            'new_password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        $user->name = $validated['name'];

        if (!empty($validated['new_password'])) {
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        AuditLog::logAction(
            'ACTUALIZACION_PERFIL',
            "El usuario {$user->name} actualizó su perfil.",
            $user
        );

        return back()->with('success', 'Perfil actualizado con éxito.');
    }
}
