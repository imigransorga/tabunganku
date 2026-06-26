<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Target Tabungan</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-flash />
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <form action="{{ route('savings.update', $goal) }}" method="POST">
                    @csrf @method('PUT')
                    @include('savings._form')
                </form>
            </div>

            <form action="{{ route('savings.destroy', $goal) }}" method="POST" class="mt-4"
                  data-confirm="Target tabungan & seluruh riwayat setorannya akan dihapus."
                  data-confirm-title="Hapus target tabungan?" data-confirm-color="#dc2626" data-confirm-yes="Ya, hapus">
                @csrf @method('DELETE')
                <button class="text-sm text-red-600 hover:underline">Hapus target tabungan ini</button>
            </form>
        </div>
    </div>
</x-app-layout>
