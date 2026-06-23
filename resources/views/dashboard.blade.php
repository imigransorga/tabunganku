<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Dashboard') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            {{-- Kartu ringkasan --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl shadow p-5">
                    <p class="text-sm text-gray-500">Total Saldo</p>
                    <p class="mt-1 text-2xl font-bold text-gray-800">@rupiah($totalBalance)</p>
                </div>
                <div class="bg-white rounded-xl shadow p-5">
                    <p class="text-sm text-gray-500">Pemasukan (bulan ini)</p>
                    <p class="mt-1 text-2xl font-bold text-green-600">@rupiah($incomeMonth)</p>
                </div>
                <div class="bg-white rounded-xl shadow p-5">
                    <p class="text-sm text-gray-500">Pengeluaran (bulan ini)</p>
                    <p class="mt-1 text-2xl font-bold text-red-600">@rupiah($expenseMonth)</p>
                </div>
                <div class="bg-white rounded-xl shadow p-5">
                    <p class="text-sm text-gray-500">Total Tabungan</p>
                    <p class="mt-1 text-2xl font-bold text-indigo-600">@rupiah($totalSaved)</p>
                </div>
            </div>

            @if ($pendingCount > 0)
                <a href="{{ route('transactions.index', ['status' => 'pending']) }}"
                   class="block rounded-xl bg-amber-50 border border-amber-200 px-5 py-3 text-amber-800">
                    ⏳ Ada <b>{{ $pendingCount }}</b> pengajuan pengeluaran menunggu persetujuan. Klik untuk meninjau.
                </a>
            @endif

            {{-- Chart --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white rounded-xl shadow p-5 lg:col-span-2">
                    <h3 class="font-semibold text-gray-700 mb-4">Tren 6 Bulan (Pemasukan vs Pengeluaran)</h3>
                    <canvas id="trendChart" height="120"></canvas>
                </div>
                <div class="bg-white rounded-xl shadow p-5">
                    <h3 class="font-semibold text-gray-700 mb-4">Pengeluaran per Kategori</h3>
                    @if ($expenseByCategory->isEmpty())
                        <p class="text-sm text-gray-400">Belum ada pengeluaran bulan ini.</p>
                    @else
                        <canvas id="categoryChart" height="200"></canvas>
                    @endif
                </div>
            </div>

            {{-- Progress tabungan --}}
            <div class="bg-white rounded-xl shadow p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-700">Progress Tabungan</h3>
                    <a href="{{ route('savings.index') }}" class="text-sm text-indigo-600 hover:underline">Kelola →</a>
                </div>
                @forelse ($goals as $goal)
                    @php $pct = $goal->progress_percent; @endphp
                    <div class="mb-4 last:mb-0">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-medium text-gray-700">{{ $goal->name }}</span>
                            <span class="text-gray-500">@rupiah($goal->saved_amount) / @rupiah($goal->target_amount)</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="bg-indigo-500 h-2.5 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Belum ada target tabungan aktif.</p>
                @endforelse
            </div>

            {{-- Transaksi terbaru --}}
            <div class="bg-white rounded-xl shadow p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-700">Transaksi Terbaru</h3>
                    <a href="{{ route('transactions.index') }}" class="text-sm text-indigo-600 hover:underline">Lihat semua →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-gray-400 border-b">
                            <tr><th class="py-2">Tanggal</th><th>Keterangan</th><th>Kategori</th><th>Akun</th><th class="text-right">Jumlah</th></tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($recent as $t)
                                <tr>
                                    <td class="py-2 text-gray-600">{{ $t->date->format('d M Y') }}</td>
                                    <td class="text-gray-700">{{ $t->description ?: '-' }}
                                        @if ($t->status !== 'approved')
                                            <span class="text-xs px-1.5 py-0.5 rounded {{ $t->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500' }}">{{ $t->status }}</span>
                                        @endif
                                    </td>
                                    <td class="text-gray-600">{{ $t->category->name ?? '-' }}</td>
                                    <td class="text-gray-600">{{ $t->account->name ?? '-' }}</td>
                                    <td class="text-right font-medium {{ $t->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $t->type === 'income' ? '+' : '-' }}@rupiah($t->amount)
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-4 text-center text-gray-400">Belum ada transaksi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const trend = @json($trend);
        new Chart(document.getElementById('trendChart'), {
            type: 'bar',
            data: {
                labels: trend.map(t => t.label),
                datasets: [
                    { label: 'Pemasukan', data: trend.map(t => t.income), backgroundColor: '#22c55e' },
                    { label: 'Pengeluaran', data: trend.map(t => t.expense), backgroundColor: '#ef4444' },
                ]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        @if ($expenseByCategory->isNotEmpty())
        const cat = @json($expenseByCategory);
        new Chart(document.getElementById('categoryChart'), {
            type: 'doughnut',
            data: {
                labels: cat.map(c => c.label),
                datasets: [{ data: cat.map(c => c.total), backgroundColor: cat.map(c => c.color) }]
            },
            options: { responsive: true }
        });
        @endif
    </script>
</x-app-layout>
