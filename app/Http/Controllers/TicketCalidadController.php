<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\TicketCalidad;
use App\Models\TicketCalidadPartida;
use App\Models\TicketLegacy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class TicketCalidadController extends Controller
{
    /**
     * Mostrar listado de tickets de calidad.
     */
    public function index()
    {
        $tickets = TicketLegacy::query()
            ->orderByDesc('fecha')
            ->get();

        return view('tickets-calidad.index', compact('tickets'));
    }

    /**
     * Mostrar detalle de un ticket de calidad
     * junto con sus archivos adjuntos.
     */
    public function show($id)
    {
        $ticket = TicketLegacy::findOrFail($id);

        $archivos = DB::connection('ticketsapp')
            ->table('ticket_archivos')
            ->where('ticket_id', $id)
            ->orderBy('id')
            ->get();

        foreach ($archivos as $archivo) {

            $ruta = trim($archivo->ruta, '/');

            $partes = explode('/', $ruta);

            $partes = array_map(
                fn($parte) => rawurlencode($parte),
                $partes
            );

            $archivo->url =
                'https://sistemarijaya.infinityfreeapp.com/'
                . implode('/', $partes);
        }

        return response()->json([
            'ticket' => $ticket,
            'archivos' => $archivos,
        ]);
    }

    public function actualizarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => [
                'required',
                'in:Abierto,En proceso,Cerrado',
            ],
        ]);

        $ticket = TicketLegacy::findOrFail($id);

        DB::connection('ticketsapp')
            ->table('tickets')
            ->where('id', $id)
            ->update([
                'estado' => $request->estado,
            ]);

        return response()->json([
            'success' => true,
            'mensaje' => 'Estado actualizado correctamente.',
            'estado' => $request->estado,
        ]);
    }

    public function descargarArchivo($id)
    {
        $archivo = DB::connection('ticketsapp')
            ->table('ticket_archivos')
            ->where('id', $id)
            ->first();

        if (!$archivo) {
            abort(404, 'Archivo no encontrado.');
        }

        $ruta = trim($archivo->ruta, '/');

        $partes = explode('/', $ruta);

        $partes = array_map(
            fn($parte) => rawurlencode($parte),
            $partes
        );

        $url = 'https://sistemarijaya.infinityfreeapp.com/'
            . implode('/', $partes);

        $response = Http::timeout(30)->get($url);

        if (!$response->successful()) {
            abort(404, 'No se pudo obtener el archivo.');
        }

        $nombre = $archivo->nombre_archivo ?: basename($ruta);

        $mime = $response->header('Content-Type')
            ?: 'application/octet-stream';

        return response(
            $response->body(),
            200,
            [
                'Content-Type' => $mime,
                'Content-Disposition' => 'attachment; filename="' . addslashes($nombre) . '"',
                'Content-Length' => strlen($response->body()),
            ]
        );
    }

    /**
     * Mostrar formulario de creación de ticket.
     *
     * Se mantiene temporalmente mientras terminamos
     * de implementar el modal.
     */
    public function create()
    {
        $pedidos = Pedido::with('partidas')
            ->orderByDesc('id')
            ->get();

        return view('tickets-calidad.create', compact('pedidos'));
    }

    /**
     * Crear un nuevo ticket de calidad.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pedido_id' => [
                'required',
                'exists:pedidos,id',
            ],

            'partidas' => [
                'required',
                'array',
                'min:1',
            ],

            'partidas.*.id' => [
                'required',
                'exists:partidas,id',
            ],

            'partidas.*.cantidad_afectada' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'nombre' => [
                'required',
                'string',
                'max:150',
            ],

            'correo' => [
                'nullable',
                'email',
                'max:150',
            ],

            'prioridad' => [
                'required',
                'in:Alta,Media,Baja',
            ],

            'tipo' => [
                'nullable',
                'string',
                'max:100',
            ],

            'subtipo' => [
                'nullable',
                'string',
                'max:100',
            ],

            'problema' => [
                'required',
                'string',
            ],
        ]);

        DB::transaction(function () use ($request) {

            /*
             * Obtener el siguiente consecutivo.
             */
            $consecutivo = DB::table('consecutivos_calidad')
                ->lockForUpdate()
                ->first();

            $siguienteNumero =
                ($consecutivo->ultimo_numero ?? 0) + 1;

            DB::table('consecutivos_calidad')
                ->where('id', $consecutivo->id)
                ->update([
                    'ultimo_numero' => $siguienteNumero,
                    'updated_at' => now(),
                ]);

            /*
             * Código del ticket.
             *
             * Ejemplo:
             * TC-000001
             */
            $ticketCodigo = 'TC-' . str_pad(
                $siguienteNumero,
                6,
                '0',
                STR_PAD_LEFT
            );

            /*
             * Crear ticket principal.
             */
            $ticket = TicketCalidad::create([
                'usuario_id' => Auth::id(),

                'ticket_codigo' => $ticketCodigo,

                'pedido_id' => $request->pedido_id,

                // Se conserva este campo por compatibilidad.
                'partida_id' => null,

                'nombre' => $request->nombre,

                'correo' => $request->correo,

                'prioridad' => $request->prioridad,

                'tipo' => $request->tipo,

                'subtipo' => $request->subtipo,

                'problema' => $request->problema,

                // La cantidad ahora se maneja por partida.
                'cantidad_afectada' => null,

                'estado' => 'Abierto',
            ]);

            /*
             * Guardar las partidas afectadas.
             */
            foreach ($request->partidas as $partida) {

                TicketCalidadPartida::create([
                    'ticket_id' => $ticket->id,

                    'partida_id' => $partida['id'],

                    'cantidad_afectada' =>
                    $partida['cantidad_afectada'] ?? null,
                ]);
            }
        });

        return redirect()
            ->route('tickets-calidad.index')
            ->with(
                'success',
                'Ticket de calidad creado correctamente.'
            );
    }
}
