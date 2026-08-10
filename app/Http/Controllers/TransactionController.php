<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['user', 'details.product'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $categories = Category::where('user_id', auth()->id())->get();
        $products = Product::where('user_id', auth()->id())->get();

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
                $product = Product::where('id', $item['product_id'])
                    ->where('user_id', auth()->id())
                    ->lockForUpdate()
                    ->first();

                if (! $product) {
                    throw new \Exception('Produk tidak ditemukan atau bukan milik Anda.');
                }

                $quantity = (int) $item['quantity'];

                if ($product->stock < $quantity) {
                    throw new \Exception("Stok \"{$product->name}\" tidak mencukupi (sisa {$product->stock}).");
                }

                $subtotal = $product->price * $quantity;
                $totalPrice += $subtotal;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ];
            }

            if ($request->pay_amount < $totalPrice) {
                throw new \Exception('Jumlah pembayaran kurang dari total belanja.');
            }

            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'invoice' => 'TRX-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4)),
                'total_price' => $totalPrice,
                'pay_amount' => $request->pay_amount,
                'change_amount' => $request->pay_amount - $totalPrice,
            ]);

            foreach ($itemsData as $data) {
                $transaction->details()->create($data);
                Product::where('id', $data['product_id'])->decrement('stock', $data['quantity']);
            }

            DB::commit();

            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal memproses transaksi: '.$e->getMessage());
        }
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            foreach ($transaction->details as $detail) {
                $product = Product::find($detail->product_id);
                if ($product) {
                    $product->increment('stock', $detail->quantity);
                }
            }

            $transaction->delete();
            DB::commit();

            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menghapus transaksi: '.$e->getMessage());
        }
    }
}
