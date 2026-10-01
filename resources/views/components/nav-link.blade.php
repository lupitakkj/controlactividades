@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-3 pt-1 border-b-2 border-blue-500 text-sm font-semibold leading-5 text-white bg-white/5 rounded-t-md focus:outline-none transition duration-200'
            : 'inline-flex items-center px-3 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-slate-400 hover:text-white hover:bg-white/5 rounded-t-md focus:outline-none transition duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>