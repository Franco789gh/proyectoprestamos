<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')->withErrors(['email' => 'Su cuenta se encuentra desactivada. Contacte al administrador.']);
        }

        if (!empty($roles) && !in_array($user->role, $roles)) {
            abort(403, 'No tiene permisos para acceder a este módulo.');
        }

        return $next($request);
    }
}
