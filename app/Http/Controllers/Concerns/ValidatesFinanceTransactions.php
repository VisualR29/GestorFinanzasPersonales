<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Account;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

trait ValidatesFinanceTransactions
{
    /**
     * @return array<string, mixed>
     */
    protected function validateTransactionPayload(Request $request, ?string $forcedCategoryType = null): array
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
                'category_id' => 'La cuenta y la categoría deben pertenecer al mismo usuario.',
            ]);
        }

        if ($forcedCategoryType !== null && $category->type !== $forcedCategoryType) {
            throw ValidationException::withMessages([
                'category_id' => $forcedCategoryType === 'ingreso'
                    ? 'Selecciona una categoría clasificada como ingreso.'
                    : 'La categoría no corresponde al tipo de movimiento solicitado.',
            ]);
        }

        return $validated;
    }
}
