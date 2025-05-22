<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!--Boton para desvincular cuenta MP -->
    <div class="p-6 bg-white shadow-md rounded mb-4">
    @if(auth()->user()->mp_access_token)
        {{-- ✅ Botón para Desvincular cuenta --}}
        <form action="{{ route('mercadopago.unlink') }}" method="GET" onsubmit="return confirm('¿Estás seguro que deseas desvincular tu cuenta de Mercado Pago?')">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                Desvincular Mercado Pago
            </button>
        </form>
    @else
        {{-- ✅ Botón para Vincular cuenta --}}
        <form action="{{ route('mercadopago.connect') }}" method="GET">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Vincular Mercado Pago
            </button>
        </form>
    @endif
</div>




    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
