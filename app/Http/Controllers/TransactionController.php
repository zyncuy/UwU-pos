<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        // Ambil riwayat transaksi milik user yang login
        $transactions = Transaction::where('user_id', auth()->id())->latest()->get();

        // Ambil daftar produk milik user yang login
        $products = Product::where('user_id', auth()->id())->get();

        return view('transactions.index', compact('transactions', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'required|exists:products,id',
            'quantities' => 'required|array',
            'quantities.*' => 'required|integer|min:1',
            'paid_amount' => 'required|numeric',
        ]);

        // Hitung total belanja secara aman di Backend
        $totalPrice = 0;
        foreach ($request->product_ids as $index => $productId) {
            $product = Product::where('user_id', auth()->id())->find($productId);
            if ($product) {
                $qty = $request->quantities[$index] ?? 1;
                $totalPrice += ($product->price * $qty);

                // Potong stok produk
                $product->decrement('stock', $qty);
            }
        }

        $paidAmount = $request->paid_amount;
        $changeAmount = $paidAmount - $totalPrice;

        // Simpan transaksi
        Transaction::create([
            'user_id' => auth()->id(),
            'invoice_number' => 'INV-' . time(),
            'total_price' => $totalPrice,
            'paid_amount' => $paidAmount,
            'change_amount' => $changeAmount,
        ]);

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil disimpan!');
    }
}