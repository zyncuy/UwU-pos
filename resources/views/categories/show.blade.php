<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Header & Tombol Kembali -->
            <div class="flex justify-between items-center bg-white p-6 rounded-lg shadow-sm">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Kategori: {{ $category->name }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ $category->description ?? 'Tidak ada keterangan.' }}</p>
                </div>
                <a href="{{ route('categories.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg text-sm transition">
                    &larr; Kembali ke Daftar Kategori
                </a>
            </div>

            <!-- Tabel Barang dalam Kategori Ini -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-md font-bold text-gray-800 mb-4">Daftar Barang dalam Kategori Ini</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <th class="py-3 px-4 text-center w-12">NO</th>
                                <th class="py-3 px-4">NAMA BARANG</th>
                                <th class="py-3 px-4">HARGA</th>
                                <th class="py-3 px-4 text-center">STOK</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                            @forelse($category->products as $index => $product)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-3.5 px-4 text-center font-medium text-gray-500">{{ $index + 1 }}</td>
                                    <td class="py-3.5 px-4 font-semibold text-gray-900">{{ $product->name }}</td>
                                    <td class="py-3.5 px-4 font-semibold text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-4 text-center font-medium">{{ $product->stock }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-6 text-gray-400">Belum ada barang di dalam kategori ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>