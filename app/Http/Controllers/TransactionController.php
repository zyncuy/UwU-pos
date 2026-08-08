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

        // Ambil daftar produk milik user yang login untuk pilihan kasir
        $products = Product::where('user_id', auth()->id())->get();

        // Kirim $transactions DAN $products ke halaman view
        return view('transactions.index', compact('transactions', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'total_price' => 'required|numeric',
            'paid_amount' => 'required|numeric',
            'change_amount' => 'required|numeric',
        ]);

        Transaction::create([
            'user_id' => auth()->id(),
            'invoice_number' => 'INV-' . time(),
            'total_price' => $request->total_price,
            'paid_amount' => $request->paid_amount,
            'change_amount' => $request->change_amount,
        ]);

        return redirect()->back()->with('success', 'Transaksi berhasil disimpan!');
    }
}