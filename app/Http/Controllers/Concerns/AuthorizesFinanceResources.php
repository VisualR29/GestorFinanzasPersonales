<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;

trait AuthorizesFinanceResources
{
    protected function financeUserIds(): ?array
    {
        $user = auth()->user();
        if (! $user instanceof User) {
            abort(403);
        }

        if ($user->isAdmin()) {
            return null;
        }

        return [$user->id];
    }

    protected function ensureAccountOwner(Account $account): void
    {
        if (auth()->user()?->isAdmin()) {
            return;
        }

        if ((int) $account->user_id !== (int) auth()->id()) {
            abort(403);
        }
    }

    protected function ensureCategoryOwner(Category $category): void
    {
        if (auth()->user()?->isAdmin()) {
            return;
        }

        if ((int) $category->user_id !== (int) auth()->id()) {
            abort(403);
        }
    }

    protected function ensureTransactionAccess(Transaction $transaction): void
    {
        $this->ensureAccountOwner($transaction->account);
        $this->ensureCategoryOwner($transaction->category);
        if ((int) $transaction->account->user_id !== (int) $transaction->category->user_id) {
            abort(403);
        }
    }
}
