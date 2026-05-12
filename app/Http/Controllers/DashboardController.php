<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();

        $startMonth = now()->copy()->startOfMonth()->toDateString();
        $endMonth = now()->copy()->endOfMonth()->toDateString();

        if ($user->isAdmin()) {
            $stats = [
                'accounts' => Account::query()->count(),
                'categories' => Category::query()->count(),
                'movimientos' => Transaction::query()->count(),
                'ingresos_mes' => (float) Transaction::query()
                    ->whereHas('category', fn ($q) => $q->where('type', 'ingreso'))
                    ->whereBetween('occurred_on', [$startMonth, $endMonth])
                    ->sum('amount'),
            ];
        } else {
            $stats = [
                'accounts' => Account::query()->where('user_id', $user->id)->count(),
                'categories' => Category::query()->where('user_id', $user->id)->count(),
                'movimientos' => Transaction::query()
                    ->whereHas('account', fn ($q) => $q->where('user_id', $user->id))
                    ->count(),
                'ingresos_mes' => (float) Transaction::query()
                    ->whereHas('account', fn ($q) => $q->where('user_id', $user->id))
                    ->whereHas('category', fn ($q) => $q->where('type', 'ingreso'))
                    ->whereBetween('occurred_on', [$startMonth, $endMonth])
                    ->sum('amount'),
            ];
        }

        return view('dashboard', compact('stats'));
    }
}
