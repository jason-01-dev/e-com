<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 🛑 Changement : Autorise l'accès si l'utilisateur est simplement CONNECTÉ (Auth::check()) 🛑
        
        if (Auth::check()) {
            // L'utilisateur est connecté, on lui donne l'accès à l'administration
            // (La vérification du rôle est temporairement ignorée)
            return $next($request);
        }

        // Si l'utilisateur n'est pas connecté, le renvoyer à la connexion
        return redirect()->route('login');
    }
}