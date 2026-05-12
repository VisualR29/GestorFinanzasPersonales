<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $account->name }}</h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('movimientos.create', ['cuenta' => $account->id]) }}"
                   class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    Movimiento
                </a>
                <a href="{{ route('movimientos.create', ['cuenta' => $account->id, 'tipo' => 'ingreso']) }}"
                   class="inline-flex items-center rounded-md bg-emerald-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500">
                    Ingreso
                </a>
                <a href="{{ route('cuentas.edit', $account) }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                    Editar
                </a>
                <a href="{{ route('cuentas.index') }}" class="text-sm text-gray-600 hover:text-gray-900 self-center px-2">Volver</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Tipo</dt>
                        <dd class="font-medium text-gray-900">{{ $account->typeLabel() }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Moneda</dt>
                        <dd class="font-medium text-gray-900">{{ $account->currency }}</dd>
                    </div>
                    @if(auth()->user()->isAdmin())
                        <div class="sm:col-span-2">
                            <dt class="text-gray-500">Usuario</dt>
                            <dd class="font-medium text-gray-900">{{ $account->user?->name }} — {{ $account->user?->email }}</dd>
                        </div>
                    @endif
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500">Notas</dt>
                        <dd class="text-gray-900">{{ $account->notes ?: '—' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Movimientos recientes</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead>
                                <tr class="text-left text-gray-600">
                                    <th class="py-2 pr-4">Fecha</th>
                                    <th class="py-2 pr-4">Categoría</th>
                                    <th class="py-2 pr-4">Monto</th>
                                    <th class="py-2 pr-4"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($account->transactions as $tx)
                                    <tr>
                                        <td class="py-2 pr-4">{{ $tx->occurred_on->format('Y-m-d') }}</td>
                                        <td class="py-2 pr-4">
                                            <div>{{ $tx->category?->name }}</div>
                                            @if($tx->category?->kindLabel())
                                                <div class="text-xs text-gray-500">{{ $tx->category->kindLabel() }}</div>
                                            @endif
                                        </td>
                                        <td class="py-2 pr-4">{{ number_format($tx->amount, 2) }}</td>
                                        <td class="py-2 pr-4 text-right">
                                            <a href="{{ route('movimientos.show', $tx) }}" class="text-indigo-600 hover:underline">Ver</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-4 text-gray-500">Sin movimientos en esta cuenta.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
