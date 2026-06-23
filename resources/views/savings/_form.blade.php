@php($g = $goal ?? null)
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Nama Target</label>
        <input type="text" name="name" value="{{ old('name', $g->name ?? '') }}" required
               class="mt-1 w-full rounded-lg border-gray-300" placeholder="Contoh: Dana Darurat">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Target Total</label>
        <x-money-input name="target_amount" :value="old('target_amount', $g->target_amount ?? 0)" required />
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Frekuensi Wajib</label>
        <select name="frequency" class="mt-1 w-full rounded-lg border-gray-300">
            @foreach (['daily' => 'Harian', 'weekly' => 'Mingguan', 'monthly' => 'Bulanan'] as $val => $label)
                <option value="{{ $val }}" @selected(old('frequency', $g->frequency ?? 'monthly') === $val)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Nominal Wajib / Periode</label>
        <x-money-input name="amount_per_period" :value="old('amount_per_period', $g->amount_per_period ?? '')" required />
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Akun Penampung (opsional)</label>
        <select name="account_id" class="mt-1 w-full rounded-lg border-gray-300">
            <option value="">— Tidak ditautkan —</option>
            @foreach ($accounts as $acc)
                <option value="{{ $acc->id }}" @selected(old('account_id', $g->account_id ?? '') == $acc->id)>{{ $acc->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
        <input type="date" name="start_date" value="{{ old('start_date', isset($g) ? $g->start_date->toDateString() : ($today ?? now()->toDateString())) }}" required
               class="mt-1 w-full rounded-lg border-gray-300">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Target Selesai (opsional)</label>
        <input type="date" name="target_date" value="{{ old('target_date', isset($g) && $g->target_date ? $g->target_date->toDateString() : '') }}"
               class="mt-1 w-full rounded-lg border-gray-300">
    </div>
    @isset($g)
        <div>
            <label class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status" class="mt-1 w-full rounded-lg border-gray-300">
                @foreach (['active' => 'Aktif', 'paused' => 'Dijeda', 'completed' => 'Selesai'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('status', $g->status) === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    @endisset
</div>
<div class="mt-6 flex gap-3">
    <button class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2 rounded-lg">Simpan</button>
    <a href="{{ route('savings.index') }}" class="text-gray-500 hover:text-gray-700 text-sm px-3 py-2">Batal</a>
</div>
