@php($a = $account ?? null)
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Nama Akun</label>
        <input type="text" name="name" value="{{ old('name', $a->name ?? '') }}" required
               class="mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Contoh: BCA Utama">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Tipe</label>
        <select name="type" class="mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            @foreach (['bank' => 'Bank', 'cash' => 'Cash', 'ewallet' => 'E-Wallet', 'savings' => 'Tabungan'] as $val => $label)
                <option value="{{ $val }}" @selected(old('type', $a->type ?? 'bank') === $val)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Saldo Awal</label>
        <x-money-input name="opening_balance" :value="old('opening_balance', $a->opening_balance ?? 0)" required />
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Nama Bank (opsional)</label>
        <input type="text" name="bank_name" value="{{ old('bank_name', $a->bank_name ?? '') }}"
               class="mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="BCA / Mandiri / ...">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">No. Rekening (opsional)</label>
        <input type="text" name="account_number" value="{{ old('account_number', $a->account_number ?? '') }}"
               class="mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
    </div>
    <div class="sm:col-span-2">
        <label class="inline-flex items-center">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $a->is_active ?? true))
                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <span class="ms-2 text-sm text-gray-700">Akun aktif</span>
        </label>
    </div>
</div>
<div class="mt-6 flex gap-3">
    <button class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2 rounded-lg">Simpan</button>
    <a href="{{ route('accounts.index') }}" class="text-gray-500 hover:text-gray-700 text-sm px-3 py-2">Batal</a>
</div>
