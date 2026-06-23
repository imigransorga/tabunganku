<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Transaksi</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-flash />
            <div class="bg-white rounded-xl shadow p-6">
                <form action="{{ route('transactions.update', $transaction) }}" method="POST">
                    @csrf @method('PUT')
                    @include('transactions._form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
