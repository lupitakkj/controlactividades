<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = Actividad::with([
            'comentarios.user',
            'archivos.user',
            'user'
        ]);

        // SOLO SUS ACTIVIDADES
        if (auth()->user()->hasRole('Diseñador')) {

            $query->where(
                'user_id',
                auth()->id()
            );
        }

        // ==========================
        // BUSCADOR
        // ==========================

        if ($request->filled('buscar')) {

            $buscar = trim($request->buscar);

            $query->where(function ($q) use ($buscar) {

                $q->where('titulo', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%")
                    ->orWhere('cliente', 'like', "%{$buscar}%");
            });
        }

        // ==========================
        // FILTRO CLIENTE
        // ==========================

        if ($request->filled('cliente')) {

            $query->where('cliente', $request->cliente);
        }

        // ==========================
        // FILTRO PRIORIDAD
        // ==========================

        if ($request->filled('prioridad')) {

            $query->where('prioridad', $request->prioridad);
        }

        
        $pendientes = (clone $query)
            ->where('estado', 'pendiente')
            ->get();

        $proceso = (clone $query)
            ->where('estado', 'en_proceso')
            ->get();

        $pausadas = (clone $query)
            ->where('estado', 'pausada')
            ->get();

        $terminadas = (clone $query)
            ->where('estado', 'terminada')
            ->get();

        $disenadores = User::role('Diseñador')
            ->get();

        $clientes = Actividad::whereNotNull('cliente')
            ->where('cliente', '<>', '')
            ->select('cliente')
            ->distinct()
            ->orderBy('cliente')
            ->pluck('cliente');

        return view('dashboard', compact(
            'pendientes',
            'proceso',
            'pausadas',
            'terminadas',
            'disenadores',
            'clientes'
        ));
    }
}
