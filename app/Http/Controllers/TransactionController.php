<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category; // Pastikan import Category ditambahkan
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $products = Product::where('stock', '>', 0)->get();
        $categories = Category::all(); // Mengambil data kategori untuk filter

        return view('transactions.index', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        // Fitur Tambah Barang ke Keranjang (Cart)
        if ($request->action === 'add_item') {
            $product = Product::findOrFail($request->product_id);
            $cart = session()->get('cart', []);

            if (isset($cart[$product->id])) {
                $cart[$product->id]['quantity'] += $request->quantity;
            } else {
                $cart[$product->id] = [
                    "name" => $product->name,
                    "quantity" => $request->quantity,
                    "price" => $product->price,
                ];
            }

            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Barang berhasil ditambahkan ke keranjang!');
        }

        // Fitur Selesaikan Transaksi / Checkout
        if ($request->action === 'checkout') {
            $cart = session()->get('cart', []);
            if (empty($cart)) {
                return redirect()->back()->with('error', 'Keranjang transaksi masih kosong!');
            }

            // Kurangi Stok Produk
            foreach ($cart as $id => $details) {
                $product = Product::find($id);
                if ($product) {
                    $product->decrement('stock', $details['quantity']);
                }
            }

            // Simpan Transaksi
            Transaction::create([
                'invoice' => 'TRX-' . time(),
                'total_price' => $request->total_price,
                'pay_amount' => $request->pay_amount,
                'change_amount' => $request->pay_amount - $request->total_price,
            ]);

            // Kosongkan Keranjang
            session()->forget('cart');

            return redirect()->back()->with('success', 'Transaksi berhasil diselesaikan!');
        }

        return redirect()->back();
    }

    public function destroy($id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Barang dihapus dari keranjang!');
    }
}