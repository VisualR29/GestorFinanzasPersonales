<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserAdminController extends Controller
{
    public function index(): View
    {
        $users = User::query()->orderBy('role')->orderBy('name')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'in:'.User::ROLE_ADMIN.','.User::ROLE_USUARIO],
        ], [
            'role.required' => 'Selecciona un rol.',
            'role.in' => 'El rol no es válido.',
        ]);

        if ($validated['role'] === User::ROLE_USUARIO && $user->isAdmin()) {
            $otherAdminExists = User::query()
                ->where('role', User::ROLE_ADMIN)
                ->where('id', '!=', $user->id)
                ->exists();

            if (! $otherAdminExists) {
                return back()->withErrors([
                    'role' => 'No puedes cambiar este usuario a «usuario»: debe existir al menos un administrador.',
                ]);
            }
        }

        $user->role = $validated['role'];
        $user->save();

        return back()->with('status', 'Rol actualizado para '.$user->name.'.');
    }
}
