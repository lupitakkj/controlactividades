<x-app-layout>
    <div class="mx-auto max-w-5xl space-y-5 p-4 sm:p-6 text-slate-900">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
            <div class="flex min-w-0 items-center gap-3">
                <a href="{{ route('almacen-salidas.index') }}"
                   aria-label="Volver a salidas"
                   title="Volver a salidas"
                   class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-600 bg-slate-800 text-white shadow-sm transition hover:border-blue-400 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>

                <div class="min-w-0">
                    <h1 class="break-words text-2xl font-bold text-white">Salida {{ $salida->folio }}</h1>
                    <p class="mt-1 text-sm text-slate-400">Detalle de salida de almacén</p>
                </div>
            </div>

            @php
                $estadoClase = match($salida->estado) {
                    'apartada' => 'bg-amber-100 text-amber-900',
                    'registrada' => 'bg-green-100 text-green-800',
                    'cancelada' => 'bg-red-100 text-red-800',
                    default => 'bg-slate-100 text-slate-800',
                };
                $estadoTexto = match($salida->estado) {
                    'apartada' => 'Apartada',
                    'registrada' => 'Registrada',
                    'cancelada' => 'Cancelada',
                    default => ucfirst($salida->estado),
                };
            @endphp
            <span class="inline-flex shrink-0 items-center rounded-full px-3 py-1 text-sm font-semibold {{ $estadoClase }}">
                {{ $estadoTexto }}
            </span>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-3 text-green-800">{{ session('success') }}</div>
        @endif
        @if($salida->estado === 'apartada' && auth()->user()->hasRole('Almacén'))
            <section class="rounded-xl border border-amber-300 bg-amber-50 p-4 sm:p-5">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h2 class="font-semibold text-amber-950">Material apartado, pendiente de entrega</h2>
                        <p class="mt-1 text-sm text-amber-900">Las cantidades están reservadas y no pueden utilizarse en otra salida. Cuando entregues físicamente el material, confirma la entrega.</p>
                    </div>
                    <form method="POST" action="{{ route('almacen-salidas.entregar', $salida) }}" onsubmit="return confirm('¿Confirmas que el material ya fue entregado físicamente? Esta acción registrará la salida y descontará el inventario.');">
                        @csrf
                        <button type="submit" class="rounded-lg bg-green-700 px-4 py-2 font-semibold text-white transition hover:bg-green-800">Confirmar entrega y registrar salida</button>
                    </form>
                </div>
            </section>
        @elseif($salida->estado === 'apartada')
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900">
                Este material está apartado y pendiente de que Almacén confirme la entrega.
            </div>
        @endif
        @if($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-red-800">
                <ul class="list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        @php
            // Las referencias pueden estar guardadas como JSON o como texto simple.
            $referenciasManuales = $salida->referencias_manuales ?? [];
            if (is_string($referenciasManuales)) {
                $decodificadas = json_decode($referenciasManuales, true);
                $referenciasManuales = json_last_error() === JSON_ERROR_NONE
                    ? $decodificadas
                    : (trim($referenciasManuales) !== '' ? [trim($referenciasManuales)] : []);
            }
            if (!is_array($referenciasManuales)) {
                $referenciasManuales = [];
            }
            // Acepta referencias guardadas como cadenas o como objetos numero/op + descripcion.
            $referenciasManuales = array_values(array_filter($referenciasManuales, function ($referencia) {
                if (is_string($referencia)) return trim($referencia) !== '';
                if (is_array($referencia)) return trim((string)($referencia['numero'] ?? $referencia['op'] ?? $referencia['nombre'] ?? '')) !== '';
                return false;
            }));
            $pedidosRelacionados = $salida->pedidos ?? collect();
            $pedidoPrincipal = $salida->pedido ?? null;
            if ($pedidosRelacionados->isEmpty() && $pedidoPrincipal) {
                $pedidosRelacionados = collect([$pedidoPrincipal]);
            }
        @endphp

        <section class="grid gap-4 rounded-xl border border-slate-200 bg-white p-4 sm:p-5 md:grid-cols-2">
            <p class="md:col-span-2 text-slate-800"><strong>Nombre del documento:</strong> {{ $salida->nombre_documento ?: '—' }}</p>

            <div class="md:col-span-2">
                <h2 class="mb-3 font-semibold text-slate-900">Órdenes de trabajo / pedidos registrados</h2>
                @if($pedidosRelacionados->isNotEmpty() || count($referenciasManuales))
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($pedidosRelacionados as $pedido)
                            @php
                                $numeroPedido = trim((string)($pedido->pedido_no ?? ''));
                                $numeroVisible = str_starts_with(strtoupper($numeroPedido), 'OP')
                                    ? 'OP' . ltrim(substr($numeroPedido, 2), '0')
                                    : 'OP' . ltrim($numeroPedido, '0');
                                if ($numeroVisible === 'OP') $numeroVisible = 'OP0';
                            @endphp
                            <div class="min-w-0 rounded-lg border border-slate-200 bg-slate-50 p-3">
                                <div class="break-words font-semibold text-slate-900">{{ $numeroVisible }}</div>
                                <div class="mt-1 break-words text-sm text-slate-600">{{ $pedido->cliente ?: 'Sin descripción de cliente' }}</div>
                                <div class="mt-2 text-xs font-medium text-slate-500">Orden registrada</div>
                            </div>
                        @endforeach

                        @foreach($referenciasManuales as $referencia)
                            @php
                                if (is_array($referencia)) {
                                    $numeroManual = trim((string)($referencia['numero'] ?? $referencia['op'] ?? $referencia['nombre'] ?? 'Referencia manual'));
                                    $descripcionManual = trim((string)($referencia['descripcion'] ?? $referencia['cliente'] ?? ''));
                                } else {
                                    $numeroManual = trim((string)$referencia);
                                    $descripcionManual = '';
                                }
                            @endphp
                            <div class="min-w-0 rounded-lg border border-blue-200 bg-blue-50 p-3">
                                <div class="break-words font-semibold text-blue-900">{{ $numeroManual }}</div>
                                @if($descripcionManual !== '')
                                    <div class="mt-1 break-words text-sm text-blue-800">{{ $descripcionManual }}</div>
                                @endif
                                <div class="mt-2 text-xs font-medium text-blue-700">Referencia manual</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-slate-500">No hay órdenes ni referencias manuales asociadas.</p>
                @endif
            </div>

            <p class="text-slate-800"><strong>Almacén:</strong> {{ $salida->almacen->nombre ?? '—' }}</p>
            <p class="text-slate-800"><strong>Fecha:</strong> {{ $salida->fecha_salida?->format('d/m/Y H:i') ?? '—' }}</p>
            <p class="text-slate-800"><strong>Recibe:</strong> {{ $salida->recibido_por ?: '—' }}</p>
            <p class="text-slate-800"><strong>Registró:</strong> {{ $salida->creador->name ?? '—' }}</p>
            @if($salida->observaciones)
                <p class="md:col-span-2 break-words text-slate-800"><strong>Observaciones:</strong> {{ $salida->observaciones }}</p>
            @endif
        </section>

        <section class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
            <table class="min-w-full text-sm text-slate-800">
                <thead class="bg-slate-100 text-left text-slate-900">
                    <tr>
                        <th class="p-3 font-semibold">Clave</th>
                        <th class="p-3 font-semibold">Descripción</th>
                        <th class="p-3 font-semibold">Unidad</th>
                        <th class="p-3 font-semibold">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salida->detalles as $detalle)
                        <tr class="border-t border-slate-200">
                            <td class="p-3">{{ $detalle->clave_material }}</td>
                            <td class="p-3">{{ $detalle->descripcion }}</td>
                            <td class="p-3">{{ $detalle->unidad ?: '—' }}</td>
                            <td class="p-3 tabular-nums">{{ number_format((float)$detalle->cantidad, 3) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-4 text-center text-slate-500">No hay materiales registrados en esta salida.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        @if(in_array($salida->estado, ['apartada', 'registrada'], true) && ! $salida->solicitudesCancelacion->contains(fn($s) => $s->estado === 'pendiente') && auth()->user()->hasAnyRole(['Almacén','Compras','Administrador']))
            <section class="rounded-xl border border-amber-300 bg-amber-50 p-4 sm:p-5">
                <h2 class="font-semibold text-amber-950">Solicitar cancelación</h2>
                <p class="mb-3 mt-1 text-sm text-slate-700">Escribe el motivo. La salida seguirá vigente hasta que Compras autorice.</p>
                <form method="POST" action="{{ route('almacen-salidas.cancelaciones.solicitar', $salida) }}" class="space-y-3">
                    @csrf
                    <textarea name="motivo" required minlength="5" maxlength="3000" rows="3" class="w-full rounded-lg border border-slate-300 bg-white text-slate-900 placeholder:text-slate-500 focus:border-amber-500 focus:ring-amber-500" placeholder="Motivo obligatorio de cancelación"></textarea>
                    <button type="submit" class="rounded-lg bg-amber-700 px-4 py-2 font-medium text-white hover:bg-amber-800" onclick="return confirm('¿Enviar solicitud a Compras?')">Enviar solicitud</button>
                </form>
            </section>
        @endif

        @if($salida->solicitudesCancelacion->isNotEmpty())
            <section class="space-y-3 rounded-xl border border-slate-200 bg-white p-4 sm:p-5">
                <h2 class="font-semibold text-slate-900">Historial de cancelación</h2>
                @foreach($salida->solicitudesCancelacion as $s)
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-slate-800">
                        <p><strong>Estado:</strong> {{ ucfirst($s->estado) }}</p>
                        <p><strong>Solicitó:</strong> {{ $s->solicitante->name ?? '—' }} · {{ $s->created_at?->format('d/m/Y H:i') }}</p>
                        <p class="break-words"><strong>Motivo:</strong> {{ $s->motivo }}</p>
                        @if($s->resolver)<p><strong>Resolvió:</strong> {{ $s->resolver->name }} · {{ $s->resuelto_en?->format('d/m/Y H:i') }}</p>@endif
                        @if($s->respuesta_compras)<p class="break-words"><strong>Respuesta:</strong> {{ $s->respuesta_compras }}</p>@endif
                        @if($s->estado === 'pendiente' && auth()->user()->hasAnyRole(['Compras','Administrador']) && $s->solicitado_por !== auth()->id())
                            <form method="POST" action="{{ route('almacen-salidas.cancelaciones.resolver', $s) }}" class="mt-3 flex flex-wrap gap-2">
                                @csrf
                                <input name="respuesta_compras" class="min-w-48 rounded-lg border border-slate-300 bg-white text-slate-900 placeholder:text-slate-500" placeholder="Motivo si rechazas">
                                <button name="decision" value="autorizar" class="rounded-lg bg-green-700 px-3 py-2 font-medium text-white hover:bg-green-800" onclick="return confirm('¿Autorizar y cancelar la salida?')">Autorizar</button>
                                <button name="decision" value="rechazar" class="rounded-lg bg-red-700 px-3 py-2 font-medium text-white hover:bg-red-800" onclick="return confirm('¿Rechazar la solicitud?')">Rechazar</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </section>
        @endif
    </div>
</x-app-layout>
