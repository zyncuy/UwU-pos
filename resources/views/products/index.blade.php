<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-r-lg shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Form Tambah Barang -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Tambah Barang Baru</h2>
                <form action="{{ route('products.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Kategori</label>
                        <select name="category_id" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Nama Barang</label>
                        <input type="text" name="name" placeholder="Contoh: Es Teh" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Harga (Rp)</label>
                        <input type="number" name="price" placeholder="5000" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Stok</label>
                        <input type="number" name="stock" placeholder="10" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg text-sm shadow transition">
                            + Simpan Barang
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabel Daftar Barang -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <th class="py-3 px-4 text-center w-12">NO</th>
                                <th class="py-3 px-4">NAMA BARANG</th>
                                <th class="py-3 px-4">KATEGORI</th>
                                <th class="py-3 px-4">HARGA</th>
                                <th class="py-3 px-4 text-center">STOK</th>
                                <th class="py-3 px-4 text-center w-32">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                            @forelse($products as $index => $product)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-3.5 px-4 text-center font-medium text-gray-500">{{ $index + 1 }}</td>
                                    <td class="py-3.5 px-4 font-semibold text-gray-900">{{ $product->name }}</td>
                                    <td class="py-3.5 px-4 text-gray-600">{{ $product->category->name ?? '-' }}</td>
                                    <td class="py-3.5 px-4 font-semibold text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-4 text-center font-medium">{{ $product->stock }}</td>
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="flex justify-center items-center gap-3">
                                            <a href="{{ route('products.edit', $product->id) }}" class="text-amber-500 hover:text-amber-700 font-bold text-sm">
                                                Edit
                                            </a>
                                            <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus barang ini?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-sm">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-6 text-gray-400">Belum ada barang yang ditambahkan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>