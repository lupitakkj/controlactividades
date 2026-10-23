<x-app-layout>
    <div class="max-w-5xl mx-auto p-6 space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div><a class="text-blue-700 underline" href="{{ route('almacen-salidas.index') }}">← Volver</a><h1 class="mt-2 text-2xl font-bold">Salida {{ $salida->folio }}</h1></div>
            <span class="rounded-full px-3 py-1 {{ $salida->estado === 'cancelada' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">{{ ucfirst($salida->estado) }}</span>
        </div>
        @if(session('success')) <div class="rounded-lg bg-green-50 p-3 text-green-800">{{ session('success') }}</div> @endif
        @if($errors->any()) <div class="rounded-lg bg-red-50 p-3 text-red-800"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif
        <section class="grid gap-3 rounded-xl border bg-white p-5 md:grid-cols-2">
            <div class="md:col-span-2"><strong>Órdenes de trabajo / pedidos:</strong>
                @if($salida->pedidos->isNotEmpty())
                    <ul class="mt-1 list-disc pl-6">@foreach($salida->pedidos as $pedido)<li>{{ $pedido->pedido_no }} — {{ $pedido->cliente }}</li>@endforeach</ul>
                @else
                    {{ $salida->pedido->pedido_no ?? '—' }} — {{ $salida->pedido->cliente ?? '—' }}
                @endif
            </div>
            <p><strong>Almacén:</strong> {{ $salida->almacen->nombre ?? '—' }}</p><p><strong>Fecha:</strong> {{ $salida->fecha_salida?->format('d/m/Y H:i') }}</p>
            <p><strong>Recibe:</strong> {{ $salida->recibido_por }}</p><p><strong>Registró:</strong> {{ $salida->creador->name ?? '—' }}</p>
            @if($salida->observaciones)<p class="md:col-span-2"><strong>Observaciones:</strong> {{ $salida->observaciones }}</p>@endif
        </section>
        <section class="overflow-x-auto rounded-xl border bg-white">
            <table class="min-w-full text-sm"><thead class="bg-slate-100 text-left"><tr><th class="p-3">Clave</th><th class="p-3">Descripción</th><th class="p-3">Unidad</th><th class="p-3">Cantidad</th></tr></thead><tbody>
            @foreach($salida->detalles as $detalle)<tr class="border-t"><td class="p-3">{{ $detalle->clave_material }}</td><td class="p-3">{{ $detalle->descripcion }}</td><td class="p-3">{{ $detalle->unidad }}</td><td class="p-3">{{ number_format((float)$detalle->cantidad, 3) }}</td></tr>@endforeach
            </tbody></table>
        </section>

        @if($salida->estado === 'registrada' && ! $salida->solicitudesCancelacion->contains(fn($s) => $s->estado === 'pendiente') && auth()->user()->hasAnyRole(['Almacén','Compras','Administrador']))
            <section class="rounded-xl border border-amber-300 bg-amber-50 p-5">
                <h2 class="font-semibold">Solicitar cancelación</h2>
                <p class="text-sm text-slate-600 mb-3">Escribe el motivo. La salida seguirá vigente hasta que Compras autorice.</p>
                <form method="POST" action="{{ route('almacen-salidas.cancelaciones.solicitar', $salida) }}" class="space-y-3">
                    @csrf
                    <textarea name="motivo" required minlength="5" maxlength="3000" rows="3" class="w-full rounded-lg border-slate-300" placeholder="Motivo obligatorio de cancelación"></textarea>
                    <button class="rounded-lg bg-amber-700 px-4 py-2 text-white" onclick="return confirm('¿Enviar solicitud a Compras?')">Enviar solicitud</button>
                </form>
            </section>
        @endif

        @if($salida->solicitudesCancelacion->isNotEmpty())
            <section class="rounded-xl border bg-white p-5 space-y-3"><h2 class="font-semibold">Historial de cancelación</h2>
            @foreach($salida->solicitudesCancelacion as $s)
                <div class="rounded-lg border p-3">
                    <p><strong>Estado:</strong> {{ ucfirst($s->estado) }}</p>
                    <p><strong>Solicitó:</strong> {{ $s->solicitante->name ?? '—' }} · {{ $s->created_at?->format('d/m/Y H:i') }}</p>
                    <p><strong>Motivo:</strong> {{ $s->motivo }}</p>
                    @if($s->resolver)<p><strong>Resolvió:</strong> {{ $s->resolver->name }} · {{ $s->resuelto_en?->format('d/m/Y H:i') }}</p>@endif
                    @if($s->respuesta_compras)<p><strong>Respuesta:</strong> {{ $s->respuesta_compras }}</p>@endif
                    @if($s->estado === 'pendiente' && auth()->user()->hasAnyRole(['Compras','Administrador']) && $s->solicitado_por !== auth()->id())
                        <form method="POST" action="{{ route('almacen-salidas.cancelaciones.resolver', $s) }}" class="mt-3 flex flex-wrap gap-2">
                            @csrf
                            <input name="respuesta_compras" class="rounded-lg border-slate-300" placeholder="Motivo si rechazas">
                            <button name="decision" value="autorizar" class="rounded-lg bg-green-700 px-3 py-2 text-white" onclick="return confirm('¿Autorizar y cancelar la salida?')">Autorizar</button>
                            <button name="decision" value="rechazar" class="rounded-lg bg-red-700 px-3 py-2 text-white" onclick="return confirm('¿Rechazar la solicitud?')">Rechazar</button>
                        </form>
                    @endif
                </div>
            @endforeach
            </section>
        @endif
    </div>
</x-app-layout>
