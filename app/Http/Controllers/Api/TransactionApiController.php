<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\AuthorizesFinanceResources;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TransactionApiController extends Controller
{
    use AuthorizesFinanceResources;

    public function index(Request $request): JsonResponse
    {
        $ids = $this->financeUserIds();
        $query = Transaction::query()
            ->with(['account', 'category'])
            ->latest('occurred_on')
            ->latest('id');

        if ($ids !== null) {
            $query->whereHas('account', fn ($q) => $q->whereIn('user_id', $ids));
        }

        return response()->json($query->paginate($request->integer('per_page', 15)));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'account_id' => ['required', 'exists:accounts,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:500'],
            'occurred_on' => ['required', 'date'],
        ]);

        $account = Account::query()->findOrFail($validated['account_id']);
        $category = Category::query()->findOrFail($validated['category_id']);

        $this->ensureAccountOwner($account);
        $this->ensureCategoryOwner($category);

        if ((int) $account->user_id !== (int) $category->user_id) {
            throw ValidationException::withMessages([
                'category_id' => ['La cuenta y la categoría deben pertenecer al mismo usuario.'],
            ]);
        }

        $transaction = Transaction::create($validated);

        return response()->json($transaction->load(['account', 'category']), 201);
    }

    public function show(Transaction $transaction): JsonResponse
    {
        $transaction->load(['account', 'category']);
        $this->ensureTransactionAccess($transaction);

        return response()->json($transaction);
    }

    public function update(Request $request, Transaction $transaction): JsonResponse
    {
        $transaction->load(['account', 'category']);
        $this->ensureTransactionAccess($transaction);

        $validated = $request->validate([
            'account_id' => ['sometimes', 'required', 'exists:accounts,id'],
            'category_id' => ['sometimes', 'required', 'exists:categories,id'],
            'amount' => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:500'],
            'occurred_on' => ['sometimes', 'required', 'date'],
        ]);

        $accountId = $validated['account_id'] ?? $transaction->account_id;
        $categoryId = $validated['category_id'] ?? $transaction->category_id;

        $account = Account::query()->findOrFail($accountId);
        $category = Category::query()->findOrFail($categoryId);

        $this->ensureAccountOwner($account);
        $this->ensureCategoryOwner($category);

        if ((int) $account->user_id !== (int) $category->user_id) {
            throw ValidationException::withMessages([
                'category_id' => ['La cuenta y la categoría deben pertenecer al mismo usuario.'],
            ]);
        }

        $transaction->update($validated);

        return response()->json($transaction->fresh()->load(['account', 'category']));
    }

    public function destroy(Transaction $transaction): JsonResponse
    {
        $transaction->load(['account', 'category']);
        $this->ensureTransactionAccess($transaction);
        $transaction->delete();

        return response()->json(['message' => 'Movimiento eliminado.']);
    }
}
