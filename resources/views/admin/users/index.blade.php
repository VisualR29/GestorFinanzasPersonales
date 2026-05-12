<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Administración — Usuarios</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <x-form-feedback title="No se pudo actualizar el rol" />

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-4">Solo el rol <strong>admin</strong> puede acceder a esta vista. Puedes cambiar entre <strong>admin</strong> y <strong>usuario</strong> respetando que siempre quede al menos un administrador.</p>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead>
                                <tr class="text-left text-gray-600">
                                    <th class="py-2 pr-4">ID</th>
                                    <th class="py-2 pr-4">Nombre</th>
                                    <th class="py-2 pr-4">Correo</th>
                                    <th class="py-2 pr-4">Teléfono</th>
                                    <th class="py-2 pr-4">Ciudad</th>
                                    <th class="py-2 pr-4">Ocupación</th>
                                    <th class="py-2 pr-4">Rol</th>
                                    <th class="py-2 pr-4 whitespace-nowrap">Cambiar rol</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($users as $user)
                                    <tr class="align-top">
                                        <td class="py-2 pr-4">{{ $user->id }}</td>
                                        <td class="py-2 pr-4 font-medium text-gray-900">{{ $user->name }}</td>
                                        <td class="py-2 pr-4">{{ $user->email }}</td>
                                        <td class="py-2 pr-4">{{ $user->phone ?: '—' }}</td>
                                        <td class="py-2 pr-4">{{ $user->city ?: '—' }}@if($user->country)<span class="block text-xs text-gray-500">{{ $user->country }}</span>@endif</td>
                                        <td class="py-2 pr-4 max-w-[12rem] truncate" title="{{ $user->occupation }}">{{ $user->occupation ?: '—' }}</td>
                                        <td class="py-2 pr-4">
                                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $user->role === \App\Models\User::ROLE_ADMIN ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $user->role }}
                                            </span>
                                        </td>
                                        <td class="py-2 pr-4">
                                            <form method="post" action="{{ route('admin.usuarios.update-role', $user) }}" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                                @csrf
                                                @method('PATCH')
                                                <label for="role-{{ $user->id }}" class="sr-only">Rol para {{ $user->name }}</label>
                                                <select id="role-{{ $user->id }}" name="role"
                                                        class="block w-full min-w-[11rem] rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                    <option value="{{ \App\Models\User::ROLE_USUARIO }}" @selected($user->role === \App\Models\User::ROLE_USUARIO)>Usuario</option>
                                                    <option value="{{ \App\Models\User::ROLE_ADMIN }}" @selected($user->role === \App\Models\User::ROLE_ADMIN)>Administrador</option>
                                                </select>
                                                <button type="submit"
                                                        class="inline-flex shrink-0 items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-white shadow-sm hover:bg-indigo-500">
                                                    Guardar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $users->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
