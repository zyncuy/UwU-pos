<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['user', 'details.product'])->latest()->get();
        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $categories = Category::all();
        $products = Product::all();
        return view('transactions.create', compact('categories', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'pay_amount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $totalPrice = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal = $product->price * $item['quantity'];
                $totalPrice += $subtotal;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ];
            }

            if ($request->pay_amount < $totalPrice) {
                return back()->with('error', 'Jumlah pembayaran kurang!');
            }

            $transaction = Transaction::create([
                'user_id' => auth()->id() ?? 1,
                'invoice' => 'TRX-' . time(),
                'total_price' => $totalPrice,
                'pay_amount' => $request->pay_amount,
                'change_amount' => $request->pay_amount - $totalPrice,
            ]);

            foreach ($itemsData as $data) {
                $transaction->details()->create($data);
                
                // Kurangi stok produk
                $product = Product::find($data['product_id']);
                if ($product) {
                    $product->decrement('stock', $data['quantity']);
                }
            }

            DB::commit();
            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses transaksi: ' . $e->getMessage());
        }
    }
}