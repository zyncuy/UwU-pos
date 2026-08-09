<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['user', 'details.product'])
            ->latest()
            ->paginate(10);

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $categories = Category::all();
        $products = Product::where('stock', '>', 0)->get();

        return view('transactions.create', compact('categories', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'pay_amount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $totalPrice = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->stock < $item['qty']) {
                    return back()->with('error', "Stok produk {$product->name} tidak mencukupi.");
                }

                $subtotal = $product->price * $item['qty'];
                $totalPrice += $subtotal;

                // Kurangi stok produk
                $product->decrement('stock', $item['qty']);

                $itemsData[] = [
                    'product_id' => $product->id,
                    'quantity'   => $item['qty'],
                    'price'      => $product->price,
                    'subtotal'   => $subtotal, // Mengisi kolom subtotal
                ];
            }

            if ($request->pay_amount < $totalPrice) {
                return back()->with('error', 'Jumlah bayar kurang dari total harga.');
            }

            $changeAmount = $request->pay_amount - $totalPrice;
            $invoiceNumber = 'TRX-' . time() . rand(100, 999);

            $transaction = Transaction::create([
                'user_id'       => auth()->id() ?? 1,
                'invoice'       => $invoiceNumber,
                'total_price'   => $totalPrice,
                'pay_amount'    => $request->pay_amount,
                'change_amount' => $changeAmount,
            ]);

            // Simpan detail item ke relasi 'details' (transaction_details)
            foreach ($itemsData as $data) {
                if (method_exists($transaction, 'details')) {
                    $transaction->details()->create($data);
                } else {
                    $transaction->items()->create($data);
                }
            }

            DB::commit();

            return redirect()->route('transactions.show', $transaction->id)
                ->with('success', 'Transaksi berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses transaksi: ' . $e->getMessage());
        }
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['user', 'details.product']);
        return view('transactions.show', compact('transaction'));
    }
}