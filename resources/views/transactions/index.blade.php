@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-bold mb-4">Transaksi Kasir</h2>

        {{-- Form Transaksi --}}
        <form action="{{ route('transactions.store') }}" method="POST">
            @csrf

            <div id="product-list" class="space-y-3 mb-4">
                {{-- Baris Produk Pertama --}}
                <div class="product-row" style="display: flex; gap: 10px; align-items: center;">
                    <select name="product_ids[]" class="product-select" required style="flex: 2; border: 1px solid #ccc; padding: 8px; border-radius: 4px;">
                        <option value="" data-price="0">-- Pilih Produk --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                {{ $product->name }} - Rp {{ number_format($product->price, 0, ',', '.') }} (Stok: {{ $product->stock }})
                            </option>
                        @endforeach
                    </select>

                    <input type="number" name="quantities[]" value="1" min="1" class="qty-input" required placeholder="Qty" style="width: 80px; border: 1px solid #ccc; padding: 8px; border-radius: 4px;">
                    
                    <button type="button" onclick="removeRow(this)" style="background-color: #dc3545; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer;">X</button>
                </div>
            </div>

            <button type="button" id="add-product-btn" style="background-color: #0d6efd; color: white; border: none; padding: 8px 16px; border-radius: 4px; margin-bottom: 20px; cursor: pointer;">+ Tambah Produk Lain</button>

            <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                <div style="flex: 1;">
                    <label class="block font-medium">Total Belanja (Rp)</label>
                    <input type="text" id="total-display" readonly value="Rp 0" style="width: 100%; border: 1px solid #ccc; padding: 8px; border-radius: 4px; background-color: #e9ecef; font-weight: bold;">
                </div>
                <div style="flex: 1;">
                    <label class="block font-medium">Uang Bayar (Rp)</label>
                    <input type="number" name="paid_amount" id="paid-amount" required placeholder="Contoh: 50000" style="width: 100%; border: 1px solid #ccc; padding: 8px; border-radius: 4px;">
                </div>
            </div>

            <div style="background-color: #e9ecef; padding: 12px; border-radius: 4px; text-align: right; margin-bottom: 15px; font-weight: bold;">
                Kembalian: <span id="change-display" style="color: #198754;">Rp 0</span>
            </div>

            <button type="submit" style="width: 100%; background-color: #198754; color: white; border: none; padding: 12px; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer;">Proses & Simpan Transaksi</button>
        </form>
    </div>

    {{-- Tabel Riwayat Transaksi --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold mb-4">Riwayat Transaksi Terakhir</h3>
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #dee2e6;">
                    <th style="padding: 8px;">No</th>
                    <th style="padding: 8px;">Tanggal</th>
                    <th style="padding: 8px;">Invoice</th>
                    <th style="padding: 8px;">Total</th>
                    <th style="padding: 8px;">Bayar</th>
                    <th style="padding: 8px;">Kembalian</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $index => $transaction)
                    <tr style="border-bottom: 1px solid #dee2e6;">
                        <td style="padding: 8px;">{{ $index + 1 }}</td>
                        <td style="padding: 8px;">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                        <td style="padding: 8px;">{{ $transaction->invoice_number }}</td>
                        <td style="padding: 8px;">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
                        <td style="padding: 8px;">Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</td>
                        <td style="padding: 8px;">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 16px; color: #6c757d;">Belum ada transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.product-row').forEach(row => {
            let select = row.querySelector('.product-select');
            let qty = row.querySelector('.qty-input').value;
            let price = select.options[select.selectedIndex]?.getAttribute('data-price') || 0;
            total += (parseFloat(price) * parseInt(qty || 0));
        });

        document.getElementById('total-display').value = 'Rp ' + total.toLocaleString('id-ID');

        let paid = parseFloat(document.getElementById('paid-amount').value) || 0;
        let change = paid - total;
        document.getElementById('change-display').innerText = 'Rp ' + (change >= 0 ? change.toLocaleString('id-ID') : 0);
    }

    document.addEventListener('change', calculateTotal);
    document.addEventListener('input', calculateTotal);

    document.getElementById('add-product-btn').addEventListener('click', function() {
        let firstRow = document.querySelector('.product-row');
        let newRow = firstRow.cloneNode(true);
        newRow.querySelector('.qty-input').value = 1;
        newRow.querySelector('.product-select').selectedIndex = 0;
        document.getElementById('product-list').appendChild(newRow);
        calculateTotal();
    });

    function removeRow(btn) {
        let rows = document.querySelectorAll('.product-row');
        if (rows.length > 1) {
            btn.closest('.product-row').remove();
            calculateTotal();
        }
    }
</script>
@endsection