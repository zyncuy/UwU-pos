<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $todaySales = Transaction::whereDate('created_at', today())->sum('total_price');
        $totalProducts = Product::count();
        $totalTransactions = Transaction::count();

        $salesData = Transaction::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_price) as total')
        )
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();

        return view('dashboard', compact('todaySales', 'totalProducts', 'totalTransactions', 'salesData'));
    }
}