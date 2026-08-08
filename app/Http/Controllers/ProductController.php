<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        // Hanya mengambil produk milik user yang sedang login
        $products = Product::where('user_id', auth()->id())->with('category')->get();
        $categories = Category::where('user_id', auth()->id())->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        Product::create([
            'user_id' => auth()->id(), // Menyimpan ID pengguna yang login
            'category_id' => $request->category_id,
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
        ]);

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan!');
    }
}