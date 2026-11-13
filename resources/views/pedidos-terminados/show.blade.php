<x-app-layout>
    <div class="mx-auto max-w-7xl space-y-6 p-4 sm:p-6 text-slate-900">
        <header class="flex flex-col gap-4 rounded-xl border border-slate-700 bg-slate-800 p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div class="flex min-w-0 items-center gap-3">
                <a href="{{ route('pedidos.terminados') }}" aria-label="Volver al historial de producción" title="Volver al historial" class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-600 bg-slate-700 text-white transition hover:border-blue-400 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div class="min-w-0">
                    <h1 class="break-words text-2xl font-bold text-white">Detalle del pedido {{ $pedido->pedido_no }}</h1>
                    <p class="mt-1 text-sm text-slate-300">Historial de fabricación y procesos registrados</p>
                </div>
            </div>
            <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-800">Terminado · {{ number_format(($pedido->avance ?? 0) * 100, 1) }}%</span>
        </header>

        <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <div class="mb-4 flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-slate-900">Información general</h2>
                <span class="rounded-lg bg-green-50 px-3 py-1 text-sm font-bold text-green-700">Avance 100%</span>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div><p class="text-xs font-medium uppercase tracking-wide text-slate-500">Pedido</p><p class="mt-1 font-semibold text-slate-900">{{ $pedido->pedido_no }}</p></div>
                <div><p class="text-xs font-medium uppercase tracking-wide text-slate-500">Cliente</p><p class="mt-1 text-slate-900">{{ $pedido->cliente ?: '—' }}</p></div>
                <div><p class="text-xs font-medium uppercase tracking-wide text-slate-500">Su pedido</p><p class="mt-1 text-slate-900">{{ $pedido->su_pedido ?: '—' }}</p></div>
                <div><p class="text-xs font-medium uppercase tracking-wide text-slate-500">Fecha de solicitud</p><p class="mt-1 text-slate-900">{{ $pedido->fecha_solicitud?->format('d/m/Y') ?? '—' }}</p></div>
                <div><p class="text-xs font-medium uppercase tracking-wide text-slate-500">Fecha de entrega</p><p class="mt-1 text-slate-900">{{ $pedido->fecha_entrega?->format('d/m/Y') ?? '—' }}</p></div>
                <div><p class="text-xs font-medium uppercase tracking-wide text-slate-500">Fecha de término</p><p class="mt-1 text-slate-900">{{ $pedido->fecha_terminado?->format('d/m/Y') ?? '—' }}</p></div>
                <div><p class="text-xs font-medium uppercase tracking-wide text-slate-500">Partidas</p><p class="mt-1 font-semibold text-slate-900">{{ $pedido->partidas->count() }}</p></div>
                <div><p class="text-xs font-medium uppercase tracking-wide text-slate-500">Piezas solicitadas</p><p class="mt-1 font-semibold text-slate-900">{{ number_format((float) $pedido->partidas->sum('cantidad'), 0) }}</p></div>
                @if($pedido->importe_total !== null)
                    <div><p class="text-xs font-medium uppercase tracking-wide text-slate-500">Importe total</p><p class="mt-1 font-semibold text-slate-900">${{ number_format((float) $pedido->importe_total, 2) }}</p></div>
                @endif
            </div>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-slate-900">Avance general de fabricación</h2>
                <span class="text-lg font-bold text-green-700">{{ number_format(($pedido->avance ?? 0) * 100, 1) }}%</span>
            </div>
            <div class="h-3 overflow-hidden rounded-full bg-slate-100">
                <div class="h-full rounded-full bg-green-600" style="width: {{ min(100, max(0, ($pedido->avance ?? 0) * 100)) }}%"></div>
            </div>
            <p class="mt-2 text-sm text-slate-500">El porcentaje general utiliza el cálculo del modelo Pedido, ponderado por la cantidad de piezas de cada partida.</p>
        </section>

        <section class="space-y-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Partidas y procesos</h2>
                    <p class="mt-1 text-sm text-slate-500">Abre una partida para consultar su despiece y procesos.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" id="btnExpandirPartidas" class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-800 transition hover:bg-blue-100">
                        Desplegar todas
                    </button>
                    <button type="button" id="btnContraerPartidas" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Contraer todas
                    </button>
                </div>
            </div>

            @forelse($pedido->partidas as $partida)
                @php
                    $despieces = $partida->despieceProcesos->keyBy('proceso_id');
                    $sumaPorcentajes = 0;
                    $cantidadProcesosAplicables = 0;
                    $todosLosProcesosResueltos = true;

                    foreach ($procesosActivos as $procesoCalculo) {
                        $registroCalculo = $despieces->get($procesoCalculo->id);
                        if (!$registroCalculo) {
                            $todosLosProcesosResueltos = false;
                            $cantidadProcesosAplicables++;
                            continue;
                        }
                        if (!$registroCalculo->aplica) {
                            continue;
                        }
                        if ($registroCalculo->cantidad_realizada === null) {
                            $todosLosProcesosResueltos = false;
                        }
                        $sumaPorcentajes += max(0, min(1, (float) $registroCalculo->porcentaje));
                        $cantidadProcesosAplicables++;
                    }

                    $avancePartida = $cantidadProcesosAplicables > 0
                        ? $sumaPorcentajes / $cantidadProcesosAplicables
                        : 0;
                    if (!$todosLosProcesosResueltos && $avancePartida >= 1) {
                        $avancePartida = 0.9999;
                    }
                    $avancePartida = max(0, min(1, $avancePartida));
                @endphp

                <details class="partida-acordeon group overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                    <summary class="partida-summary list-none bg-slate-50 px-4 py-3 transition hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-blue-500 [&::-webkit-details-marker]:hidden">
                        <div class="partida-descripcion">
                            <h3 class="break-words font-semibold text-slate-900">Partida {{ $loop->iteration }}{{ $partida->clave ? ' · '.$partida->clave : '' }}</h3>
                            <p class="mt-1 break-words text-sm text-slate-600">{{ $partida->descripcion ?: 'Sin descripción' }}</p>
                            @if($partida->linea)<p class="mt-1 text-xs text-slate-500">Línea: {{ $partida->linea }}</p>@endif
                        </div>
                        <div class="partida-indicadores">
                            <div class="rounded-lg border border-slate-200 bg-white px-3 py-2"><p class="text-xs text-slate-500">Cantidad de piezas</p><p class="mt-1 font-bold text-slate-900">{{ number_format((float) $partida->cantidad, 0) }}</p></div>
                            <div class="rounded-lg border border-slate-200 bg-white px-3 py-2"><p class="text-xs text-slate-500">Avance de partida</p><p class="mt-1 font-bold text-green-700">{{ number_format($avancePartida * 100, 1) }}%</p></div>
                        </div>
                        <span class="flex shrink-0 items-center justify-start gap-2 text-xs font-semibold text-slate-500 sm:w-28 sm:flex-none sm:justify-end">
                            <span class="texto-desplegar">Ver procesos</span>
                            <svg class="h-5 w-5 transition-transform duration-200 group-open:rotate-180" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="m6 9 6 6 6-6"/>
                            </svg>
                        </span>
                    </summary>

                    <div class="border-t border-slate-200 p-4">
                        <div class="mb-3 flex items-center justify-between gap-3"><h4 class="font-semibold text-slate-800">Despiece y procesos</h4><span class="text-xs text-slate-500">{{ $procesosActivos->count() }} procesos activos</span></div>
                        <div class="overflow-x-auto rounded-lg border border-slate-200">
                            <table class="min-w-full text-sm">
                                <thead class="bg-slate-100 text-left text-slate-700"><tr><th class="p-3 font-semibold">Proceso</th><th class="p-3 font-semibold">Aplicación</th><th class="p-3 font-semibold">Piezas realizadas</th><th class="p-3 font-semibold">Avance</th><th class="p-3 font-semibold">Estado</th></tr></thead>
                                <tbody>
                                    @forelse($procesosActivos as $proceso)
                                        @php
                                            $registro = $despieces->get($proceso->id);
                                            $noAplica = $registro && !$registro->aplica;
                                            $porcentajeProceso = $registro && $registro->aplica
                                                ? max(0, min(1, (float) $registro->porcentaje)) * 100
                                                : 0;
                                            $cantidadRealizada = $registro?->cantidad_realizada;
                                            $procesoResuelto = $registro && ($noAplica || $cantidadRealizada !== null);
                                            $procesoCompleto = $noAplica || ($registro && $porcentajeProceso >= 100 && $cantidadRealizada !== null);
                                        @endphp
                                        <tr class="border-t border-slate-200">
                                            <td class="p-3 font-medium text-slate-900">{{ $proceso->nombre }}</td>
                                            <td class="p-3">
                                                @if(!$registro)<span class="rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-800">Por definir</span>
                                                @elseif($noAplica)<span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">No aplica</span>
                                                @else<span class="rounded-full bg-blue-50 px-2 py-1 text-xs font-semibold text-blue-800">Aplica</span>@endif
                                            </td>
                                            <td class="p-3 tabular-nums">
                                                @if($noAplica) —
                                                @elseif($registro) {{ $cantidadRealizada !== null ? number_format((float) $cantidadRealizada, 2) : 'Sin registrar' }} <span class="text-slate-500">/ {{ number_format((float) $partida->cantidad, 2) }}</span>
                                                @else —
                                                @endif
                                            </td>
                                            <td class="p-3">
                                                @if($noAplica) —
                                                @else
                                                    <div class="flex min-w-28 items-center gap-2"><div class="h-2 flex-1 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full {{ $porcentajeProceso >= 100 ? 'bg-green-600' : 'bg-blue-600' }}" style="width: {{ $porcentajeProceso }}%"></div></div><span class="w-12 text-right text-xs font-semibold tabular-nums">{{ number_format($porcentajeProceso, 1) }}%</span></div>
                                                @endif
                                            </td>
                                            <td class="p-3">
                                                @if($noAplica)<span class="text-xs font-medium text-slate-500">No aplica</span>
                                                @elseif(!$registro)<span class="text-xs font-semibold text-amber-700">Sin registro</span>
                                                @elseif($procesoCompleto)<span class="text-xs font-semibold text-green-700">Completo</span>
                                                @elseif(!$procesoResuelto)<span class="text-xs font-semibold text-amber-700">Pendiente de cantidad</span>
                                                @else<span class="text-xs font-semibold text-blue-700">En proceso</span>@endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="p-5 text-center text-slate-500">No hay procesos activos configurados.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </details>
            @empty
                <div class="rounded-xl border border-slate-200 bg-white p-8 text-center text-slate-500">Este pedido no tiene partidas registradas.</div>
            @endforelse
        </section>

        <div class="flex justify-start"><a href="{{ route('pedidos.terminados') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>Volver al historial</a></div>
    </div>

    <style>

        .partida-acordeon > summary::-webkit-details-marker { display: none; }
        .partida-acordeon > summary { list-style: none; }
        .partida-summary {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            align-items: center;
            gap: 12px;
        }
        .partida-descripcion {
            min-width: 0;
            width: 100%;
            overflow-wrap: anywhere;
        }
        .partida-indicadores {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
            width: 100%;
            min-width: 0;
        }
        .partida-toggle {
            justify-self: start;
            min-width: 0;
        }
        @media (min-width: 768px) {
            .partida-summary {
                grid-template-columns: minmax(0, 1fr) 272px 112px;
                gap: 16px;
            }
            .partida-indicadores {
                width: 272px;
            }
            .partida-toggle {
                width: 112px;
                justify-self: end;
                justify-content: flex-end;
            }
        }
        .partida-acordeon[open] .texto-desplegar { font-size: 0; }
        .partida-acordeon[open] .texto-desplegar::before {
            content: "Ocultar procesos";
            font-size: 0.75rem;
        }
        </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const partidas = Array.from(document.querySelectorAll('.partida-acordeon'));
            const btnExpandir = document.getElementById('btnExpandirPartidas');
            const btnContraer = document.getElementById('btnContraerPartidas');

            if (btnExpandir) {
                btnExpandir.addEventListener('click', function () {
                    partidas.forEach(partida => partida.open = true);
                });
            }

            if (btnContraer) {
                btnContraer.addEventListener('click', function () {
                    partidas.forEach(partida => partida.open = false);
                });
            }
        });
    </script>
</x-app-layout>
