<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Akun & Saldo</h2>
            <a href="{{ route('accounts.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg">+ Tambah Akun</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($accounts as $account)
                    <div class="bg-white rounded-xl shadow p-5">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-semibold text-gray-800">{{ $account->name }}</p>
                                <p class="text-xs uppercase tracking-wide text-gray-400">{{ $account->type }}</p>
                            </div>
                            @unless ($account->is_active)
                                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded">nonaktif</span>
                            @endunless
                        </div>
                        @if ($account->bank_name || $account->account_number)
                            <p class="mt-2 text-sm text-gray-500">{{ $account->bank_name }} {{ $account->account_number ? '· '.$account->account_number : '' }}</p>
                        @endif
                        <p class="mt-3 text-2xl font-bold text-gray-800">@rupiah($account->balance)</p>
                        <div class="mt-4 flex gap-3 text-sm">
                            <a href="{{ route('accounts.edit', $account) }}" class="text-indigo-600 hover:underline">Edit</a>
                            <form action="{{ route('accounts.destroy', $account) }}" method="POST" onsubmit="return confirm('Hapus akun ini? Semua transaksinya ikut terhapus.')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-400">Belum ada akun. Tambahkan akun bank / cash pertamamu.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
