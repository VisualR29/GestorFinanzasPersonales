<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $category->name }}</h2>
            <div class="space-x-3">
                <a href="{{ route('categorias.edit', $category) }}" class="text-indigo-600 hover:underline text-sm">Editar</a>
                <a href="{{ route('categorias.index') }}" class="text-gray-600 hover:underline text-sm">Volver</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Tipo</dt>
                        <dd class="font-medium text-gray-900">{{ $category->typeLabel() }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Concepto</dt>
                        <dd class="font-medium text-gray-900">{{ $category->kindLabel() ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Color</dt>
                        <dd class="font-medium text-gray-900">{{ $category->color ?: '—' }}</dd>
                    </div>
                    @if(auth()->user()->isAdmin())
                        <div class="sm:col-span-2">
                            <dt class="text-gray-500">Usuario</dt>
                            <dd class="font-medium text-gray-900">{{ $category->user?->name }} — {{ $category->user?->email }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</x-app-layout>
