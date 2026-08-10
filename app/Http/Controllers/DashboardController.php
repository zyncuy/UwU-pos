<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $todaySales = Transaction::where('user_id', $userId)
            ->whereDate('created_at', Carbon::today())
            ->sum('total_price');
        $totalSales = Transaction::where('user_id', $userId)->sum('total_price');
        $todayTransactions = Transaction::where('user_id', $userId)
            ->whereDate('created_at', Carbon::today())
            ->count();
        $totalProducts = Product::where('user_id', $userId)->count();
        $totalCategories = Category::where('user_id', $userId)->count();

        return view('dashboard', compact(
            'todaySales',
            'totalSales',
            'todayTransactions',
            'totalProducts',
            'totalCategories'
        ));
    }
}
