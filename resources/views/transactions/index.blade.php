@php
    $filters = [
        ['label' => 'Todos', 'tipo' => null],
        ['label' => 'Solo ingresos', 'tipo' => 'ingreso'],
        ['label' => 'Solo gastos', 'tipo' => 'gasto'],
    ];
    $heading = match ($tipo) {
        'ingreso' => 'Ingresos',
        'gasto' => 'Gastos',
        default => 'Movimientos',
    };
    $newParams = array_filter(['tipo' => $tipo]);
    $emptyColspan = auth()->user()->isAdmin() ? 7 : 6;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $heading }}</h2>
                @if($tipo !== null)
                    <p class="mt-1 text-sm text-gray-600">
                        Vista filtrada por categorías tipo <strong>{{ $tipo === 'ingreso' ? 'ingreso' : 'gasto' }}</strong>.
                        <a href="{{ route('movimientos.index') }}" class="text-indigo-600 hover:underline">Ver todos los movimientos</a>
                    </p>
                @endif
            </div>
            <a href="{{ route('movimientos.create', $newParams) }}"
               class="inline-flex shrink-0 items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                @if($tipo === 'ingreso')
                    Registrar ingreso
                @elseif($tipo === 'gasto')
                    Registrar gasto
                @else
                    Nuevo movimiento
                @endif
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-form-feedback />

            <div class="flex flex-wrap gap-2">
                @foreach($filters as $item)
                    @php($active = $tipo === $item['tipo'])
                    <a href="{{ route('movimientos.index', $item['tipo'] ? ['tipo' => $item['tipo']] : []) }}"
                       @class([
                           'inline-flex items-center rounded-full px-3 py-1 text-sm font-medium ring-1 ring-inset transition',
                           'bg-indigo-600 text-white ring-indigo-600' => $active,
                           'bg-white text-gray-700 ring-gray-300 hover:bg-gray-50' => ! $active,
                       ])>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>

            @if($tipo !== null && $totalTipo !== null && $mesTipo !== null)
                <div class="grid gap-4 sm:grid-cols-2">
                    @if($tipo === 'ingreso')
                        <div class="rounded-lg border border-emerald-100 bg-emerald-50 p-4">
                            <p class="text-sm font-medium text-emerald-900">Total histórico (ingresos)</p>
                            <p class="mt-1 text-2xl font-semibold text-emerald-800">${{ number_format($totalTipo, 2) }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                            <p class="text-sm font-medium text-gray-700">Ingresos del mes en curso</p>
                            <p class="mt-1 text-2xl font-semibold text-gray-900">${{ number_format($mesTipo, 2) }}</p>
                            <p class="mt-1 text-xs text-gray-500">Mes calendario según la fecha del servidor.</p>
                        </div>
                    @else
                        <div class="rounded-lg border border-rose-100 bg-rose-50 p-4">
                            <p class="text-sm font-medium text-rose-900">Total histórico (gastos)</p>
                            <p class="mt-1 text-2xl font-semibold text-rose-800">${{ number_format($totalTipo, 2) }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                            <p class="text-sm font-medium text-gray-700">Gastos del mes en curso</p>
                            <p class="mt-1 text-2xl font-semibold text-gray-900">${{ number_format($mesTipo, 2) }}</p>
                            <p class="mt-1 text-xs text-gray-500">Mes calendario según la fecha del servidor.</p>
                        </div>
                    @endif
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead>
                                <tr class="text-left text-gray-600">
                                    <th class="py-2 pr-4">Fecha</th>
                                    <th class="py-2 pr-4">Cuenta</th>
                                    <th class="py-2 pr-4">Categoría</th>
                                    <th class="py-2 pr-4">Monto</th>
                                    @if(auth()->user()->isAdmin())
                                        <th class="py-2 pr-4">Usuario (cuenta)</th>
                                    @endif
                                    <th class="py-2 pr-4"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($transactions as $tx)
                                    <tr>
                                        <td class="py-2 pr-4">{{ $tx->occurred_on->format('Y-m-d') }}</td>
                                        <td class="py-2 pr-4">{{ $tx->account?->name }}</td>
                                        <td class="py-2 pr-4">
                                            <div class="font-medium text-gray-900">{{ $tx->category?->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $tx->category?->typeLabel() }}@if($tx->category?->kindLabel()) — {{ $tx->category->kindLabel() }}@endif</div>
                                        </td>
                                        <td class="py-2 pr-4 font-medium">${{ number_format($tx->amount, 2) }}</td>
                                        @if(auth()->user()->isAdmin())
                                            <td class="py-2 pr-4">{{ $tx->account?->user?->email }}</td>
                                        @endif
                                        <td class="py-2 pr-4 text-right whitespace-nowrap">
                                            <a href="{{ route('movimientos.show', $tx) }}" class="text-indigo-600 hover:underline">Ver</a>
                                            <a href="{{ route('movimientos.edit', $tx) }}" class="ml-3 text-indigo-600 hover:underline">Editar</a>
                                            <form action="{{ route('movimientos.destroy', $tx) }}" method="post" class="inline" onsubmit="return confirm('¿Eliminar este movimiento?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="ml-3 text-red-600 hover:underline">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $emptyColspan }}" class="py-8 text-center text-gray-500">
                                            @if($tipo === 'ingreso')
                                                No hay ingresos. Crea categorías tipo «Ingreso» y registra un movimiento.
                                            @elseif($tipo === 'gasto')
                                                No hay gastos en este filtro.
                                            @else
                                                No hay movimientos.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
