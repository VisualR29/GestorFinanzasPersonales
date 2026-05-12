<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Account;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

trait ProvidesFinanceFormOptions
{
    /**
     * Cuenta por query `?cuenta=` (solo si pertenece al usuario o eres admin).
     */
    protected function defaultAccountIdFromQuery(Request $request): ?int
    {
        $id = (int) $request->query('cuenta', 0);
        if ($id < 1) {
            return null;
        }

        $account = Account::query()->find($id);
        if (! $account) {
            return null;
        }

        $user = $request->user();
        if ($user && ! $user->isAdmin() && (int) $account->user_id !== (int) $user->id) {
            return null;
        }

        return $account->id;
    }

    /**
     * @return Collection<int, Account>
     */
    protected function accountsForForm()
    {
        $ids = $this->financeUserIds();
        $q = Account::query()->orderBy('name');
        if ($ids !== null) {
            $q->whereIn('user_id', $ids);
        }

        return $q->get();
    }

    /**
     * @return Collection<int, Category>
     */
    protected function categoriesForForm()
    {
        $ids = $this->financeUserIds();
        $q = Category::query()->orderBy('name');
        if ($ids !== null) {
            $q->whereIn('user_id', $ids);
        }

        return $q->get();
    }

    /**
     * @return Collection<int, Category>
     */
    protected function incomeCategoriesForForm()
    {
        $ids = $this->financeUserIds();
        $q = Category::query()->where('type', 'ingreso')->orderBy('name');
        if ($ids !== null) {
            $q->whereIn('user_id', $ids);
        }

        return $q->get();
    }

    /**
     * @return Collection<int, Category>
     */
    protected function expenseCategoriesForForm()
    {
        $ids = $this->financeUserIds();
        $q = Category::query()->where('type', 'gasto')->orderBy('name');
        if ($ids !== null) {
            $q->whereIn('user_id', $ids);
        }

        return $q->get();
    }
}
