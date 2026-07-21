@props(['disabled' => false])

<input @disabled($disabled)
    {{ $attributes->merge([
        'class' => 'border border-slate-700 bg-slate-950 text-white rounded-xl shadow-sm focus:border-blue-500 focus:ring-blue-500 placeholder-slate-500 transition duration-200'
    ]) }}>