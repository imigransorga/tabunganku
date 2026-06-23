<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Target Tabungan Baru</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-flash />
            <div class="bg-white rounded-xl shadow p-6">
                <form action="{{ route('savings.store') }}" method="POST">
                    @csrf
                    @include('savings._form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
