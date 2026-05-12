<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar categoría</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-form-feedback title="Revisa los datos del formulario" />

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="post" action="{{ route('categorias.update', $category) }}" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    @if(auth()->user()->isAdmin() && isset($users) && $users->count())
                        <div>
                            <x-input-label for="user_id" value="Usuario propietario" />
                            <select id="user_id" name="user_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" @selected(old('user_id', $category->user_id) == $u->id)>
                                        {{ $u->name }} ({{ $u->email }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('user_id')" />
                        </div>
                    @endif

                    <div class="space-y-4 rounded-lg border border-gray-100 bg-gray-50 p-4"
                         data-kinds='@json($kindGroups, JSON_HEX_APOS)'
                         x-data="{
                            tipo: @js(old('type', $category->type)),
                            presetKind: @js(old('kind', $category->kind ?? '')),
                            kinds: {},
                            init() {
                                try {
                                    this.kinds = JSON.parse(this.$el.dataset.kinds || '{}');
                                } catch (e) {
                                    this.kinds = {};
                                }
                            },
                            syncKind() {
                                const opts = this.kinds[this.tipo] || {};
                                if (!this.presetKind || !Object.prototype.hasOwnProperty.call(opts, this.presetKind)) {
                                    this.presetKind = '';
                                }
                            },
                            refillKindOptions() {
                                const el = this.$refs.kindSelect;
                                if (!el) {
                                    return;
                                }
                                while (el.options.length > 1) {
                                    el.remove(1);
                                }
                                const opts = this.kinds[this.tipo] || {};
                                for (const [key, label] of Object.entries(opts)) {
                                    const o = document.createElement('option');
                                    o.value = key;
                                    o.textContent = label;
                                    el.appendChild(o);
                                }
                                this.syncKind();
                            },
                         }"
                         x-init="$nextTick(() => refillKindOptions())">
                        <div>
                            <x-input-label for="type" value="Tipo (ingreso / gasto)" />
                            <select id="type" name="type"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                    x-model="tipo"
                                    x-on:change="$nextTick(() => refillKindOptions())">
                                @foreach($typeLabels as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('type')" />
                        </div>

                        <div>
                            <x-input-label for="kind" value="Concepto detallado (opcional)" />
                            <select id="kind" name="kind" x-ref="kindSelect"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    x-model="presetKind">
                                <option value="">— Sin concepto —</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('kind')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="name" value="Nombre de la categoría" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $category->name)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="color" value="Color (#RRGGBB, opcional)" />
                        <x-text-input id="color" name="color" type="text" class="mt-1 block w-full" :value="old('color', $category->color)" placeholder="#4F46E5" maxlength="7" />
                        <x-input-error class="mt-2" :messages="$errors->get('color')" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Guardar cambios') }}</x-primary-button>
                        <a href="{{ route('categorias.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
