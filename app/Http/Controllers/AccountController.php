<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $accounts = $request->user()->accounts()->latest()->get();

        return view('accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('accounts.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        // saldo berjalan dimulai dari saldo awal.
        $data['balance'] = $data['opening_balance'];
        $request->user()->accounts()->create($data);

        return redirect()->route('accounts.index')->with('success', 'Akun berhasil ditambahkan.');
    }

    public function edit(Account $account)
    {
        $this->authorizeOwner($account);

        return view('accounts.edit', compact('account'));
    }

    public function update(Request $request, Account $account)
    {
        $this->authorizeOwner($account);
        $data = $this->validateData($request);

        // Jika saldo awal diubah, geser saldo berjalan dengan selisihnya.
        $diff = $data['opening_balance'] - (float) $account->opening_balance;
        $data['balance'] = (float) $account->balance + $diff;
        $account->update($data);

        return redirect()->route('accounts.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(Account $account)
    {
        $this->authorizeOwner($account);
        $account->delete();

        return redirect()->route('accounts.index')->with('success', 'Akun dihapus.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:bank,cash,ewallet,savings'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:255'],
            'opening_balance' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function authorizeOwner(Account $account): void
    {
        abort_unless($account->user_id === request()->user()->id, 403);
    }
}
