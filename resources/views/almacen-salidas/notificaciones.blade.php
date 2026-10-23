<x-app-layout>
    <div class="max-w-5xl mx-auto p-6 space-y-4">
        <a class="text-blue-700 underline" href="{{ route('almacen-salidas.index') }}">← Volver a salidas</a>
        <h1 class="text-2xl font-bold">Notificaciones de almacén</h1>
        @foreach($notificaciones as $notificacion)
            <a href="{{ route('almacen-salidas.notificaciones.leer', $notificacion) }}" class="block rounded-xl border p-4 {{ $notificacion->leida_en ? 'bg-white' : 'bg-blue-50 border-blue-300' }}">
                <div class="flex justify-between gap-3"><strong>{{ $notificacion->titulo }}</strong><span class="text-xs text-slate-500">{{ $notificacion->created_at?->format('d/m/Y H:i') }}</span></div>
                <p class="mt-1 text-sm">{{ $notificacion->mensaje }}</p>
                @if(!$notificacion->leida_en)<span class="mt-2 inline-block text-xs font-semibold text-blue-700">No leída · Abrir</span>@endif
            </a>
        @endforeach
        {{ $notificaciones->links() }}
    </div>
</x-app-layout>
