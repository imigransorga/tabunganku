<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tabungan</h2>
            <a href="{{ route('savings.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg">+ Target Baru</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse ($goals as $goal)
                    @php($freq = ['daily' => 'Harian', 'weekly' => 'Mingguan', 'monthly' => 'Bulanan'][$goal->frequency])
                    <div class="bg-white rounded-xl shadow p-5">
                        <div class="flex justify-between items-start">
                            <div>
                                <a href="{{ route('savings.show', $goal) }}" class="font-semibold text-gray-800 hover:text-indigo-600">{{ $goal->name }}</a>
                                <p class="text-xs text-gray-400">Wajib {{ $freq }} · @rupiah($goal->amount_per_period) / periode</p>
                            </div>
                            <span class="text-xs px-2 py-0.5 rounded {{ $goal->status === 'completed' ? 'bg-green-100 text-green-700' : ($goal->status === 'paused' ? 'bg-gray-100 text-gray-500' : 'bg-indigo-100 text-indigo-700') }}">
                                {{ ucfirst($goal->status) }}
                            </span>
                        </div>

                        <div class="mt-4">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-500">@rupiah($goal->saved_amount) / @rupiah($goal->target_amount)</span>
                                <span class="font-medium text-gray-700">{{ $goal->progress_percent }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2.5">
                                <div class="bg-indigo-500 h-2.5 rounded-full" style="width: {{ $goal->progress_percent }}%"></div>
                            </div>
                        </div>

                        @if ($goal->arrears > 0)
                            <p class="mt-3 text-sm text-red-600">Tunggakan setoran: <b>@rupiah($goal->arrears)</b></p>
                        @else
                            <p class="mt-3 text-sm text-green-600">Setoran wajib up-to-date ✔</p>
                        @endif

                        <a href="{{ route('savings.show', $goal) }}" class="mt-4 inline-block text-sm text-indigo-600 hover:underline">Detail & setor →</a>
                    </div>
                @empty
                    <p class="text-gray-400">Belum ada target tabungan. Buat target tabungan wajib (harian/mingguan/bulanan).</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
