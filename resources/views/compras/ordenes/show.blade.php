<x-app-layout>
<div class="py-6"><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
    @if(session('success'))<div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <div class="rounded-xl border border-slate-700 bg-slate-800 p-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div><h1 class="text-2xl font-bold text-white"><i class="fa-solid fa-file-invoice mr-2"></i>Orden {{ $orden->folio }}</h1><p class="mt-1 text-sm text-slate-300">Recepciones de materiales</p></div>
        @if($orden->recepciones->isEmpty() && !in_array($orden->estado, ['recibida','cerrada_con_faltantes'], true))
            <a href="{{ route('compras.ordenes.edit', $orden) }}" class="inline-flex justify-center rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600"><i class="fa-solid fa-pen-to-square mr-2"></i>Editar orden</a>
        @endif
        <a href="{{ route('compras.ordenes.index') }}" class="inline-flex justify-center rounded-lg border border-slate-500 px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-700">Volver al listado</a>
    </div>

    @php
        $totalSolicitado = 0; $totalRecibido = 0;
        foreach ($orden->detalles as $d) { $totalSolicitado += (float)$d->cantidad_solicitada; $totalRecibido += (float)$d->recibido_calculado; }
        $avanceOrden = $totalSolicitado > 0 ? min(100, ($totalRecibido / $totalSolicitado) * 100) : 0;
    @endphp

    <section class="rounded-xl border border-slate-200 bg-white shadow-sm p-5">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div><div class="text-xs font-semibold uppercase text-slate-500">Proveedor</div><div class="mt-1 font-semibold">{{ $orden->proveedor->razon_social ?? '—' }}</div></div>
            <div><div class="text-xs font-semibold uppercase text-slate-500">Fecha de orden</div><div class="mt-1">{{ optional($orden->fecha_orden)->format('d/m/Y') ?? '—' }}</div></div>
            <div><div class="text-xs font-semibold uppercase text-slate-500">Estado</div><div class="mt-1 font-semibold">{{ ucfirst(str_replace('_',' ', $orden->estado)) }}</div></div>
            <div><div class="text-xs font-semibold uppercase text-slate-500">Creó la orden</div><div class="mt-1">{{ $orden->creador->name ?? '—' }}</div></div>
        </div>
        <div class="mt-5">
            <div class="flex justify-between text-sm font-semibold"><span>Avance de recepción</span><span>{{ number_format($avanceOrden, 1) }}%</span></div>
            <div class="mt-2 h-3 w-full overflow-hidden rounded-full" style="background-color: #e2e8f0;">
                <div style="height: 12px; width: {{ max(0, min(100, $avanceOrden)) }}%; background-color: #16a34a; border-radius: 9999px; display: block;"></div>
            </div>
            <p class="mt-2 text-sm text-slate-600">Recibido: {{ number_format($totalRecibido,3) }} de {{ number_format($totalSolicitado,3) }} piezas/unidades acumuladas.</p>
        </div>
        @if($orden->estado === 'cerrada_con_faltantes')
            <div class="mt-4 rounded-lg border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900">
                <strong>Orden cerrada con faltantes</strong>
                <p class="mt-1">Autorizó: {{ $orden->autorizadorCierre->name ?? 'Usuario no disponible' }} · {{ optional($orden->fecha_cierre_faltantes)->format('d/m/Y H:i') }}</p>
                <p class="mt-1">Justificación: {{ $orden->motivo_cierre_faltantes }}</p>
            </div>
        @endif
        @if($orden->pdf_path)<p class="mt-4 text-sm"><a target="_blank" rel="noopener" class="font-semibold text-blue-700 hover:underline" href="{{ asset($orden->pdf_path) }}">Ver PDF de la orden</a></p>@endif
        @if($orden->observaciones)<p class="mt-3 whitespace-pre-line text-sm text-slate-600">{{ $orden->observaciones }}</p>@endif
    </section>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b bg-slate-50 px-5 py-4"><h2 class="font-semibold text-slate-800">Productos solicitados y pendientes</h2><p class="mt-1 text-sm text-slate-500">El recibido se acumula a partir de todas las recepciones registradas.</p></div>
        <div class="overflow-x-auto"><table class="min-w-full text-sm"><thead class="bg-slate-100 text-slate-700"><tr><th class="px-4 py-3 text-left">Clave / descripción</th><th class="px-4 py-3 text-right">Solicitado</th><th class="px-4 py-3 text-right">Recibido</th><th class="px-4 py-3 text-right">Pendiente</th><th class="px-4 py-3 text-right">Avance</th></tr></thead><tbody class="divide-y divide-slate-200">
        @foreach($orden->detalles as $d)
            @php $sol=(float)$d->cantidad_solicitada; $rec=(float)$d->recibido_calculado; $pen=max(0,$sol-$rec); $por=$sol>0?min(100,$rec/$sol*100):0; @endphp
            <tr><td class="px-4 py-3"><div class="font-semibold text-slate-900">{{ $d->descripcion }}</div><div class="text-xs text-slate-500">{{ $d->clave_material ?: 'Sin clave' }} · {{ $d->unidad ?: 'Unidad no especificada' }}</div></td><td class="whitespace-nowrap px-4 py-3 text-right">{{ number_format($sol,3) }}</td><td class="whitespace-nowrap px-4 py-3 text-right font-semibold text-emerald-700">{{ number_format($rec,3) }}</td><td class="whitespace-nowrap px-4 py-3 text-right">{{ number_format($pen,3) }}</td><td class="px-4 py-3 min-w-40"><div class="text-right font-semibold">{{ number_format($por,1) }}%</div><div class="mt-1 h-2 w-full overflow-hidden rounded-full" style="background-color: #e2e8f0;">
                <div style="height: 8px; width: {{ max(0, min(100, $por)) }}%; background-color: #16a34a; border-radius: 9999px; display: block;"></div>
            </div></td></tr>
        @endforeach
        </tbody></table></div>
    </section>

    @if(!in_array($orden->estado, ['recibida','cerrada_con_faltantes'], true))
    <section class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b bg-slate-50 px-5 py-4"><h2 class="font-semibold text-slate-800">Registrar recepción</h2><p class="mt-1 text-sm text-slate-500">La evidencia es obligatoria. Puedes registrar una entrega parcial y completar la orden después.</p></div>
        <form method="POST" action="{{ route('compras.recepciones.store', $orden) }}" enctype="multipart/form-data" class="space-y-5 p-5">
            @csrf
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div><label class="mb-1 block text-sm font-medium text-slate-700">Fecha y hora de recepción</label><input type="datetime-local" name="fecha_recepcion" required value="{{ old('fecha_recepcion', now()->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border-slate-300 text-sm"><x-input-error :messages="$errors->get('fecha_recepcion')" class="mt-1" /></div>
                <div><label class="mb-1 block text-sm font-medium text-slate-700">Almacén donde se ingresó el material *</label><select name="almacen_id" required class="w-full rounded-lg border-slate-300 text-sm"><option value="">Selecciona un almacén</option>@foreach($almacenes as $almacen)<option value="{{ $almacen->id }}" @selected(old('almacen_id') == $almacen->id)>{{ $almacen->clave }} — {{ $almacen->nombre }}</option>@endforeach</select><x-input-error :messages="$errors->get('almacen_id')" class="mt-1" />@if($almacenes->isEmpty())<p class="mt-1 text-xs text-red-600">No hay almacenes activos. Registra o activa uno en el catálogo de Almacenes.</p>@endif</div>
                <div><label class="mb-1 block text-sm font-medium text-slate-700">Evidencia obligatoria (JPG, PNG, WEBP o PDF, máximo 10 MB)</label><input type="file" name="evidencia" required accept=".jpg,.jpeg,.png,.webp,.pdf" class="w-full rounded-lg border border-slate-300 p-2 text-sm"><x-input-error :messages="$errors->get('evidencia')" class="mt-1" /></div>
            </div>
            <div class="overflow-x-auto"><table class="min-w-full text-sm"><thead class="bg-slate-100"><tr><th class="px-3 py-2 text-left">Producto</th><th class="px-3 py-2 text-right">Pendiente</th><th class="px-3 py-2 text-right">Recibir ahora</th><th class="px-3 py-2 text-left">Observación</th></tr></thead><tbody class="divide-y">
                @foreach($orden->detalles as $d)
                @php $pendiente=max(0,(float)$d->cantidad_solicitada-(float)$d->recibido_calculado); @endphp
                <tr><td class="px-3 py-3"><div class="font-medium">{{ $d->descripcion }}</div><div class="text-xs text-slate-500">{{ $d->clave_material ?: '—' }}</div></td><td class="px-3 py-3 text-right">{{ number_format($pendiente,3) }}</td><td class="px-3 py-3"><input type="number" name="cantidades[{{ $d->id }}]" min="0" max="{{ $pendiente }}" step="0.001" value="{{ old('cantidades.'.$d->id, 0) }}" class="w-32 rounded-lg border-slate-300 text-right text-sm"><x-input-error :messages="$errors->get('cantidades.'.$d->id)" class="mt-1" /></td><td class="px-3 py-3"><input type="text" name="observaciones_detalle[{{ $d->id }}]" maxlength="1000" class="w-full min-w-48 rounded-lg border-slate-300 text-sm" placeholder="Opcional"></td></tr>
                @endforeach
            </tbody></table></div>
            <div><label class="mb-1 block text-sm font-medium text-slate-700">Observaciones de esta recepción</label><textarea name="observaciones" rows="3" maxlength="5000" class="w-full rounded-lg border-slate-300 text-sm">{{ old('observaciones') }}</textarea></div>
            <div class="flex justify-end"><button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Guardar recepción</button></div>
        </form>
    </section>

    @php $puedeCerrar = auth()->user()->hasAnyRole(['Administrador','Compras']) && $totalRecibido + 0.0001 < $totalSolicitado; @endphp
    @if($puedeCerrar)
    <section class="rounded-xl border border-amber-300 bg-amber-50 shadow-sm">
        <div class="border-b border-amber-200 px-5 py-4"><h2 class="font-semibold text-amber-900">Cerrar orden con faltantes</h2><p class="mt-1 text-sm text-amber-800">Solo Administrador y Compras. La justificación quedará registrada en la orden.</p></div>
        <form method="POST" action="{{ route('compras.recepciones.cerrar-faltantes', $orden) }}" class="space-y-4 p-5" onsubmit="return confirm('¿Confirmas cerrar esta orden con material pendiente?');">
            @csrf
            <div><label class="mb-1 block text-sm font-medium text-amber-900">Justificación obligatoria (mínimo 10 caracteres)</label><textarea name="motivo_cierre_faltantes" required minlength="10" maxlength="5000" rows="3" class="w-full rounded-lg border-amber-300 text-sm" placeholder="Explica por qué se acepta cerrar la orden sin recibir todo el material...">{{ old('motivo_cierre_faltantes') }}</textarea></div>
            <div class="flex justify-end"><button type="submit" class="rounded-lg bg-amber-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-800">Autorizar cierre con faltantes</button></div>
        </form>
    </section>
    @endif
    @endif

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b bg-slate-50 px-5 py-4"><h2 class="font-semibold text-slate-800">Historial de recepciones</h2></div>
        @forelse($orden->recepciones as $r)
            <div class="border-b p-5 last:border-b-0"><div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"><div><div class="font-semibold">Recepción #{{ $r->id }} · {{ optional($r->fecha_recepcion)->format('d/m/Y H:i') }}</div><div class="text-sm text-slate-500">Registró: {{ $r->recibidoPor->name ?? 'Usuario no disponible' }}</div><div class="mt-1 text-sm text-slate-700"><i class="fa-solid fa-warehouse mr-1"></i>Almacén: <strong>{{ $r->almacen->nombre ?? 'No asignado (recepción histórica)' }}</strong>@if($r->almacen?->clave)<span class="text-slate-500"> · {{ $r->almacen->clave }}</span>@endif</div></div><a href="{{ route('compras.recepciones.evidencia', $r) }}" class="inline-flex rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-slate-50">Descargar evidencia</a></div>
                @if($r->observaciones)<p class="mt-2 whitespace-pre-line text-sm text-slate-700">{{ $r->observaciones }}</p>@endif
                <div class="mt-3 overflow-x-auto"><table class="min-w-full text-sm"><thead><tr class="text-left text-slate-500"><th class="py-2 pr-4">Producto</th><th class="py-2 text-right">Cantidad recibida</th></tr></thead><tbody>@foreach($r->detalles as $rd)<tr><td class="py-1 pr-4">{{ $rd->ordenDetalle->descripcion ?? 'Producto eliminado' }}</td><td class="py-1 text-right">{{ number_format($rd->cantidad_recibida,3) }}</td></tr>@endforeach</tbody></table></div>
            </div>
        @empty
            <p class="p-5 text-sm text-slate-500">Todavía no hay recepciones registradas.</p>
        @endforelse
    </section>
</div></div>
</x-app-layout>
