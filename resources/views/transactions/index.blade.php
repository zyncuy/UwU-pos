<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kasir Multi-Item') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div style="background-color: #d1e7dd; color: #0f5132; padding: 15px; border-radius: 6px; font-weight: bold;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="background-color: #f8d7da; color: #842029; padding: 15px; border-radius: 6px; font-weight: bold;">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Form Transaksi -->
            <div class="p-6 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-bold mb-4">Pilih Barang Belanjaan</h3>
                
                <form action="{{ route('transactions.store') }}" method="POST">
                    @csrf
                    
                    <div id="product-list" class="space-y-3 mb-4">
                        <!-- Baris Produk Pertama -->
                        <div class="product-row" style="display: flex; gap: 10px; align-items: center;">
                            <select name="product_ids[]" class="product-select" required style="flex: 2; border: 1px solid #ccc; padding: 8px; border-radius: 4px;" onchange="calculateTotal()">
                                <option value="" data-price="0">-- Pilih Produk --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                        {{ $product->name }} - Rp {{ number_format($product->price, 0, ',', '.') }} (Stok: {{ $product->stock }})
                                    </option>
                                @endforeach
                            </select>

                            <input type="number" name="quantities[]" value="1" min="1" class="qty-input" required placeholder="Qty" style="width: 80px; border: 1px solid #ccc; padding: 8px; border-radius: 4px;" oninput="calculateTotal()">

                            <button type="button" onclick="removeRow(this)" style="background-color: #dc3545; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer;">X</button>
                        </div>
                    </div>

                    <button type="button" onclick="addRow()" style="background-color: #0d6efd; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; margin-bottom: 20px;">
                        + Tambah Produk Lain
                    </button>

                    <hr style="margin-bottom: 20px;">

                    <div style="display: flex; gap: 15px; align-items: flex-end; margin-bottom: 20px;">
                        <div style="flex: 1;">
                            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Total Belanja (Rp)</label>
                            <input type="text" id="total_display" value="Rp 0" readonly style="width: 100%; border: 1px solid #ccc; padding: 8px; border-radius: 4px; background-color: #f0f0f0; font-weight: bold; font-size: 16px;">
                        </div>

                        <div style="flex: 1;">
                            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Uang Bayar (Rp)</label>
                            <input type="number" id="pay_amount" name="pay_amount" placeholder="Contoh: 50000" required style="width: 100%; border: 1px solid #ccc; padding: 8px; border-radius: 4px; font-size: 16px;" oninput="calculateTotal()">
                        </div>
                    </div>

                    <div style="background-color: #e2e3e5; padding: 12px; border-radius: 4px; text-align: right; font-size: 18px; margin-bottom: 15px;">
                        Kembalian: <strong id="change_display" style="color: #0d6efd;">Rp 0</strong>
                    </div>

                    <button type="submit" style="background-color: #198754; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; width: 100%; font-size: 16px;">
                        Proses & Simpan Transaksi
                    </button>
                </form>
            </div>

            <!-- Riwayat Transaksi -->
            <div class="p-6 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-bold mb-4">Riwayat Transaksi Terakhir</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #ddd; background-color: #f8f9fa; text-align: left;">
                            <th style="padding: 10px;">No</th>
                            <th style="padding: 10px;">Tanggal</th>
                            <th style="padding: 10px;">Item Dibeli</th>
                            <th style="padding: 10px;">Total</th>
                            <th style="padding: 10px;">Bayar</th>
                            <th style="padding: 10px;">Kembalian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $index => $trx)
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 10px;">{{ $index + 1 }}</td>
                                <td style="padding: 10px;">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                                <td style="padding: 10px;">
                                    <ul style="list-style-type: disc; padding-left: 15px;">
                                        @foreach($trx->details as $detail)
                                            <li>{{ $detail->product->name ?? 'Produk Dihapus' }} (x{{ $detail->quantity }})</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td style="padding: 10px; font-weight: bold;">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                                <td style="padding: 10px;">Rp {{ number_format($trx->pay_amount ?? 0, 0, ',', '.') }}</td>
                                <td style="padding: 10px; color: green; font-weight: bold;">Rp {{ number_format($trx->change_amount ?? 0, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 15px; text-align: center; color: #888;">Belum ada transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- Script JavaScript Dinamis -->
    <script>
        function addRow() {
            const container = document.getElementById('product-list');
            const firstRow = container.querySelector('.product-row');
            const newRow = firstRow.cloneNode(true);
            
            // Reset pilihan
            newRow.querySelector('.product-select').value = '';
            newRow.querySelector('.qty-input').value = 1;
            
            container.appendChild(newRow);
            calculateTotal();
        }

        function removeRow(button) {
            const rows = document.querySelectorAll('.product-row');
            if (rows.length > 1) {
                button.parentElement.remove();
                calculateTotal();
            } else {
                alert('Minimal harus ada 1 barang!');
            }
        }

        function calculateTotal() {
            let grandTotal = 0;
            const rows = document.querySelectorAll('.product-row');

            rows.forEach(row => {
                const select = row.querySelector('.product-select');
                const qtyInput = row.querySelector('.qty-input');
                
                const selectedOption = select.options[select.selectedIndex];
                const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
                const qty = parseInt(qtyInput.value) || 0;

                grandTotal += (price * qty);
            });

            const pay = parseFloat(document.getElementById('pay_amount').value) || 0;
            const change = pay - grandTotal;

            document.getElementById('total_display').value = 'Rp ' + grandTotal.toLocaleString('id-ID');
            
            const changeElement = document.getElementById('change_display');
            if (pay > 0 && change >= 0) {
                changeElement.innerText = 'Rp ' + change.toLocaleString('id-ID');
                changeElement.style.color = '#198754';
            } else if (pay > 0 && change < 0) {
                changeElement.innerText = 'Uang Kurang!';
                changeElement.style.color = '#dc3545';
            } else {
                changeElement.innerText = 'Rp 0';
                changeElement.style.color = '#0d6efd';
            }
        }
    </script>
</x-app-layout>