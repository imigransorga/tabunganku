<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->transactions()->with(['account', 'category']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->latest('date')->latest('id')->paginate(15)->withQueryString();

        return view('transactions.index', compact('transactions'));
    }

    public function create(Request $request)
    {
        return view('transactions.create', $this->formData($request));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        if ($msg = $this->budgetError($request, $data)) {
            return back()->withErrors(['amount' => $msg])->withInput();
        }

        // Income langsung approved; expense masuk sebagai pengajuan (pending).
        if ($data['type'] === 'income') {
            $data['status'] = 'approved';
            $data['approved_at'] = now();
        } else {
            $data['status'] = 'pending';
        }

        $request->user()->transactions()->create($data);

        return redirect()->route('transactions.index')->with('success', 'Transaksi disimpan.');
    }

    public function edit(Request $request, Transaction $transaction)
    {
        $this->authorizeOwner($transaction);

        return view('transactions.edit', array_merge(
            $this->formData($request),
            ['transaction' => $transaction]
        ));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $this->authorizeOwner($transaction);
        $data = $this->validateData($request);

        if ($msg = $this->budgetError($request, $data, $transaction->id)) {
            return back()->withErrors(['amount' => $msg])->withInput();
        }

        $transaction->update($data);

        return redirect()->route('transactions.index')->with('success', 'Transaksi diperbarui.');
    }

    public function destroy(Request $request, Transaction $transaction)
    {
        $this->authorizeOwner($transaction);
        $transaction->delete();

        return back()->with('success', 'Transaksi dihapus.');
    }

    /** Setujui pengajuan pengeluaran -> saldo akun berkurang (lewat observer). */
    public function approve(Request $request, Transaction $transaction)
    {
        $this->authorizeOwner($transaction);
        $transaction->update(['status' => 'approved', 'approved_at' => now()]);

        return back()->with('success', 'Pengajuan disetujui.');
    }

    public function reject(Request $request, Transaction $transaction)
    {
        $this->authorizeOwner($transaction);
        $transaction->update(['status' => 'rejected', 'approved_at' => null]);

        return back()->with('success', 'Pengajuan ditolak.');
    }

    private function formData(Request $request): array
    {
        return [
            'accounts' => $request->user()->accounts()->where('is_active', true)->get(),
            'categories' => $request->user()->categories()->get(),
            'today' => Carbon::today()->toDateString(),
        ];
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'account_id' => ['required', 'exists:accounts,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function authorizeOwner(Transaction $transaction): void
    {
        abort_unless($transaction->user_id === request()->user()->id, 403);
    }

    /**
     * Cek apakah expense melebihi budget kategori pada bulan transaksi.
     * Mengembalikan pesan error bila melebihi, atau null bila aman.
     */
    private function budgetError(Request $request, array $data, ?int $ignoreId = null): ?string
    {
        if ($data['type'] !== 'expense' || empty($data['category_id'])) {
            return null;
        }

        $budget = $request->user()->budgets()->where('category_id', $data['category_id'])->first();
        if (! $budget) {
            return null; // kategori tanpa budget = tidak dibatasi
        }

        $start = Carbon::parse($data['date'])->startOfMonth();
        $end = Carbon::parse($data['date'])->endOfMonth();

        $spent = (float) $request->user()->transactions()
            ->where('category_id', $data['category_id'])
            ->where('type', 'expense')
            ->whereIn('status', ['approved', 'pending'])
            ->whereBetween('date', [$start, $end])
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->sum('amount');

        if ($spent + (float) $data['amount'] > (float) $budget->amount) {
            $sisa = max(0, (float) $budget->amount - $spent);
            $fmt = fn ($v) => 'Rp ' . number_format((float) $v, 0, ',', '.');

            return 'Melebihi budget! Budget bulan ini ' . $fmt($budget->amount)
                . ', sudah terpakai ' . $fmt($spent)
                . ', sisa hanya ' . $fmt($sisa) . '.';
        }

        return null;
    }
}
