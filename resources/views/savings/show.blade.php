<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $goal->name }}</h2>
            <div class="flex gap-3">
                <a href="{{ route('savings.edit', $goal) }}" class="text-sm text-indigo-600 hover:underline">Edit</a>
                <a href="{{ route('savings.index') }}" class="text-sm text-gray-500 hover:underline">← Kembali</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            {{-- Ringkasan --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl shadow p-5"><p class="text-sm text-gray-500">Terkumpul</p><p class="mt-1 text-xl font-bold text-indigo-600">@rupiah($goal->saved_amount)</p></div>
                <div class="bg-white rounded-xl shadow p-5"><p class="text-sm text-gray-500">Target</p><p class="mt-1 text-xl font-bold text-gray-800">@rupiah($goal->target_amount)</p></div>
                <div class="bg-white rounded-xl shadow p-5"><p class="text-sm text-gray-500">Target s/d hari ini</p><p class="mt-1 text-xl font-bold text-gray-800">@rupiah($goal->expected_amount)</p></div>
                <div class="bg-white rounded-xl shadow p-5"><p class="text-sm text-gray-500">Tunggakan</p><p class="mt-1 text-xl font-bold {{ $goal->arrears > 0 ? 'text-red-600' : 'text-green-600' }}">@rupiah($goal->arrears)</p></div>
            </div>

            <div class="bg-white rounded-xl shadow p-5">
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-500">Progress</span>
                    <span class="font-medium text-gray-700">{{ $goal->progress_percent }}%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3">
                    <div class="bg-indigo-500 h-3 rounded-full" style="width: {{ $goal->progress_percent }}%"></div>
                </div>
            </div>

            {{-- Form setoran --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Catat Setoran</h3>
                <form action="{{ route('savings.deposits.store', $goal) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                        <x-money-input name="amount" :value="$goal->amount_per_period" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                        <input type="date" name="date" value="{{ $today }}" required class="mt-1 w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Dari Akun</label>
                        <select name="account_id" class="mt-1 w-full rounded-lg border-gray-300">
                            <option value="">— Tidak potong saldo —</option>
                            @foreach ($accounts as $acc)
                                <option value="{{ $acc->id }}" @selected($goal->account_id == $acc->id)>{{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg">Setor</button>
                </form>
                <p class="text-xs text-gray-400 mt-2">Memilih akun akan mengurangi saldo akun tersebut sebesar setoran.</p>
            </div>

            {{-- Riwayat setoran --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Riwayat Setoran</h3>
                <table class="min-w-full text-sm">
                    <thead class="text-left text-gray-400 border-b">
                        <tr><th class="py-2">Tanggal</th><th>Catatan</th><th>Dari Akun</th><th class="text-right">Jumlah</th><th></th></tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($goal->deposits as $d)
                            <tr>
                                <td class="py-2 text-gray-600">{{ $d->date->format('d M Y') }}</td>
                                <td class="text-gray-700">{{ $d->note ?: '-' }}</td>
                                <td class="text-gray-600">{{ $d->account->name ?? '-' }}</td>
                                <td class="text-right font-medium text-indigo-600">@rupiah($d->amount)</td>
                                <td class="text-right">
                                    <form action="{{ route('savings.deposits.destroy', [$goal, $d]) }}" method="POST"
                                          data-confirm="Setoran ini akan dihapus dan saldo akun dikembalikan."
                                          data-confirm-title="Hapus setoran?" data-confirm-color="#dc2626" data-confirm-yes="Ya, hapus">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:underline text-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 text-center text-gray-400">Belum ada setoran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
