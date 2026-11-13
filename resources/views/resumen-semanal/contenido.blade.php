<p class="mb-5 text-sm text-slate-600">
    Semana {{ $semana->semana }} · {{ $semana->fecha_inicio->format('d/m/Y') }} al {{ $semana->fecha_fin->format('d/m/Y') }}
</p>

<div class="grid gap-4 sm:grid-cols-2">
    <section class="rounded-lg border border-slate-200 bg-slate-50 p-4">
        <h3 class="font-semibold text-slate-900">Cierre de la semana anterior</h3>
        @if ($anterior)
            <p class="mt-1 text-xs text-slate-600">
                {{ $anterior->fecha_inicio->format('d/m/Y') }} al {{ $anterior->fecha_fin->format('d/m/Y') }}
            </p>
            <p class="mt-3 text-xl font-semibold text-emerald-700">${{ number_format($cierre->importe, 2) }}</p>
            <p class="text-sm text-slate-700">Pedidos terminados · {{ $cierre->terminados }}</p>
        @else
            <p class="mt-3 text-sm text-slate-600">No hay una semana anterior configurada.</p>
        @endif
    </section>

    <section class="rounded-lg border border-slate-200 bg-slate-50 p-4">
        <h3 class="font-semibold text-slate-900">Pendientes de esta semana</h3>
        <p class="mt-3 text-xl font-semibold text-blue-700">{{ $pendientes }}</p>
        <p class="text-sm text-slate-700">Pedidos pendientes de terminar</p>
        <p class="mt-2 text-sm text-slate-600">De semanas anteriores · {{ $rezago }}</p>
    </section>
</div>
