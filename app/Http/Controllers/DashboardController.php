<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {

        $query = Actividad::with([
            'comentarios.user',
            'archivos',
            'user'
        ]);

        // SOLO SUS ACTIVIDADES

        if (auth()->user()->hasRole('disenador')) {

            $query->where(
                'user_id',
                auth()->user()->id
            );
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

        // DISEÑADORES

        $disenadores = User::role('Diseñador')
            ->get();

        return view('dashboard', compact(
            'pendientes',
            'proceso',
            'pausadas',
            'terminadas',
            'disenadores'
        ));
    }
}