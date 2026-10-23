<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\CompraOrden;
use App\Models\CompraOrdenDetalle;
use App\Models\CompraRecepcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CompraRecepcionController extends Controller
{
    private function autorizarConsulta(): void
    {
        abort_unless(auth()->user()->hasAnyRole(['Administrador', 'Compras', 'Almacén']), 403);
    }

    private function autorizarCierre(): void
    {
        abort_unless(auth()->user()->hasAnyRole(['Administrador', 'Compras']), 403,
            'Solo Administrador o Compras pueden autorizar el cierre con faltantes.');
    }

    public function store(Request $request, CompraOrden $orden)
    {
        $this->autorizarConsulta();

        if (in_array($orden->estado, ['recibida', 'cerrada_con_faltantes'], true)) {
            return back()->withErrors(['recepcion' => 'Esta orden ya está cerrada y no admite nuevas recepciones.']);
        }

        $data = $request->validate([
            'fecha_recepcion' => ['required', 'date'],
            'almacen_id' => ['required', 'integer', 'exists:almacenes,id'],
            'observaciones' => ['nullable', 'string', 'max:5000'],
            'evidencia' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240'],
            'cantidades' => ['required', 'array'],
            'cantidades.*' => ['nullable', 'numeric', 'min:0'],
            'observaciones_detalle' => ['nullable', 'array'],
            'observaciones_detalle.*' => ['nullable', 'string', 'max:1000'],
        ], [
            'fecha_recepcion.required' => 'Indica la fecha de recepción.',
            'almacen_id.required' => 'Selecciona el almacén donde se ingresó el material.',
            'almacen_id.exists' => 'El almacén seleccionado no existe.',
            'evidencia.required' => 'Debes adjuntar evidencia para registrar la recepción.',
            'evidencia.mimes' => 'La evidencia debe ser imagen JPG, PNG, WEBP o PDF.',
            'evidencia.max' => 'La evidencia no debe superar los 10 MB.',
            'cantidades.required' => 'Captura las cantidades recibidas.',
        ]);

        // Solo se permiten almacenes activos, aunque alguien manipule la petición.
        $almacenActivo = Almacen::whereKey($data['almacen_id'])->where('activo', true)->exists();
        if (!$almacenActivo) {
            throw ValidationException::withMessages([
                'almacen_id' => 'El almacén seleccionado está inactivo. Selecciona un almacén activo.',
            ]);
        }

        $detalles = $orden->detalles()->get()->keyBy('id');
        $cantidades = [];
        $hayCantidad = false;

        foreach ($detalles as $detalle) {
            $id = (string) $detalle->id;
            $cantidad = (float) ($data['cantidades'][$id] ?? 0);
            if ($cantidad < 0) {
                throw ValidationException::withMessages(['cantidades.'.$id => 'La cantidad no puede ser negativa.']);
            }

            $recibidoAntes = (float) DB::table('compras_recepciones_detalle')
                ->join('compras_recepciones', 'compras_recepciones.id', '=', 'compras_recepciones_detalle.recepcion_id')
                ->where('compras_recepciones.orden_id', $orden->id)
                ->where('compras_recepciones_detalle.orden_detalle_id', $detalle->id)
                ->sum('compras_recepciones_detalle.cantidad_recibida');

            $pendiente = max(0, (float)$detalle->cantidad_solicitada - $recibidoAntes);
            if ($cantidad > $pendiente + 0.0001) {
                throw ValidationException::withMessages([
                    'cantidades.'.$id => "La cantidad excede el pendiente de {$pendiente} para {$detalle->descripcion}.",
                ]);
            }
            if ($cantidad > 0) {
                $hayCantidad = true;
                $cantidades[$id] = $cantidad;
            }
        }

        if (!$hayCantidad) {
            throw ValidationException::withMessages(['cantidades' => 'Registra al menos una cantidad recibida mayor que cero.']);
        }

        $evidencia = $request->file('evidencia');
        $path = $evidencia->store('compras/recepciones', 'local');

        try {
            DB::transaction(function () use ($orden, $data, $cantidades, $evidencia, $path) {
                $recepcion = CompraRecepcion::create([
                    'orden_id' => $orden->id,
                    'almacen_id' => (int) $data['almacen_id'],
                    'fecha_recepcion' => $data['fecha_recepcion'],
                    'recibido_por' => auth()->id(),
                    'observaciones' => $data['observaciones'] ?? null,
                    'evidencia_path' => $path,
                    'evidencia_nombre_original' => $evidencia->getClientOriginalName(),
                    'evidencia_mime' => $evidencia->getMimeType(),
                    'evidencia_bytes' => $evidencia->getSize(),
                ]);

                foreach ($cantidades as $detalleId => $cantidad) {
                    $recepcion->detalles()->create([
                        'orden_detalle_id' => (int)$detalleId,
                        'cantidad_recibida' => $cantidad,
                        'observaciones' => $data['observaciones_detalle'][$detalleId] ?? null,
                    ]);
                }

                $this->actualizarEstadoOrden($orden->fresh());
            });
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($path);
            throw $e;
        }

        return redirect()->route('compras.ordenes.show', $orden)->with('success', 'Recepción registrada con evidencia.');
    }

    public function cerrarConFaltantes(Request $request, CompraOrden $orden)
    {
        $this->autorizarCierre();

        $data = $request->validate([
            'motivo_cierre_faltantes' => ['required', 'string', 'min:10', 'max:5000'],
        ], [
            'motivo_cierre_faltantes.required' => 'La justificación del cierre con faltantes es obligatoria.',
            'motivo_cierre_faltantes.min' => 'Explica el motivo con al menos 10 caracteres.',
        ]);

        if (in_array($orden->estado, ['recibida', 'cerrada_con_faltantes'], true)) {
            return back()->withErrors(['cierre' => 'La orden ya está cerrada.']);
        }

        $pendiente = $this->totalPendiente($orden);
        if ($pendiente <= 0.0001) {
            return back()->withErrors(['cierre' => 'La orden está completa; no requiere cierre con faltantes.']);
        }

        $orden->update([
            'estado' => 'cerrada_con_faltantes',
            'fecha_cierre_faltantes' => now(),
            'cierre_faltantes_por' => auth()->id(),
            'motivo_cierre_faltantes' => trim($data['motivo_cierre_faltantes']),
            'actualizado_por' => auth()->id(),
        ]);

        return redirect()->route('compras.ordenes.show', $orden)->with('success', 'Orden cerrada con faltantes y autorización registrada.');
    }

    public function descargarEvidencia(CompraRecepcion $recepcion)
    {
        $this->autorizarConsulta();
        abort_unless(Storage::disk('local')->exists($recepcion->evidencia_path), 404);
        return Storage::disk('local')->download(
            $recepcion->evidencia_path,
            $recepcion->evidencia_nombre_original ?: basename($recepcion->evidencia_path)
        );
    }

    private function totalPendiente(CompraOrden $orden): float
    {
        $total = 0.0;
        foreach ($orden->detalles as $detalle) {
            $recibido = (float) DB::table('compras_recepciones_detalle')
                ->join('compras_recepciones', 'compras_recepciones.id', '=', 'compras_recepciones_detalle.recepcion_id')
                ->where('compras_recepciones.orden_id', $orden->id)
                ->where('compras_recepciones_detalle.orden_detalle_id', $detalle->id)
                ->sum('compras_recepciones_detalle.cantidad_recibida');
            $total += max(0, (float)$detalle->cantidad_solicitada - $recibido);
        }
        return $total;
    }

    private function actualizarEstadoOrden(CompraOrden $orden): void
    {
        if (in_array($orden->estado, ['cerrada_con_faltantes', 'recibida'], true)) {
            return;
        }

        $totalSolicitado = 0.0;
        $totalRecibido = 0.0;
        foreach ($orden->detalles as $detalle) {
            $totalSolicitado += (float)$detalle->cantidad_solicitada;
            $totalRecibido += (float) DB::table('compras_recepciones_detalle')
                ->join('compras_recepciones', 'compras_recepciones.id', '=', 'compras_recepciones_detalle.recepcion_id')
                ->where('compras_recepciones.orden_id', $orden->id)
                ->where('compras_recepciones_detalle.orden_detalle_id', $detalle->id)
                ->sum('compras_recepciones_detalle.cantidad_recibida');
        }

        $estado = $totalRecibido <= 0 ? 'pendiente'
            : ($totalRecibido + 0.0001 >= $totalSolicitado ? 'recibida' : 'parcial');

        $orden->update(['estado' => $estado, 'actualizado_por' => auth()->id()]);
    }
}
