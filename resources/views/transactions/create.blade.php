<x-app-layout>
    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl p-6">
                
                <div class="flex justify-between items-center mb-6 border-b pb-4">
                    <h2 class="text-2xl font-bold text-gray-800">Kasir / Transaksi Baru</h2>
                    <a href="{{ route('transactions.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-semibold">
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
                    
                    <!-- Panel Pencarian & Filter -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-gray-200 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                            
                            <!-- Filter Kategori -->
                            <div class="md:col-span-3">
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Filter Kategori</label>
                                <select id="filter-category" class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Semua Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Input Cari Produk (Autocomplete) -->
                            <div class="md:col-span-5 relative">
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Cari Nama Produk</label>
                                <input type="text" id="product-search" placeholder="Ketik nama produk..." autocomplete="off" class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <input type="hidden" id="selected-product-id">
                                
                                <!-- Dropdown Suggestion -->
                                <div id="search-dropdown" class="absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl z-50 max-h-60 overflow-y-auto hidden"></div>
                            </div>

                            <!-- Input Jumlah Beli awal -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Qty Beli</label>
                                <input type="number" id="input-qty" value="1" min="1" class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <!-- Tombol Tambah -->
                            <div class="md:col-span-2">
                                <button type="button" id="btn-add-item" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-lg text-sm transition">
                                    + Tambah
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Keranjang Belanja -->
                    <div class="overflow-x-auto border rounded-xl mb-6">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-100 text-xs font-bold text-gray-600 uppercase border-b">
                                    <th class="py-3 px-4">Produk</th>
                                    <th class="py-3 px-4">Harga Unit</th>
                                    <th class="py-3 px-4 text-center w-40">Jumlah (Qty)</th>
                                    <th class="py-3 px-4">Subtotal</th>
                                    <th class="py-3 px-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="cart-table" class="divide-y divide-gray-200 text-sm">
                                <tr id="empty-row">
                                    <td colspan="5" class="text-center py-8 text-gray-400">Belum ada item yang dimasukkan ke transaksi.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Ringkasan Pembayaran -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Total Harga (Rp)</label>
                            <input type="number" id="total_price" name="total_price" readonly value="0" class="w-full bg-gray-100 border-gray-300 rounded-lg text-xl font-extrabold text-indigo-700">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Jumlah Bayar (Rp)</label>
                            <input type="number" id="pay_amount" name="pay_amount" required placeholder="0" class="w-full border-gray-300 rounded-lg text-xl font-bold focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <button type="submit" class="w-full mt-6 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl shadow-md transition text-lg">
                        Simpan & Selesaikan Transaksi
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Script Autocomplete & Dynamic Cart -->
    <script>
        const products = @json($products);
        let selectedProduct = null;
        let itemIndex = 0;

        const productSearch = document.getElementById('product-search');
        const searchDropdown = document.getElementById('search-dropdown');
        const filterCategory = document.getElementById('filter-category');
        const inputQty = document.getElementById('input-qty');
        const btnAddItem = document.getElementById('btn-add-item');
        const cartTable = document.getElementById('cart-table');
        const emptyRow = document.getElementById('empty-row');
        const totalPriceInput = document.getElementById('total_price');

        // Autocomplete search + filter kategori
        function renderDropdown() {
            const query = productSearch.value.toLowerCase();
            const catId = filterCategory.value;

            const filtered = products.filter(p => {
                const matchName = p.name.toLowerCase().includes(query);
                const matchCat = catId ? p.category_id == catId : true;
                return matchName && matchCat;
            });

            if (filtered.length === 0) {
                searchDropdown.innerHTML = `<div class="p-3 text-sm text-gray-400">Produk tidak ditemukan</div>`;
            } else {
                searchDropdown.innerHTML = filtered.map(p => `
                    <div class="p-3 hover:bg-indigo-50 cursor-pointer border-b border-gray-100 last:border-0 dropdown-item" 
                         data-id="${p.id}" data-name="${p.name}" data-price="${p.price}" data-stock="${p.stock}">
                        <div class="font-semibold text-gray-800">${p.name}</div>
                        <div class="text-xs text-gray-500">Rp ${parseInt(p.price).toLocaleString('id-ID')} | Stok: ${p.stock}</div>
                    </div>
                `).join('');
            }
            searchDropdown.classList.remove('hidden');
        }

        productSearch.addEventListener('focus', renderDropdown);
        productSearch.addEventListener('input', renderDropdown);
        filterCategory.addEventListener('change', renderDropdown);

        document.addEventListener('click', function(e) {
            if (!productSearch.contains(e.target) && !searchDropdown.contains(e.target)) {
                searchDropdown.classList.add('hidden');
            }
        });

        // Pilih item dari dropdown
        searchDropdown.addEventListener('click', function(e) {
            const item = e.target.closest('.dropdown-item');
            if (!item) return;

            selectedProduct = {
                id: item.dataset.id,
                name: item.dataset.name,
                price: parseFloat(item.dataset.price),
                stock: parseInt(item.dataset.stock)
            };

            productSearch.value = selectedProduct.name;
            searchDropdown.classList.add('hidden');
        });

        // Tambahkan ke tabel keranjang
        btnAddItem.addEventListener('click', function() {
            if (!selectedProduct) return alert('Silakan pilih produk terlebih dahulu dari list!');
            const qty = parseInt(inputQty.value) || 1;

            if (qty > selectedProduct.stock) {
                return alert(`Stok tidak cukup! Maksimal stok: ${selectedProduct.stock}`);
            }

            if (emptyRow) emptyRow.style.display = 'none';

            const subtotal = selectedProduct.price * qty;
            const tr = document.createElement('tr');
            tr.className = 'cart-item';
            tr.dataset.price = selectedProduct.price;

            tr.innerHTML = `
                <td class="py-3 px-4 font-bold text-gray-800">${selectedProduct.name}</td>
                <td class="py-3 px-4">Rp ${selectedProduct.price.toLocaleString('id-ID')}</td>
                <td class="py-3 px-4 text-center">
                    <input type="hidden" name="items[${itemIndex}][product_id]" value="${selectedProduct.id}">
                    <div class="flex items-center justify-center gap-1">
                        <button type="button" class="btn-minus bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold px-2 py-1 rounded">-</button>
                        <input type="number" name="items[${itemIndex}][quantity]" value="${qty}" min="1" max="${selectedProduct.stock}" class="w-16 text-center border-gray-300 rounded-lg text-sm qty-input">
                        <button type="button" class="btn-plus bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold px-2 py-1 rounded">+</button>
                    </div>
                </td>
                <td class="py-3 px-4 font-bold text-indigo-600 subtotal">Rp ${subtotal.toLocaleString('id-ID')}</td>
                <td class="py-3 px-4 text-center">
                    <button type="button" class="btn-remove text-red-500 hover:text-red-700 font-bold text-xs uppercase">Hapus</button>
                </td>
            `;

            cartTable.appendChild(tr);
            itemIndex++;

            // Reset input
            productSearch.value = '';
            selectedProduct = null;
            inputQty.value = 1;

            updateTotal();

            // Handler Ubah Qty di Tabel
            const qtyInput = tr.querySelector('.qty-input');
            const btnMinus = tr.querySelector('.btn-minus');
            const btnPlus = tr.querySelector('.btn-plus');

            function updateRowSubtotal() {
                let currentQty = parseInt(qtyInput.value) || 1;
                const newSubtotal = tr.dataset.price * currentQty;
                tr.querySelector('.subtotal').innerText = 'Rp ' + newSubtotal.toLocaleString('id-ID');
                updateTotal();
            }

            btnMinus.addEventListener('click', function() {
                if (parseInt(qtyInput.value) > 1) {
                    qtyInput.value = parseInt(qtyInput.value) - 1;
                    updateRowSubtotal();
                }
            });

            btnPlus.addEventListener('click', function() {
                if (parseInt(qtyInput.value) < parseInt(qtyInput.max)) {
                    qtyInput.value = parseInt(qtyInput.value) + 1;
                    updateRowSubtotal();
                }
            });

            qtyInput.addEventListener('input', updateRowSubtotal);

            // Handler Hapus Item
            tr.querySelector('.btn-remove').addEventListener('click', function() {
                tr.remove();
                if (cartTable.querySelectorAll('.cart-item').length === 0 && emptyRow) {
                    emptyRow.style.display = '';
                }
                updateTotal();
            });
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
    </script>
</x-app-layout>