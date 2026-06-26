<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Budget Pengeluaran</h2>
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $month }}</span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            <p class="text-sm text-gray-500 dark:text-gray-400">
                Bagi-bagi jatah pengeluaranmu per kategori. Kalau transaksi melebihi jatahnya, otomatis ditolak. 💡
            </p>

            {{-- Ringkasan --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="rounded-2xl p-5 bg-gradient-to-br from-indigo-600 to-purple-600 text-white shadow-lg shadow-indigo-500/30">
                    <p class="text-sm text-indigo-100">Total Budget</p>
                    <p class="mt-1 text-2xl font-bold">@rupiah($totalBudget)</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm ring-1 ring-gray-100 dark:ring-gray-700 p-5">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Terpakai</p>
                    <p class="mt-1 text-2xl font-bold text-red-600 dark:text-red-400">@rupiah($totalSpent)</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm ring-1 ring-gray-100 dark:ring-gray-700 p-5">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Sisa</p>
                    <p class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400">@rupiah(max(0, $totalBudget - $totalSpent))</p>
                </div>
            </div>

            {{-- Daftar kategori --}}
            <div class="space-y-3">
                @forelse ($categories as $cat)
                    @php
                        $b = $cat->budget;
                        $spent = $b ? $b->spentThisMonth() : 0;
                        $amount = $b ? (float) $b->amount : 0;
                        $pct = $b ? $b->progress_percent : 0;
                        $remaining = $b ? $b->remaining : 0;
                        $barColor = $pct >= 100 ? 'bg-red-500' : ($pct >= 80 ? 'bg-amber-500' : 'bg-green-500');
                    @endphp
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm ring-1 ring-gray-100 dark:ring-gray-700 p-5">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <span class="inline-block w-3 h-3 rounded-full" style="background: {{ $cat->color }}"></span>
                                <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $cat->name }}</span>
                            </div>

                            {{-- Form set budget --}}
                            <form action="{{ route('budgets.store') }}" method="POST" class="flex items-end gap-2">
                                @csrf
                                <input type="hidden" name="category_id" value="{{ $cat->id }}">
                                <div class="w-40">
                                    <x-money-input name="amount" :value="$amount ?: ''" placeholder="0" />
                                </div>
                                <button class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                                    {{ $b ? 'Ubah' : 'Atur' }}
                                </button>
                            </form>
                        </div>

                        @if ($b)
                            <div class="mt-4">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-500 dark:text-gray-400">Terpakai @rupiah($spent) dari @rupiah($amount)</span>
                                    <span class="font-medium {{ $remaining < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-200' }}">
                                        Sisa @rupiah(max(0, $remaining))
                                    </span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2.5">
                                    <div class="{{ $barColor }} h-2.5 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                                </div>
                                @if ($pct >= 100)
                                    <p class="mt-2 text-xs text-red-600 dark:text-red-400">⚠️ Budget habis — transaksi baru kategori ini akan ditolak.</p>
                                @endif
                            </div>
                        @else
                            <p class="mt-3 text-sm text-gray-400 dark:text-gray-500">Belum ada budget. Isi nominal lalu klik "Atur".</p>
                        @endif
                    </div>
                @empty
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm ring-1 ring-gray-100 dark:ring-gray-700 p-6 text-center text-gray-400 dark:text-gray-500">
                        Belum ada kategori pengeluaran. Buat dulu di menu <a href="{{ route('categories.index') }}" class="text-indigo-600 dark:text-indigo-400 underline">Kategori</a>.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
