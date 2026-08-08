<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        // Total Penjualan Hari Ini (hanya milik user login)
        $penjualanHariIni = Transaction::where('user_id', $userId)
            ->whereDate('created_at', Carbon::today())
            ->sum('total_price');

        // Total Jenis Produk (hanya milik user login)
        $totalProduk = Product::where('user_id', $userId)->count();

        // Total Transaksi Selesai (hanya milik user login)
        $totalTransaksi = Transaction::where('user_id', $userId)->count();

        return view('dashboard', compact('penjualanHariIni', 'totalProduk', 'totalTransaksi'));
    }
}