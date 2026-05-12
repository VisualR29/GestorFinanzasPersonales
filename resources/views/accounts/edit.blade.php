<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar cuenta</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-form-feedback title="Revisa los datos del formulario" />

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="post" action="{{ route('cuentas.update', $account) }}" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    @if(auth()->user()->isAdmin() && isset($users) && $users->count())
                        <div>
                            <x-input-label for="user_id" value="Usuario propietario" />
                            <select id="user_id" name="user_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" @selected(old('user_id', $account->user_id) == $u->id)>
                                        {{ $u->name }} ({{ $u->email }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('user_id')" />
                        </div>
                    @endif

                    <div>
                        <x-input-label for="name" value="Nombre" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $account->name)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="type" value="Tipo de cuenta" />
                        <select id="type" name="type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            @foreach($typeLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('type', $account->type) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('type')" />
                    </div>

                    <div>
                        <x-input-label for="currency" value="Moneda" />
                        <x-text-input id="currency" name="currency" type="text" class="mt-1 block w-full" :value="old('currency', $account->currency)" required maxlength="8" />
                        <x-input-error class="mt-2" :messages="$errors->get('currency')" />
                    </div>

                    <div>
                        <x-input-label for="notes" value="Notas (opcional)" />
                        <textarea id="notes" name="notes" rows="3"
                                  class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes', $account->notes) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Guardar cambios') }}</x-primary-button>
                        <a href="{{ route('cuentas.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
