<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-r-lg shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        Manajemen Kategori
                    </h2>
                    <a href="{{ route('categories.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg text-sm shadow transition">
                        + Tambah Kategori
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <th class="py-3 px-4 text-center w-16">No</th>
                                <th class="py-3 px-4">Nama Kategori</th>
                                <th class="py-3 px-4">Keterangan</th>
                                <th class="py-3 px-4 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                            @forelse($categories as $index => $category)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-3.5 px-4 text-center font-medium text-gray-500">{{ $index + 1 }}</td>
                                    <td class="py-3.5 px-4">
                                        <a href="{{ route('categories.show', $category->id) }}" class="text-blue-600 font-bold hover:underline" title="Klik untuk lihat barang di kategori ini">
                                            {{ $category->name }}
                                        </a>
                                    </td>
                                    <td class="py-3.5 px-4 text-gray-500">{{ $category->description ?? '-' }}</td>
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="flex justify-center items-center gap-2">
                                            <a href="{{ route('categories.edit', $category->id) }}" class="bg-amber-400 hover:bg-amber-500 text-white p-1.5 rounded-md transition" title="Edit Kategori">
                                                ✏️
                                            </a>
                                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white p-1.5 rounded-md transition" title="Hapus Kategori">
                                                    🗑️
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-6 text-gray-400">Belum ada kategori yang ditambahkan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>