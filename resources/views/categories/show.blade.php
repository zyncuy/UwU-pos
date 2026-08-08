<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="flex justify-between items-center bg-white p-6 rounded-lg shadow-sm">
                <div>
                    <a href="{{ route('categories.index') }}" class="text-sm text-blue-600 hover:underline flex items-center gap-1 mb-1">
                        ← Kembali ke Daftar Kategori
                    </a>
                    <h2 class="text-2xl font-bold text-gray-800">
                        Kategori: <span class="text-blue-600">{{ $category->name }}</span>
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">{{ $category->description ?? 'Tidak ada keterangan.' }}</p>
                </div>
                <div class="text-right">
                    <span class="text-sm text-gray-500">Total Barang</span>
                    <p class="text-2xl font-bold text-gray-800">{{ $products->count() }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <th class="py-3.5 px-4 text-center w-16">No</th>
                                <th class="py-3.5 px-4">Nama Barang</th>
                                <th class="py-3.5 px-4 text-right w-40">Harga</th>
                                <th class="py-3.5 px-4 text-center w-28">Stok</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                            @forelse($products as $index => $product)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-3.5 px-4 text-center font-medium text-gray-500">{{ $index + 1 }}</td>
                                    <td class="py-3.5 px-4 font-semibold text-gray-900">{{ $product->name }}</td>
                                    <td class="py-3.5 px-4 text-right font-semibold text-gray-900">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-2.5 py-1 text-xs rounded-full font-bold {{ $product->stock > 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $product->stock }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-8 text-gray-400">
                                        Belum ada barang di dalam kategori {{ $category->name }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>