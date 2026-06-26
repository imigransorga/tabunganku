<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Transaksi</h2>
            <a href="{{ route('transactions.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg">+ Transaksi Baru</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            {{-- Filter --}}
            <form method="GET" class="bg-white rounded-xl shadow p-4 flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs text-gray-500">Tipe</label>
                    <select name="type" class="rounded-lg border-gray-300 text-sm">
                        <option value="">Semua</option>
                        <option value="income" @selected(request('type') === 'income')>Pemasukan</option>
                        <option value="expense" @selected(request('type') === 'expense')>Pengeluaran</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500">Status</label>
                    <select name="status" class="rounded-lg border-gray-300 text-sm">
                        <option value="">Semua</option>
                        @foreach (['pending', 'approved', 'rejected'] as $s)
                            <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="bg-gray-800 text-white text-sm px-4 py-2 rounded-lg">Filter</button>
                <a href="{{ route('transactions.index') }}" class="text-sm text-gray-500 px-2 py-2">Reset</a>
            </form>

            <div class="bg-white rounded-xl shadow overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-gray-400 border-b">
                        <tr>
                            <th class="py-3 px-4">Tanggal</th><th>Keterangan</th><th>Kategori</th>
                            <th>Akun</th><th>Status</th><th class="text-right">Jumlah</th><th class="text-right px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($transactions as $t)
                            <tr>
                                <td class="py-3 px-4 text-gray-600 whitespace-nowrap">{{ $t->date->format('d M Y') }}</td>
                                <td class="text-gray-700">{{ $t->description ?: '-' }}</td>
                                <td class="text-gray-600">{{ $t->category->name ?? '-' }}</td>
                                <td class="text-gray-600">{{ $t->account->name ?? '-' }}</td>
                                <td>
                                    @php $map = ['pending' => 'bg-amber-100 text-amber-700', 'approved' => 'bg-green-100 text-green-700', 'rejected' => 'bg-gray-100 text-gray-500']; @endphp
                                    <span class="text-xs px-2 py-0.5 rounded {{ $map[$t->status] }}">{{ ucfirst($t->status) }}</span>
                                </td>
                                <td class="text-right font-medium {{ $t->type === 'income' ? 'text-green-600' : 'text-red-600' }} whitespace-nowrap">
                                    {{ $t->type === 'income' ? '+' : '-' }}@rupiah($t->amount)
                                </td>
                                <td class="text-right px-4 whitespace-nowrap">
                                    @if ($t->status === 'pending')
                                        <form action="{{ route('transactions.approve', $t) }}" method="POST" class="inline"
                                              data-confirm="Setujui pengeluaran ini? Saldo akun akan berkurang."
                                              data-confirm-title="Setujui pengajuan?" data-confirm-icon="question"
                                              data-confirm-color="#16a34a" data-confirm-yes="Ya, setujui">
                                            @csrf @method('PATCH')
                                            <button class="text-green-600 hover:underline">Setujui</button>
                                        </form>
                                        <form action="{{ route('transactions.reject', $t) }}" method="POST" class="inline ms-1"
                                              data-confirm="Tolak pengajuan pengeluaran ini?"
                                              data-confirm-title="Tolak pengajuan?" data-confirm-color="#d97706" data-confirm-yes="Ya, tolak">
                                            @csrf @method('PATCH')
                                            <button class="text-amber-600 hover:underline">Tolak</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('transactions.edit', $t) }}" class="text-indigo-600 hover:underline ms-1">Edit</a>
                                    <form action="{{ route('transactions.destroy', $t) }}" method="POST" class="inline ms-1"
                                          data-confirm="Transaksi yang dihapus tidak bisa dikembalikan."
                                          data-confirm-title="Hapus transaksi?" data-confirm-color="#dc2626" data-confirm-yes="Ya, hapus">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-6 text-center text-gray-400">Belum ada transaksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $transactions->links() }}</div>
        </div>
    </div>
</x-app-layout>
