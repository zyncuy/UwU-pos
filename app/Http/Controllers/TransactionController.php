<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $transactions = Transaction::with('details.product')->latest()->get();

        return view('transactions.index', compact('products', 'transactions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_ids'   => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'quantities'    => 'required|array',
            'quantities.*'  => 'integer|min:1',
            'pay_amount'    => 'required|numeric|min:0',
        ]);

        $totalPrice = 0;
        $itemsToSave = [];

        // Hitung total dan persiapkan data item
        foreach ($request->product_ids as $index => $productId) {
            $product = Product::findOrFail($productId);
            $qty = $request->quantities[$index];
            $subtotal = $product->price * $qty;
            $totalPrice += $subtotal;

            $itemsToSave[] = [
                'product'  => $product,
                'quantity' => $qty,
                'price'    => $product->price,
                'subtotal' => $subtotal,
            ];
        }

        // Cek uang bayar
        if ($request->pay_amount < $totalPrice) {
            return redirect()->back()->with('error', 'Uang pembayaran kurang!');
        }

        $changeAmount = $request->pay_amount - $totalPrice;

        // Simpan Transaksi Utama
        $transaction = Transaction::create([
            'user_id'       => auth()->id(),
            'total_price'   => $totalPrice,
            'pay_amount'    => $request->pay_amount,
            'change_amount' => $changeAmount,
        ]);

        // Simpan Detail Barang & Potong Stok
        foreach ($itemsToSave as $item) {
            TransactionDetail::create([
                'transaction_id' => $transaction->id,
                'product_id'     => $item['product']->id,
                'quantity'       => $item['quantity'],
                'price'          => $item['price'],
                'subtotal'       => $item['subtotal'],
            ]);

            if (isset($item['product']->stock)) {
                $item['product']->decrement('stock', $item['quantity']);
            }
        }

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil! Kembalian: Rp ' . number_format($changeAmount, 0, ',', '.'));
    }
}