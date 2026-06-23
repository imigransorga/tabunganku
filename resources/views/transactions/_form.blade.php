@php($t = $transaction ?? null)
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">Tipe</label>
        <select name="type" class="mt-1 w-full rounded-lg border-gray-300">
            <option value="expense" @selected(old('type', $t->type ?? 'expense') === 'expense')>Pengeluaran</option>
            <option value="income" @selected(old('type', $t->type ?? '') === 'income')>Pemasukan</option>
        </select>
        <p class="text-xs text-gray-400 mt-1">Pengeluaran masuk sebagai pengajuan (pending) sampai disetujui.</p>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Jumlah</label>
        <x-money-input name="amount" :value="old('amount', $t->amount ?? '')" required />
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Akun</label>
        <select name="account_id" required class="mt-1 w-full rounded-lg border-gray-300">
            @foreach ($accounts as $acc)
                <option value="{{ $acc->id }}" @selected(old('account_id', $t->account_id ?? '') == $acc->id)>{{ $acc->name }} (@rupiah($acc->balance))</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Kategori</label>
        <select name="category_id" class="mt-1 w-full rounded-lg border-gray-300">
            <option value="">— Tanpa kategori —</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" data-type="{{ $cat->type }}" @selected(old('category_id', $t->category_id ?? '') == $cat->id)>{{ $cat->name }} ({{ $cat->type }})</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Tanggal</label>
        <input type="date" name="date" value="{{ old('date', isset($t) ? $t->date->toDateString() : $today) }}" required
               class="mt-1 w-full rounded-lg border-gray-300">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Keterangan</label>
        <input type="text" name="description" value="{{ old('description', $t->description ?? '') }}"
               class="mt-1 w-full rounded-lg border-gray-300" placeholder="Opsional">
    </div>
</div>
<div class="mt-6 flex gap-3">
    <button class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2 rounded-lg">Simpan</button>
    <a href="{{ route('transactions.index') }}" class="text-gray-500 hover:text-gray-700 text-sm px-3 py-2">Batal</a>
</div>
