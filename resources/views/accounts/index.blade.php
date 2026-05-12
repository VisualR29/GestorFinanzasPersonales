<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cuentas</h2>
            <a href="{{ route('cuentas.create') }}"
               class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                Nueva cuenta
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-form-feedback />

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead>
                                <tr class="text-left text-gray-600">
                                    <th class="py-2 pr-4">Nombre</th>
                                    <th class="py-2 pr-4">Tipo</th>
                                    <th class="py-2 pr-4">Moneda</th>
                                    @if(auth()->user()->isAdmin())
                                        <th class="py-2 pr-4">Usuario</th>
                                    @endif
                                    <th class="py-2 pr-4"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($accounts as $account)
                                    <tr>
                                        <td class="py-2 pr-4 font-medium text-gray-900">{{ $account->name }}</td>
                                        <td class="py-2 pr-4">{{ $account->typeLabel() }}</td>
                                        <td class="py-2 pr-4">{{ $account->currency }}</td>
                                        @if(auth()->user()->isAdmin())
                                            <td class="py-2 pr-4">{{ $account->user?->email }}</td>
                                        @endif
                                        <td class="py-2 pr-4 text-right whitespace-nowrap">
                                            <a href="{{ route('cuentas.show', $account) }}" class="text-indigo-600 hover:underline">Ver</a>
                                            <a href="{{ route('cuentas.edit', $account) }}" class="ml-3 text-indigo-600 hover:underline">Editar</a>
                                            <form action="{{ route('cuentas.destroy', $account) }}" method="post" class="inline" onsubmit="return confirm('¿Eliminar esta cuenta?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="ml-3 text-red-600 hover:underline">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="99" class="py-10 px-4 text-center text-gray-600">
                                            <p class="mb-3">Aún no tienes cuentas.</p>
                                            <a href="{{ route('cuentas.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Crear primera cuenta</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $accounts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
