<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-r-lg shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-r-lg shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- KIRI: Input Barang & Daftar Belanjaan -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">Transaksi Kasir</h2>
                        
                        <form action="{{ route('transactions.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="action" value="add_item">

                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                                <!-- Filter Kategori -->
                                <div class="md:col-span-4">
                                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Filter Kategori</label>
                                    <select id="category-filter" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 h-[42px]">
                                        <option value="">-- Semua Kategori --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Cari / Pilih Produk -->
                                <div class="md:col-span-5">
                                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Cari / Pilih Produk</label>
                                    <input type="hidden" name="product_id" id="selected-product-id" required>
                                    <input type="text" id="product-search" list="product-options" placeholder="Ketik nama produk..." class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 h-[42px]" autocomplete="off" required>
                                    
                                    <datalist id="product-options">
                                        @foreach($products as $product)
                                            <option data-id="{{ $product->id }}" data-category="{{ $product->category_id }}" value="{{ $product->name }} - Rp {{ number_format($product->price, 0, ',', '.') }} (Stok: {{ $product->stock }})"></option>
                                        @endforeach
                                    </datalist>
                                </div>

                                <!-- Jumlah -->
                                <div class="md:col-span-3 flex gap-2">
                                    <div class="w-1/2">
                                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">QTY</label>
                                        <input type="number" name="quantity" value="1" min="1" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 h-[42px]" required>
                                    </div>
                                    <div class="w-1/2 flex items-end">
                                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-sm shadow transition h-[42px]">
                                            + Tambah
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Tabel Daftar Belanjaan -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-md font-bold text-gray-800 mb-4">Daftar Belanjaan</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-100 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase">
                                        <th class="py-3 px-4">Barang</th>
                                        <th class="py-3 px-4 text-right">Harga</th>
                                        <th class="py-3 px-4 text-center">QTY</th>
                                        <th class="py-3 px-4 text-right">Subtotal</th>
                                        <th class="py-3 px-4 text-center w-16">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 text-sm">
                                    @php $grandTotal = 0; @endphp
                                    @forelse(session('cart', []) as $id => $item)
                                        @php 
                                            $subtotal = $item['price'] * $item['quantity'];
                                            $grandTotal += $subtotal;
                                        @endphp
                                        <tr class="hover:bg-gray-50">
                                            <td class="py-3 px-4 font-semibold text-gray-800">{{ $item['name'] }}</td>
                                            <td class="py-3 px-4 text-right">Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                                            <td class="py-3 px-4 text-center">{{ $item['quantity'] }}</td>
                                            <td class="py-3 px-4 text-right font-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                                            <td class="py-3 px-4 text-center">
                                                <form action="{{ route('transactions.destroy', $id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-xs bg-red-50 px-2 py-1 rounded">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-8 text-gray-400">Keranjang transaksi masih kosong.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- KANAN: Form Pembayaran -->
                <div class="space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-md font-bold text-gray-800 mb-4 border-b pb-2">Pembayaran</h3>

                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between items-center text-gray-600">
                                <span>Total Belanja:</span>
                                <span class="text-xl font-bold text-gray-900">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <form action="{{ route('transactions.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="action" value="checkout">
                            <input type="hidden" name="total_price" value="{{ $grandTotal }}">

                            <div>
                                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Uang Diterima (Rp)</label>
                                <input type="number" id="pay_amount" name="pay_amount" min="{{ $grandTotal }}" placeholder="0" class="w-full border-gray-300 rounded-lg text-lg font-bold text-gray-800 focus:ring-blue-500 focus:border-blue-500" required {{ empty(session('cart')) ? 'disabled' : '' }}>
                            </div>

                            <div class="p-3 bg-gray-50 rounded-lg flex justify-between items-center text-sm">
                                <span class="text-gray-600 font-medium">Kembalian:</span>
                                <span id="change_amount" class="text-lg font-bold text-green-600">Rp 0</span>
                            </div>

                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow transition disabled:opacity-50" {{ empty(session('cart')) ? 'disabled' : '' }}>
                                Selesaikan Transaksi
                            </button>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- JavaScript Logika Pencarian & Kembalian -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productSearch = document.getElementById('product-search');
            const hiddenProductId = document.getElementById('selected-product-id');
            const datalist = document.getElementById('product-options');
            const categoryFilter = document.getElementById('category-filter');

            // Tangkap ID produk saat user memilih/mengisi nama produk
            productSearch.addEventListener('input', function() {
                const options = datalist.querySelectorAll('option');
                let found = false;
                options.forEach(opt => {
                    if (opt.value === this.value) {
                        hiddenProductId.value = opt.getAttribute('data-id');
                        found = true;
                    }
                });
                if (!found) hiddenProductId.value = '';
            });

            // Filter opsi produk berdasarkan kategori
            categoryFilter.addEventListener('change', function() {
                const selectedCategory = this.value;
                const options = datalist.querySelectorAll('option');
                productSearch.value = '';
                hiddenProductId.value = '';

                options.forEach(opt => {
                    const categoryId = opt.getAttribute('data-category');
                    if (!selectedCategory || categoryId === selectedCategory) {
                        opt.disabled = false;
                    } else {
                        opt.disabled = true;
                    }
                });
            });

            // Hitung kembalian otomatis
            const payInput = document.getElementById('pay_amount');
            const changeOutput = document.getElementById('change_amount');
            const grandTotal = {{ $grandTotal }};

            if (payInput) {
                payInput.addEventListener('input', function() {
                    const payValue = parseFloat(this.value) || 0;
                    const change = payValue - grandTotal;
                    if (change >= 0) {
                        changeOutput.textContent = 'Rp ' + change.toLocaleString('id-ID');
                    } else {
                        changeOutput.textContent = 'Rp 0';
                    }
                });
            }
        });
    </script>
</x-app-layout>