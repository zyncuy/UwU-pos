<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\Category;
use Carbon\Carbon;
use Exception;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $todaySales = Transaction::whereDate('created_at', Carbon::today())->sum('total_price') ?? 0;
            $totalSales = Transaction::sum('total_price') ?? 0;
            $todayTransactions = Transaction::whereDate('created_at', Carbon::today())->count();
            $totalProducts = Product::count();
            $totalCategories = Category::count();
        } catch (Exception $e) {
            $todaySales = 0;
            $totalSales = 0;
            $todayTransactions = 0;
            $totalProducts = 0;
            $totalCategories = 0;
        }

        return view('dashboard', compact(
            'todaySales',
            'totalSales',
            'todayTransactions',
            'totalProducts',
            'totalCategories'
        ));
    }
}