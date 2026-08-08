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

        // 1. Total Penjualan Hari Ini
        $todaySales = Transaction::where('user_id', $userId)
            ->whereDate('created_at', Carbon::today())
            ->sum('total_price');

        // 2. Total Jenis Produk
        $totalProducts = Product::where('user_id', $userId)->count();

        // 3. Total Transaksi Selesai
        $totalTransactions = Transaction::where('user_id', $userId)->count();

        // 4. Data Grafik Penjualan 7 Hari Terakhir
        $salesData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $total = Transaction::where('user_id', $userId)
                ->whereDate('created_at', $date)
                ->sum('total_price');

            $salesData[] = [
                'date' => Carbon::now()->subDays($i)->format('d M'),
                'total' => (float) $total,
            ];
        }

        return view('dashboard', compact('todaySales', 'totalProducts', 'totalTransactions', 'salesData'));
    }
}