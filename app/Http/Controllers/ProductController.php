<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->where('user_id', auth()->id())->get();
        $categories = Category::where('user_id', auth()->id())->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        return redirect()->route('products.index');
    }

    public function show(Product $product)
    {
        $this->authorizeProduct($product);

        return redirect()->route('products.index');
    }

    public function edit(Product $product)
    {
        $this->authorizeProduct($product);

        $categories = Category::where('user_id', auth()->id())->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        if ($request->category_id && ! $this->categoryBelongsToUser($request->category_id)) {
            abort(403);
        }

        Product::create([
            'user_id' => auth()->id(),
            'category_id' => $request->category_id,
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
        ]);

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan!');
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizeProduct($product);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        if ($request->category_id && ! $this->categoryBelongsToUser($request->category_id)) {
            abort(403);
        }

        $product->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
        ]);

        return redirect()->back()->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy(Product $product)
    {
        $this->authorizeProduct($product);

        $product->delete();

        return redirect()->back()->with('success', 'Produk berhasil dihapus!');
    }

    private function authorizeProduct(Product $product): void
    {
        if ($product->user_id !== auth()->id()) {
            abort(403);
        }
    }

    private function categoryBelongsToUser(int $categoryId): bool
    {
        return Category::where('id', $categoryId)
            ->where('user_id', auth()->id())
            ->exists();
    }
}
