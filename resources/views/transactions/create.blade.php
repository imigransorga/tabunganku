<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Transaksi Baru</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-flash />
            @if ($accounts->isEmpty())
                <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl p-4">
                    Buat <a href="{{ route('accounts.create') }}" class="underline">akun</a> dulu sebelum mencatat transaksi.
                </div>
            @else
                <div class="bg-white rounded-xl shadow p-6">
                    <form action="{{ route('transactions.store') }}" method="POST">
                        @csrf
                        @include('transactions._form')
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
