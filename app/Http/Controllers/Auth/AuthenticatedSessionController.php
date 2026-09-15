<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $usuario = $request->user();

        // ==========================================
        // VENTAS → IMPORTACIÓN DE PEDIDOS
        // ==========================================

        if ($usuario->hasRole('Ventas')) {
            return redirect()->route('importaciones.index');
        }

        // ==========================================
        // DISEÑADOR Y SUPERVISOR → DASHBOARD ACTUAL
        // ==========================================

        if (
            $usuario->hasRole('Diseñador') ||
            $usuario->hasRole('Supervisor')
        ) {
            return redirect()->route('dashboard');
        }

        // ==========================================
        // RESTO → DASHBOARD PRODUCCIÓN
        // ==========================================

        return redirect()->route('dashboard.produccion');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
