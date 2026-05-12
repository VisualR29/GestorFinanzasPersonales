<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesFinanceResources;
use App\Models\Account;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AccountController extends Controller
{
    use AuthorizesFinanceResources;

    public function index(): View
    {
        $ids = $this->financeUserIds();
        $query = Account::query()->with('user')->orderBy('name');
        if ($ids !== null) {
            $query->whereIn('user_id', $ids);
        }

        return view('accounts.index', [
            'accounts' => $query->paginate(10)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        $users = [];
        if (auth()->user()?->isAdmin()) {
            $users = User::query()->orderBy('name')->get(['id', 'name', 'email']);
        }

        return view('accounts.create', [
            'typeLabels' => Account::typeLabels(),
            'users' => $users,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(Account::typeKeys())],
            'currency' => ['required', 'string', 'max:8'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        if (auth()->user()?->isAdmin() && $request->filled('user_id')) {
            $validated['user_id'] = (int) $request->input('user_id');
        } else {
            $validated['user_id'] = auth()->id();
        }

        Account::create($validated);

        return redirect()->route('cuentas.index')->with('status', 'Cuenta creada correctamente.');
    }

    public function show(Account $cuenta): View
    {
        $this->ensureAccountOwner($cuenta);
        $cuenta->load([
            'user',
            'transactions' => fn ($q) => $q->with('category')->latest('occurred_on')->latest('id'),
        ]);

        return view('accounts.show', ['account' => $cuenta]);
    }

    public function edit(Account $cuenta): View
    {
        $this->ensureAccountOwner($cuenta);

        $users = [];
        if (auth()->user()?->isAdmin()) {
            $users = User::query()->orderBy('name')->get(['id', 'name', 'email']);
        }

        return view('accounts.edit', [
            'account' => $cuenta,
            'typeLabels' => Account::typeLabels(),
            'users' => $users,
        ]);
    }

    public function update(Request $request, Account $cuenta): RedirectResponse
    {
        $this->ensureAccountOwner($cuenta);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(Account::typeKeys())],
            'currency' => ['required', 'string', 'max:8'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if (auth()->user()?->isAdmin() && $request->filled('user_id')) {
            $request->validate(['user_id' => ['required', 'exists:users,id']]);
            $validated['user_id'] = (int) $request->input('user_id');
        }

        $cuenta->update($validated);

        return redirect()->route('cuentas.index')->with('status', 'Cuenta actualizada correctamente.');
    }

    public function destroy(Account $cuenta): RedirectResponse
    {
        $this->ensureAccountOwner($cuenta);
        $cuenta->delete();

        return redirect()->route('cuentas.index')->with('status', 'Cuenta eliminada.');
    }
}
