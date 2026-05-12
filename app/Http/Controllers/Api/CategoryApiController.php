<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\AuthorizesFinanceResources;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryApiController extends Controller
{
    use AuthorizesFinanceResources;

    public function index(Request $request): JsonResponse
    {
        $ids = $this->financeUserIds();
        $query = Category::query()->latest();
        if ($ids !== null) {
            $query->whereIn('user_id', $ids);
        }

        return response()->json($query->paginate($request->integer('per_page', 15)));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(Category::typeKeys())],
            'kind' => ['nullable', 'string', Rule::in(Category::kindKeysForType($request->input('type')))],
            'color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        $validated['kind'] = filled($validated['kind'] ?? null) ? $validated['kind'] : null;

        if ($request->user()?->isAdmin() && $request->filled('user_id')) {
            $validated['user_id'] = (int) $request->input('user_id');
        } else {
            $validated['user_id'] = $request->user()->id;
        }

        $category = Category::create($validated);

        return response()->json($category, 201);
    }

    public function show(Category $category): JsonResponse
    {
        $this->ensureCategoryOwner($category);

        return response()->json($category->load('user'));
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $this->ensureCategoryOwner($category);

        $typeForKind = $request->input('type', $category->type);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'type' => ['sometimes', 'required', Rule::in(Category::typeKeys())],
            'kind' => ['nullable', 'string', Rule::in(Category::kindKeysForType($typeForKind))],
            'color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        $validated['kind'] = filled($validated['kind'] ?? null) ? $validated['kind'] : null;

        if ($request->user()?->isAdmin() && $request->filled('user_id')) {
            $validated['user_id'] = (int) $request->input('user_id');
        }

        $category->update($validated);

        return response()->json($category->fresh());
    }

    public function destroy(Category $category): JsonResponse
    {
        $this->ensureCategoryOwner($category);
        $category->delete();

        return response()->json(['message' => 'Categoría eliminada.']);
    }
}
