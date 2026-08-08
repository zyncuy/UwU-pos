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

        // Variabel disesuaikan dengan dashboard.blade.php
        $todaySales = Transaction::where('user_id', $userId)
            ->whereDate('created_at', Carbon::today())
            ->sum('total_price');

        $totalProducts = Product::where('user_id', $userId)->count();

        $totalTransactions = Transaction::where('user_id', $userId)->count();

        return view('dashboard', compact('todaySales', 'totalProducts', 'totalTransactions'));
    }
}