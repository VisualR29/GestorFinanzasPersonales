<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Movimiento #{{ $transaction->id }}</h2>
            <div class="space-x-3">
                <a href="{{ route('movimientos.edit', $transaction) }}" class="text-indigo-600 hover:underline text-sm">Editar</a>
                <a href="{{ route('movimientos.index') }}" class="text-gray-600 hover:underline text-sm">Volver</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Fecha</dt>
                        <dd class="font-medium text-gray-900">{{ $transaction->occurred_on->format('Y-m-d') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Monto</dt>
                        <dd class="font-medium text-gray-900">{{ number_format($transaction->amount, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Cuenta</dt>
                        <dd class="font-medium text-gray-900">{{ $transaction->account?->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Categoría</dt>
                        <dd class="font-medium text-gray-900">
                            {{ $transaction->category?->name }}
                            <span class="block text-xs font-normal text-gray-600 mt-0.5">
                                {{ $transaction->category?->typeLabel() }}
                                @if($transaction->category?->kindLabel())
                                    — {{ $transaction->category->kindLabel() }}
                                @endif
                            </span>
                        </dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500">Descripción</dt>
                        <dd class="text-gray-900">{{ $transaction->description ?: '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</x-app-layout>
