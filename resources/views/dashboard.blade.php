<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Panel — Finanzas personales
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-sm">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Cuentas</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['accounts'] }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-sm">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Categorías</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['categories'] }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-sm">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Movimientos</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['movimientos'] }}</p>
                </div>
                <div class="rounded-lg border border-emerald-100 bg-emerald-50/90 px-4 py-3 shadow-sm">
                    <p class="text-xs font-medium uppercase tracking-wide text-emerald-800">Ingresos (mes)</p>
                    <p class="mt-1 text-2xl font-semibold text-emerald-900">${{ number_format($stats['ingresos_mes'], 2) }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-2">
                    <p class="text-gray-700">Hola, <strong>{{ auth()->user()->name }}</strong>.</p>
                    <p class="text-sm text-gray-600">
                        Rol: <span class="font-semibold">{{ auth()->user()->role }}</span>
                        @if(auth()->user()->isAdmin())
                            — puedes ver datos de todos los usuarios y acceder a <a class="text-indigo-600 underline" href="{{ route('admin.usuarios.index') }}">Administración de usuarios</a>.
                        @else
                            — accedes solo a tus cuentas, categorías y movimientos.
                        @endif
                    </p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <a href="{{ route('cuentas.index') }}" class="block rounded-lg border border-gray-200 bg-white p-6 shadow-sm hover:border-indigo-300 hover:shadow transition">
                    <h3 class="text-lg font-semibold text-gray-900">Cuentas</h3>
                    <p class="mt-2 text-sm text-gray-600">Tipos ampliados: nómina, crédito, digital, inversiones…</p>
                </a>
                <a href="{{ route('categorias.index') }}" class="block rounded-lg border border-gray-200 bg-white p-6 shadow-sm hover:border-indigo-300 hover:shadow transition">
                    <h3 class="text-lg font-semibold text-gray-900">Categorías</h3>
                    <p class="mt-2 text-sm text-gray-600">Ingreso/gasto y conceptos detallados.</p>
                </a>
                <a href="{{ route('movimientos.index', ['tipo' => 'ingreso']) }}" class="block rounded-lg border border-emerald-200 bg-emerald-50/80 p-6 shadow-sm hover:border-emerald-400 hover:shadow transition">
                    <h3 class="text-lg font-semibold text-emerald-900">Ingresos</h3>
                    <p class="mt-2 text-sm text-emerald-800">Resumen y registro rápido solo de entradas.</p>
                </a>
                <a href="{{ route('movimientos.index') }}" class="block rounded-lg border border-gray-200 bg-white p-6 shadow-sm hover:border-indigo-300 hover:shadow transition">
                    <h3 class="text-lg font-semibold text-gray-900">Movimientos</h3>
                    <p class="mt-2 text-sm text-gray-600">Todos los movimientos (ingresos y gastos).</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
