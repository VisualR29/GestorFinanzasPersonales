<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Categorías</h2>
            <a href="{{ route('categorias.create') }}"
               class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                Nueva categoría
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
                                    <th class="py-2 pr-4">Concepto</th>
                                    <th class="py-2 pr-4">Color</th>
                                    @if(auth()->user()->isAdmin())
                                        <th class="py-2 pr-4">Usuario</th>
                                    @endif
                                    <th class="py-2 pr-4"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($categories as $category)
                                    <tr>
                                        <td class="py-2 pr-4 font-medium text-gray-900">{{ $category->name }}</td>
                                        <td class="py-2 pr-4">{{ $category->typeLabel() }}</td>
                                        <td class="py-2 pr-4 text-gray-700">{{ $category->kindLabel() ?: '—' }}</td>
                                        <td class="py-2 pr-4">
                                            @if($category->color)
                                                <span class="inline-flex items-center gap-2">
                                                    <span class="h-4 w-4 rounded border border-gray-200" style="background: {{ $category->color }}"></span>
                                                    {{ $category->color }}
                                                </span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        @if(auth()->user()->isAdmin())
                                            <td class="py-2 pr-4">{{ $category->user?->email }}</td>
                                        @endif
                                        <td class="py-2 pr-4 text-right whitespace-nowrap">
                                            <a href="{{ route('categorias.show', $category) }}" class="text-indigo-600 hover:underline">Ver</a>
                                            <a href="{{ route('categorias.edit', $category) }}" class="ml-3 text-indigo-600 hover:underline">Editar</a>
                                            <form action="{{ route('categorias.destroy', $category) }}" method="post" class="inline" onsubmit="return confirm('¿Eliminar esta categoría?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="ml-3 text-red-600 hover:underline">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="99" class="py-6 text-gray-500">No hay categorías.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $categories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
