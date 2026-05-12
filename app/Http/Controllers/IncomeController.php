<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesFinanceResources;
use App\Http\Controllers\Concerns\ValidatesFinanceTransactions;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    use AuthorizesFinanceResources;
    use ValidatesFinanceTransactions;

    public function index(Request $request): RedirectResponse
    {
        return redirect()->route('movimientos.index', ['tipo' => 'ingreso']);
    }

    public function create(Request $request): RedirectResponse
    {
        $params = ['tipo' => 'ingreso'];
        if ($request->filled('cuenta')) {
            $params['cuenta'] = $request->query('cuenta');
        }

        return redirect()->route('movimientos.create', $params);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateTransactionPayload($request, 'ingreso');
        Transaction::create($validated);

        return redirect()->route('movimientos.index', ['tipo' => 'ingreso'])
            ->with('status', 'Ingreso registrado correctamente.');
    }
}
