<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'total_price' => 'required|numeric',
            'pay_amount' => 'required|numeric|gte:total_price',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $userId = Auth::id() ?? DB::table('users')->value('id') ?? 1;

                // Simpan data transaksi utama
                $transaction = Transaction::create([
                    'user_id'       => $userId,
                    'invoice'       => 'TRX-' . time(),
                    'total_price'   => $request->total_price,
                    'pay_amount'    => $request->pay_amount,
                    'change_amount' => $request->pay_amount - $request->total_price,
                ]);

                // Proses pemotongan stok jika ada items
                if ($request->has('items')) {
                    foreach ($request->items as $item) {
                        $product = Product::find($item['product_id']);
                        if ($product) {
                            $product->decrement('stock', $item['quantity']);

                            DB::table('transaction_details')->insert([
                                'transaction_id' => $transaction->id,
                                'product_id'     => $product->id,
                                'quantity'       => $item['quantity'],
                                'price'          => $product->price,
                                'created_at'     => now(),
                                'updated_at'     => now(),
                            ]);
                        }
                    }
                }
            });

            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil disimpan!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses transaksi: ' . $e->getMessage());
        }
    }
}