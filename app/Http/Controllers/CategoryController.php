<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesFinanceResources;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    use AuthorizesFinanceResources;

    public function index(): View
    {
        $ids = $this->financeUserIds();
        $query = Category::query()->with('user')->orderBy('type')->orderBy('name');
        if ($ids !== null) {
            $query->whereIn('user_id', $ids);
        }

        return view('categories.index', [
            'categories' => $query->paginate(10)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        $users = [];
        if (auth()->user()?->isAdmin()) {
            $users = User::query()->orderBy('name')->get(['id', 'name', 'email']);
        }

        return view('categories.create', [
            'typeLabels' => Category::typeLabels(),
            'kindGroups' => config('finanzas.category_kinds'),
            'users' => $users,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(Category::typeKeys())],
            'kind' => ['nullable', 'string', Rule::in(Category::kindKeysForType($request->input('type')))],
            'color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        $validated['kind'] = filled($validated['kind'] ?? null) ? $validated['kind'] : null;

        if (auth()->user()?->isAdmin() && $request->filled('user_id')) {
            $validated['user_id'] = (int) $request->input('user_id');
        } else {
            $validated['user_id'] = auth()->id();
        }

        Category::create($validated);

        return redirect()->route('categorias.index')->with('status', 'Categoría creada correctamente.');
    }

    public function show(Category $categoria): View
    {
        $this->ensureCategoryOwner($categoria);
        $categoria->load(['user', 'transactions.account']);

        return view('categories.show', ['category' => $categoria]);
    }

    public function edit(Category $categoria): View
    {
        $this->ensureCategoryOwner($categoria);

        $users = [];
        if (auth()->user()?->isAdmin()) {
            $users = User::query()->orderBy('name')->get(['id', 'name', 'email']);
        }

        return view('categories.edit', [
            'category' => $categoria,
            'typeLabels' => Category::typeLabels(),
            'kindGroups' => config('finanzas.category_kinds'),
            'users' => $users,
        ]);
    }

    public function update(Request $request, Category $categoria): RedirectResponse
    {
        $this->ensureCategoryOwner($categoria);

        $typeForKind = $request->input('type', $categoria->type);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(Category::typeKeys())],
            'kind' => ['nullable', 'string', Rule::in(Category::kindKeysForType($typeForKind))],
            'color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        $validated['kind'] = filled($validated['kind'] ?? null) ? $validated['kind'] : null;

        if (auth()->user()?->isAdmin() && $request->filled('user_id')) {
            $validated['user_id'] = (int) $request->input('user_id');
        }

        $categoria->update($validated);

        return redirect()->route('categorias.index')->with('status', 'Categoría actualizada correctamente.');
    }

    public function destroy(Category $categoria): RedirectResponse
    {
        $this->ensureCategoryOwner($categoria);
        $categoria->delete();

        return redirect()->route('categorias.index')->with('status', 'Categoría eliminada.');
    }
}
