<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesFinanceResources;
use App\Http\Controllers\Concerns\ProvidesFinanceFormOptions;
use App\Http\Controllers\Concerns\ValidatesFinanceTransactions;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    use AuthorizesFinanceResources;
    use ProvidesFinanceFormOptions;
    use ValidatesFinanceTransactions;

    public function index(Request $request): View
    {
        $ids = $this->financeUserIds();

        $tipo = $request->query('tipo');
        if (! is_string($tipo) || ! in_array($tipo, ['ingreso', 'gasto'], true)) {
            $tipo = null;
        }

        $query = Transaction::query()
            ->with(['account.user', 'category'])
            ->latest('occurred_on')
            ->latest('id');

        if ($ids !== null) {
            $query->whereHas('account', fn ($q) => $q->whereIn('user_id', $ids));
        }

        if ($tipo !== null) {
            $query->whereHas('category', fn ($q) => $q->where('type', $tipo));
        }

        $transactions = $query->paginate(15)->withQueryString();

        $totalTipo = null;
        $mesTipo = null;
        if ($tipo !== null) {
            $sumBase = Transaction::query();
            if ($ids !== null) {
                $sumBase->whereHas('account', fn ($q) => $q->whereIn('user_id', $ids));
            }
            $sumBase->whereHas('category', fn ($q) => $q->where('type', $tipo));
            $totalTipo = (float) $sumBase->sum('amount');

            $startMonth = now()->copy()->startOfMonth()->toDateString();
            $endMonth = now()->copy()->endOfMonth()->toDateString();
            $mesTipo = (float) (clone $sumBase)
                ->whereBetween('occurred_on', [$startMonth, $endMonth])
                ->sum('amount');
        }

        return view('transactions.index', compact('transactions', 'tipo', 'totalTipo', 'mesTipo'));
    }

    public function create(Request $request): View
    {
        $presetTipo = $request->query('tipo');
        if (! is_string($presetTipo) || ! in_array($presetTipo, ['ingreso', 'gasto'], true)) {
            $presetTipo = null;
        }

        $categories = match ($presetTipo) {
            'ingreso' => $this->incomeCategoriesForForm(),
            'gasto' => $this->expenseCategoriesForForm(),
            default => $this->categoriesForForm(),
        };

        return view('transactions.create', [
            'accounts' => $this->accountsForForm(),
            'categories' => $categories,
            'defaultAccountId' => $this->defaultAccountIdFromQuery($request),
            'presetTipo' => $presetTipo,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateTransactionPayload($request);
        Transaction::create($validated);

        return redirect()->route('movimientos.index')->with('status', 'Movimiento registrado correctamente.');
    }

    public function show(Transaction $movimiento): View
    {
        $movimiento->load(['account.user', 'category']);
        $this->ensureTransactionAccess($movimiento);

        return view('transactions.show', ['transaction' => $movimiento]);
    }

    public function edit(Request $request, Transaction $movimiento): View
    {
        $movimiento->load(['account', 'category']);
        $this->ensureTransactionAccess($movimiento);

        $defaultAccountId = $this->defaultAccountIdFromQuery($request) ?? $movimiento->account_id;

        return view('transactions.edit', [
            'transaction' => $movimiento,
            'accounts' => $this->accountsForForm(),
            'categories' => $this->categoriesForForm(),
            'defaultAccountId' => $defaultAccountId,
        ]);
    }

    public function update(Request $request, Transaction $movimiento): RedirectResponse
    {
        $movimiento->load(['account', 'category']);
        $this->ensureTransactionAccess($movimiento);

        $validated = $this->validateTransactionPayload($request);
        $movimiento->update($validated);

        return redirect()->route('movimientos.index')->with('status', 'Movimiento actualizado correctamente.');
    }

    public function destroy(Transaction $movimiento): RedirectResponse
    {
        $movimiento->load(['account', 'category']);
        $this->ensureTransactionAccess($movimiento);
        $movimiento->delete();

        return redirect()->route('movimientos.index')->with('status', 'Movimiento eliminado.');
    }
}
