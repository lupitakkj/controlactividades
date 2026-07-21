<button {{ $attributes->merge([
'class' => 'w-full flex justify-center items-center py-4 px-6 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 rounded-xl text-white font-semibold text-lg shadow-lg shadow-blue-600/30 transition duration-300'
]) }}>
    {{ $slot }}
</button>