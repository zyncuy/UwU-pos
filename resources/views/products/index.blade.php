<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Barang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Alert Success -->
            @if(session('success'))
                <div style="background-color: #d1e7dd; color: #0f5132; padding: 12px; border-radius: 6px; margin-bottom: 15px;">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Form Tambah Barang -->
            <div class="p-6 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-bold mb-4">Tambah Barang Baru</h3>
                <form action="{{ route('products.store') }}" method="POST" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end;">
                    @csrf
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: bold;">Kategori</label>
                        <select name="category_id" required style="border: 1px solid #ccc; padding: 6px 10px; border-radius: 4px;">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 12px; font-weight: bold;">Nama Barang</label>
                        <input type="text" name="name" placeholder="Contoh: Es Teh" required style="border: 1px solid #ccc; padding: 6px 10px; border-radius: 4px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 12px; font-weight: bold;">Harga (Rp)</label>
                        <input type="number" name="price" placeholder="5000" required style="border: 1px solid #ccc; padding: 6px 10px; border-radius: 4px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 12px; font-weight: bold;">Stok</label>
                        <input type="number" name="stock" placeholder="10" required style="border: 1px solid #ccc; padding: 6px 10px; border-radius: 4px; width: 80px;">
                    </div>

                    <button type="submit" style="background-color: #0d6efd; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                        + Simpan Barang
                    </button>
                </form>
            </div>

            <!-- Tabel Daftar Barang -->
            <div class="p-6 bg-white shadow sm:rounded-lg">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="border-bottom: 2px solid #ddd; background-color: #f8f9fa;">
                            <th style="padding: 10px;">NO</th>
                            <th style="padding: 10px;">NAMA BARANG</th>
                            <th style="padding: 10px;">KATEGORI</th>
                            <th style="padding: 10px;">HARGA</th>
                            <th style="padding: 10px;">STOK</th>
                            <th style="padding: 10px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $index => $product)
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 10px;">{{ $index + 1 }}</td>
                                <td style="padding: 10px;">{{ $product->name }}</td>
                                <td style="padding: 10px;">{{ $product->category->name ?? '-' }}</td>
                                <td style="padding: 10px;">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                <td style="padding: 10px;">{{ $product->stock }}</td>
                                <td style="padding: 10px;">
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Hapus barang ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="color: red; background: none; border: none; cursor: pointer; text-decoration: underline;">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 15px; text-align: center; color: #888;">Belum ada data barang. Silakan isi form di atas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>