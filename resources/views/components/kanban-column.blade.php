@props([
    'id',
    'title',
    'icon',
    'actividades'
])

<div id="{{ $id }}" class="kanban-column">

    {{-- Encabezado --}}
    <div class="kanban-header">

        <h2 class="kanban-title">
            <i class="fa-solid {{ $icon }}"></i>
            <span>{{ $title }}</span>
        </h2>

        <p class="kanban-count">
            {{ $actividades->count() }}
            {{ Str::plural('actividad', $actividades->count()) }}
        </p>

    </div>

    {{-- Contenido --}}
    <div class="kanban-body">

        @forelse($actividades as $actividad)

            <x-kanban-card
                :actividad="$actividad"
                :estado="$id" />

        @empty

            <div class="kanban-empty">

                <i class="fa-regular fa-clipboard"></i>

                <p>No hay actividades</p>

                <span>
                    en {{ strtolower($title) }}
                </span>

            </div>

        @endforelse

    </div>

</div>