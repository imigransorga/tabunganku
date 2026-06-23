<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kategori</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            {{-- Form tambah --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Tambah Kategori</h3>
                <form action="{{ route('categories.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                    @csrf
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Nama</label>
                        <input type="text" name="name" required class="mt-1 w-full rounded-lg border-gray-300" placeholder="Gaji / Makan / Transport">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipe</label>
                        <select name="type" class="mt-1 w-full rounded-lg border-gray-300">
                            <option value="expense">Pengeluaran</option>
                            <option value="income">Pemasukan</option>
                        </select>
                    </div>
                    <div class="flex gap-2 items-end">
                        <input type="color" name="color" value="#6366f1" class="h-10 w-12 rounded border-gray-300">
                        <button class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg">Tambah</button>
                    </div>
                </form>
            </div>

            {{-- Daftar --}}
            <div class="bg-white rounded-xl shadow p-6">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-gray-400 border-b">
                        <tr><th class="py-2">Kategori</th><th>Tipe</th><th class="text-right">Aksi</th></tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($categories as $c)
                            <tr x-data="{ edit: false }">
                                <td class="py-2">
                                    <div x-show="!edit" class="flex items-center gap-2">
                                        <span class="inline-block w-3 h-3 rounded-full" style="background: {{ $c->color }}"></span>
                                        {{ $c->name }}
                                    </div>
                                    <form x-show="edit" x-cloak action="{{ route('categories.update', $c) }}" method="POST" class="flex gap-2 items-center">
                                        @csrf @method('PUT')
                                        <input type="text" name="name" value="{{ $c->name }}" class="rounded border-gray-300 text-sm">
                                        <select name="type" class="rounded border-gray-300 text-sm">
                                            <option value="expense" @selected($c->type === 'expense')>Pengeluaran</option>
                                            <option value="income" @selected($c->type === 'income')>Pemasukan</option>
                                        </select>
                                        <input type="color" name="color" value="{{ $c->color }}" class="h-8 w-10 rounded border-gray-300">
                                        <button class="text-indigo-600 hover:underline">Simpan</button>
                                    </form>
                                </td>
                                <td>
                                    <span class="text-xs px-2 py-0.5 rounded {{ $c->type === 'income' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $c->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <button @click="edit = !edit" class="text-indigo-600 hover:underline text-sm">Edit</button>
                                    <form action="{{ route('categories.destroy', $c) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kategori ini?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:underline text-sm ms-2">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center text-gray-400">Belum ada kategori.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
