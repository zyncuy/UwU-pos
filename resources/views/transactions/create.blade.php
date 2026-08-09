<x-app-layout>
    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Kasir / Transaksi Baru</h2>
                <a href="{{ route('transactions.index') }}" class="text-sm text-gray-600 hover:underline">&larr; Kembali</a>
            </div>

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('transactions.store') }}" method="POST" id="transactionForm">
                @csrf
                
                <!-- Filter & Input Produk -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6 border border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Filter Kategori</label>
                            <select id="filter_category" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Cari Nama Produk</label>
                            <select id="product_select" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Pilih Produk --</option>
                                @foreach($products as $prod)
                                    <option value="{{ $prod->id }}" data-category="{{ $prod->category_id }}" data-price="{{ $prod->price }}" data-stock="{{ $prod->stock }}">
                                        {{ $prod->name }} (Stok: {{ $prod->stock }}) - Rp {{ number_format($prod->price, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">QTY Beli</label>
                            <input type="number" id="qty_input" value="1" min="1" class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <button type="button" id="btn_add_item" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-md">
                                + Tambah
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabel Keranjang Belanja -->
                <div class="overflow-x-auto mb-6">
                    <table class="min-w-full divide-y divide-gray-200" id="cartTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga Unit</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Jumlah (QTY)</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="cartBody">
                            <tr id="emptyRow">
                                <td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada item yang dimasukkan ke transaksi.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Kalkulasi Harga, Pembayaran & Kembalian -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Total Harga (Rp)</label>
                        <input type="text" id="total_price_display" value="0" class="w-full bg-gray-100 border border-gray-300 rounded-md p-2 text-xl font-bold text-gray-800" readonly>
                        <input type="hidden" name="total_price" id="total_price" value="0">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Bayar (Rp)</label>
                        <input type="number" name="pay_amount" id="pay_amount" placeholder="0" min="0" class="w-full border border-gray-300 rounded-md p-2 text-xl font-bold text-gray-800 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kembalian (Rp)</label>
                        <input type="text" id="change_amount_display" value="0" class="w-full bg-gray-100 border border-gray-300 rounded-md p-2 text-xl font-bold text-green-600" readonly>
                    </div>
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-md text-lg">
                    Simpan & Selesaikan Transaksi
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let cart = [];
            const productSelect = document.getElementById('product_select');
            const categorySelect = document.getElementById('filter_category');
            const qtyInput = document.getElementById('qty_input');
            const btnAdd = document.getElementById('btn_add_item');
            const cartBody = document.getElementById('cartBody');
            const totalPriceDisplay = document.getElementById('total_price_display');
            const totalPriceInput = document.getElementById('total_price');
            const payAmountInput = document.getElementById('pay_amount');
            const changeDisplay = document.getElementById('change_amount_display');

            // Filter Kategori
            categorySelect.addEventListener('change', function () {
                const categoryId = this.value;
                Array.from(productSelect.options).forEach(option => {
                    if (!option.value) return;
                    option.style.display = (!categoryId || option.dataset.category === categoryId) ? '' : 'none';
                });
                productSelect.value = '';
            });

            // Tambah ke Keranjang
            btnAdd.addEventListener('click', function () {
                const selected = productSelect.options[productSelect.selectedIndex];
                if (!selected || !selected.value) return alert('Pilih produk terlebih dahulu.');

                const id = selected.value;
                const name = selected.text.split(' (Stok:')[0];
                const price = parseFloat(selected.dataset.price);
                const maxStock = parseInt(selected.dataset.stock);
                const qty = parseInt(qtyInput.value) || 1;

                const existingIndex = cart.findIndex(i => i.id === id);
                const currentQtyInCart = existingIndex > -1 ? cart[existingIndex].qty : 0;

                if (currentQtyInCart + qty > maxStock) {
                    return alert('Jumlah melebihi stok yang tersedia (' + maxStock + ')');
                }

                if (existingIndex > -1) {
                    cart[existingIndex].qty += qty;
                } else {
                    cart.push({ id, name, price, qty, maxStock });
                }

                qtyInput.value = 1;
                renderCart();
            });

            // Render Keranjang & Hitung Total
            function renderCart() {
                cartBody.innerHTML = '';
                let total = 0;

                if (cart.length === 0) {
                    cartBody.innerHTML = `<tr id="emptyRow"><td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada item yang dimasukkan ke transaksi.</td></tr>`;
                    totalPriceDisplay.value = '0';
                    totalPriceInput.value = 0;
                    updateChange();
                    return;
                }

                cart.forEach((item, index) => {
                    const subtotal = item.price * item.qty;
                    total += subtotal;

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="px-4 py-3 font-medium text-gray-900">${item.name}
                            <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
                            <input type="hidden" name="items[${index}][qty]" value="${item.qty}">
                        </td>
                        <td class="px-4 py-3">Rp ${new Intl.NumberFormat('id-ID').format(item.price)}</td>
                        <td class="px-4 py-3 text-center">${item.qty}</td>
                        <td class="px-4 py-3 font-bold">Rp ${new Intl.NumberFormat('id-ID').format(subtotal)}</td>
                        <td class="px-4 py-3 text-center">
                            <button type="button" class="text-red-600 hover:text-red-900 font-bold" onclick="removeItem(${index})">Hapus</button>
                        </td>
                    `;
                    cartBody.appendChild(tr);
                });

                totalPriceDisplay.value = new Intl.NumberFormat('id-ID').format(total);
                totalPriceInput.value = total;
                updateChange();
            }

            window.removeItem = function (index) {
                cart.splice(index, 1);
                renderCart();
            };

            // Hitung Kembalian Real-time
            function updateChange() {
                const total = parseFloat(totalPriceInput.value) || 0;
                const pay = parseFloat(payAmountInput.value) || 0;
                const change = pay - total;

                if (pay === 0) {
                    changeDisplay.value = '0';
                    changeDisplay.className = 'w-full bg-gray-100 border border-gray-300 rounded-md p-2 text-xl font-bold text-gray-800';
                } else if (change >= 0) {
                    changeDisplay.value = 'Rp ' + new Intl.NumberFormat('id-ID').format(change);
                    changeDisplay.className = 'w-full bg-gray-100 border border-gray-300 rounded-md p-2 text-xl font-bold text-green-600';
                } else {
                    changeDisplay.value = 'Uang Kurang';
                    changeDisplay.className = 'w-full bg-gray-100 border border-gray-300 rounded-md p-2 text-xl font-bold text-red-600';
                }
            }

            payAmountInput.addEventListener('input', updateChange);
        });
    </script>
</x-app-layout>