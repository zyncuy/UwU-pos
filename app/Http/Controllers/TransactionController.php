<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        // Hanya menampilkan transaksi milik user yang sedang login
        $transactions = Transaction::where('user_id', auth()->id())->latest()->get();

        return view('transactions.index', compact('transactions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'total_price' => 'required|numeric',
            'paid_amount' => 'required|numeric',
            'change_amount' => 'required|numeric',
        ]);

        Transaction::create([
            'user_id' => auth()->id(), // Menyimpan ID user yang login
            'invoice_number' => 'INV-' . time(),
            'total_price' => $request->total_price,
            'paid_amount' => $request->paid_amount,
            'change_amount' => $request->change_amount,
        ]);

        return redirect()->back()->with('success', 'Transaksi berhasil disimpan!');
    }
}