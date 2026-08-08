<x-app-layout>
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6 border-b pb-4">
                    <h2 class="text-xl font-bold text-gray-800">Transaksi Baru</h2>
                    <a href="{{ route('transactions.index') }}" class="text-gray-600 hover:text-gray-800 text-sm font-semibold">
                        &larr; Kembali
                    </a>
                </div>

                @if(session('error'))
                    <div class="p-4 mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 text-sm rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('transactions.store') }}" method="POST">
                    @csrf
                    
                    <!-- Pilih Produk -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Produk</label>
                        <div class="flex gap-3">
                            <select id="select-product" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Pilih Produk --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}" data-stock="{{ $product->stock }}">
                                        {{ $product->name }} - Rp {{ number_format($product->price, 0, ',', '.') }} (Stok: {{ $product->stock }})
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" id="btn-add-item" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2 rounded-md text-sm transition">
                                + Tambah
                            </button>
                        </div>
                    </div>

                    <!-- Tabel Item Transaksi -->
                    <div class="mb-6 overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-100 border-b text-xs font-semibold text-gray-600 uppercase">
                                    <th class="py-3 px-4">Produk</th>
                                    <th class="py-3 px-4">Harga</th>
                                    <th class="py-3 px-4 w-32">Qty</th>
                                    <th class="py-3 px-4">Subtotal</th>
                                    <th class="py-3 px-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="cart-table" class="divide-y divide-gray-200 text-sm">
                                <tr>
                                    <td colspan="5" id="empty-row" class="text-center py-6 text-gray-400">Belum ada item yang dipilih.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Ringkasan Bayar -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 pt-4 border-t">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Total Harga (Rp)</label>
                            <input type="number" id="total_price" name="total_price" class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm font-bold text-lg" readonly value="0">
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Jumlah Bayar (Rp)</label>
                            <input type="number" id="pay_amount" name="pay_amount" class="w-full border-gray-300 rounded-md shadow-sm text-lg focus:ring-blue-500 focus:border-blue-500" required placeholder="0">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow transition">
                        Simpan Transaksi
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Script Hitung Otomatis -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectProduct = document.getElementById('select-product');
            const btnAddItem = document.getElementById('btn-add-item');
            const cartTable = document.getElementById('cart-table');
            const emptyRow = document.getElementById('empty-row');
            const totalPriceInput = document.getElementById('total_price');

            let itemIndex = 0;

            btnAddItem.addEventListener('click', function() {
                const selectedOption = selectProduct.options[selectProduct.selectedIndex];
                if (!selectedOption.value) return;

                const productId = selectedOption.value;
                const name = selectedOption.dataset.name;
                const price = parseFloat(selectedOption.dataset.price);
                const stock = parseInt(selectedOption.dataset.stock);

                if (emptyRow) emptyRow.style.display = 'none';

                const tr = document.createElement('tr');
                tr.className = 'cart-item';
                tr.innerHTML = `
                    <td class="py-3 px-4 font-semibold text-gray-800">${name}</td>
                    <td class="py-3 px-4">Rp ${price.toLocaleString('id-ID')}</td>
                    <td class="py-3 px-4">
                        <input type="hidden" name="items[${itemIndex}][product_id]" value="${productId}">
                        <input type="number" name="items[${itemIndex}][quantity]" value="1" min="1" max="${stock}" class="w-20 border-gray-300 rounded-md text-sm qty-input">
                    </td>
                    <td class="py-3 px-4 font-semibold subtotal">Rp ${price.toLocaleString('id-ID')}</td>
                    <td class="py-3 px-4 text-center">
                        <button type="button" class="btn-remove text-red-500 hover:text-red-700 font-bold text-xs">Hapus</button>
                    </td>
                `;

                tr.dataset.price = price;
                cartTable.appendChild(tr);
                itemIndex++;

                updateTotal();

                // Handler hitung subtotal saat qty diubah
                tr.querySelector('.qty-input').addEventListener('input', function() {
                    const qty = parseInt(this.value) || 0;
                    const subtotal = price * qty;
                    tr.querySelector('.subtotal').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
                    updateTotal();
                });

                // Handler hapus item
                tr.querySelector('.btn-remove').addEventListener('click', function() {
                    tr.remove();
                    if (cartTable.querySelectorAll('.cart-item').length === 0 && emptyRow) {
                        emptyRow.style.display = '';
                    }
                    updateTotal();
                });

                selectProduct.value = '';
            });

            function updateTotal() {
                let total = 0;
                document.querySelectorAll('.cart-item').forEach(tr => {
                    const price = parseFloat(tr.dataset.price);
                    const qty = parseInt(tr.querySelector('.qty-input').value) || 0;
                    total += price * qty;
                });
                totalPriceInput.value = total;
            }
        });
    </script>
</x-app-layout>