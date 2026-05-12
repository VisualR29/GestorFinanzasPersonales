<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar movimiento</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-form-feedback title="Revisa los datos del formulario" />

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="post" action="{{ route('movimientos.update', $transaction) }}" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="account_id" value="Cuenta" />
                        <select id="account_id" name="account_id" required
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            @foreach($accounts as $a)
                                    <option value="{{ $a->id }}" @selected((int) old('account_id', $defaultAccountId ?? $transaction->account_id) === $a->id)>{{ $a->name }} — {{ $a->typeLabel() }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('account_id')" />
                    </div>

                    <div>
                        <x-input-label for="category_id" value="Categoría" />
                        <select id="category_id" name="category_id" required
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" @selected(old('category_id', $transaction->category_id) == $c->id)>
                                    {{ $c->name }} — {{ $c->typeLabel() }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                    </div>

                    <div>
                        <x-input-label for="amount" value="Monto" />
                        <x-text-input id="amount" name="amount" type="number" step="0.01" min="0.01" class="mt-1 block w-full" :value="old('amount', $transaction->amount)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('amount')" />
                    </div>

                    <div>
                        <x-input-label for="occurred_on" value="Fecha" />
                        <x-text-input id="occurred_on" name="occurred_on" type="date" class="mt-1 block w-full" :value="old('occurred_on', $transaction->occurred_on->toDateString())" required />
                        <x-input-error class="mt-2" :messages="$errors->get('occurred_on')" />
                    </div>

                    <div>
                        <x-input-label for="description" value="Descripción (opcional)" />
                        <textarea id="description" name="description" rows="3"
                                  class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $transaction->description) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Guardar cambios') }}</x-primary-button>
                        <a href="{{ route('movimientos.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
