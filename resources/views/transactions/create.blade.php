@php($presetTipo = $presetTipo ?? null)
@php($createTitle = match ($presetTipo) {
    'ingreso' => 'Registrar ingreso',
    'gasto' => 'Registrar gasto',
    default => 'Nuevo movimiento',
})

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $createTitle }}</h2>
            @if($presetTipo === 'ingreso')
                <p class="text-sm text-gray-600">Solo categorías clasificadas como <strong>ingreso</strong>. También puedes usar <a href="{{ route('movimientos.create') }}" class="text-indigo-600 hover:underline">nuevo movimiento</a> para ver todas las categorías.</p>
            @elseif($presetTipo === 'gasto')
                <p class="text-sm text-gray-600">Solo categorías clasificadas como <strong>gasto</strong>.</p>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-form-feedback title="Revisa los datos del formulario" />

            @if($accounts->isEmpty())
                <div class="rounded-md bg-amber-50 border border-amber-200 p-4 text-sm text-amber-900">
                    Necesitas al menos una cuenta. <a href="{{ route('cuentas.create') }}" class="font-semibold underline">Crear cuenta</a>
                </div>
            @elseif($categories->isEmpty())
                <div class="rounded-md bg-amber-50 border border-amber-200 p-4 text-sm text-amber-900">
                    @if($presetTipo === 'ingreso')
                        No tienes categorías de ingreso. <a href="{{ route('categorias.create') }}" class="font-semibold underline">Crear categoría</a> con tipo «Ingreso».
                    @elseif($presetTipo === 'gasto')
                        No tienes categorías de gasto. <a href="{{ route('categorias.create') }}" class="font-semibold underline">Crear categoría</a> con tipo «Gasto».
                    @else
                        Necesitas al menos una categoría. <a href="{{ route('categorias.create') }}" class="font-semibold underline">Crear categoría</a>
                    @endif
                </div>
            @else
                @php($accSel = old('account_id', $defaultAccountId))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form method="post" action="{{ route('movimientos.store') }}" class="p-6 space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="account_id" value="Cuenta" />
                            <select id="account_id" name="account_id" required
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="" disabled @selected(!$accSel)>Selecciona una cuenta</option>
                                @foreach($accounts as $a)
                                    <option value="{{ $a->id }}" @selected((int) $accSel === $a->id)>{{ $a->name }} — {{ $a->typeLabel() }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Puedes abrir este formulario desde el detalle de una cuenta para que venga preseleccionada.</p>
                            <x-input-error class="mt-2" :messages="$errors->get('account_id')" />
                        </div>

                        <div>
                            <x-input-label for="category_id" value="Categoría" />
                            <select id="category_id" name="category_id" required
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="" disabled @selected(!old('category_id'))>Selecciona una categoría</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}" @selected(old('category_id') == $c->id)>
                                        {{ $c->name }} — {{ $c->typeLabel() }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">La cuenta y la categoría deben ser del mismo usuario.</p>
                            <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                        </div>

                        <div>
                            <x-input-label for="amount" value="Monto" />
                            <x-text-input id="amount" name="amount" type="number" step="0.01" min="0.01" class="mt-1 block w-full" :value="old('amount')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('amount')" />
                        </div>

                        <div>
                            <x-input-label for="occurred_on" value="Fecha" />
                            <x-text-input id="occurred_on" name="occurred_on" type="date" class="mt-1 block w-full" :value="old('occurred_on', now()->toDateString())" required />
                            <x-input-error class="mt-2" :messages="$errors->get('occurred_on')" />
                        </div>

                        <div>
                            <x-input-label for="description" value="Descripción (opcional)" />
                            <textarea id="description" name="description" rows="3"
                                      class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Guardar') }}</x-primary-button>
                            <a href="{{ route('movimientos.index', $presetTipo ? ['tipo' => $presetTipo] : []) }}" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
