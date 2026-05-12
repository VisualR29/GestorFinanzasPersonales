<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\AuthorizesFinanceResources;
use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccountApiController extends Controller
{
    use AuthorizesFinanceResources;

    public function index(Request $request): JsonResponse
    {
        $ids = $this->financeUserIds();
        $query = Account::query()->latest();
        if ($ids !== null) {
            $query->whereIn('user_id', $ids);
        }

        return response()->json($query->paginate($request->integer('per_page', 15)));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(Account::typeKeys())],
            'currency' => ['required', 'string', 'max:8'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        if ($request->user()?->isAdmin() && $request->filled('user_id')) {
            $validated['user_id'] = (int) $request->input('user_id');
        } else {
            $validated['user_id'] = $request->user()->id;
        }

        $account = Account::create($validated);

        return response()->json($account, 201);
    }

    public function show(Account $account): JsonResponse
    {
        $this->ensureAccountOwner($account);

        return response()->json($account->load('user'));
    }

    public function update(Request $request, Account $account): JsonResponse
    {
        $this->ensureAccountOwner($account);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'type' => ['sometimes', 'required', Rule::in(Account::typeKeys())],
            'currency' => ['sometimes', 'required', 'string', 'max:8'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        if ($request->user()?->isAdmin() && $request->filled('user_id')) {
            $validated['user_id'] = (int) $request->input('user_id');
        }

        $account->update($validated);

        return response()->json($account->fresh());
    }

    public function destroy(Account $account): JsonResponse
    {
        $this->ensureAccountOwner($account);
        $account->delete();

        return response()->json(['message' => 'Cuenta eliminada.']);
    }
}
