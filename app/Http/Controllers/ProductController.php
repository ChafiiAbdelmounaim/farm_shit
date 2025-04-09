<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Field;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->when($request->search, function($query) use ($request) {
                $query->where('name', 'like', '%'.$request->search.'%')
                    ->orWhereHas('category', function($q) use ($request) {
                        $q->where('name', 'like', '%'.$request->search.'%');
                    });
            })
            ->orderBy('name')
            ->paginate(10);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'current_quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        Product::create($request->all());

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully!');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'current_quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $product->update($request->all());

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully!');
    }

    public function addStock(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $product->current_quantity += $request->quantity;
        $product->save();

        InventoryTransaction::create([
            'product_id' => $product->id,
            'user_id' => auth()->check() ? auth()->id() : 1,
            'type' => 'in',
            'quantity' => $request->quantity,
            'date' => $request->date,
            'notes' => $request->notes
        ]);

        return back()->with('success', 'Stock added!');
    }

    public function useStock(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => "required|integer|min:1|max:$product->current_quantity",
            'field_id' => 'required|exists:fields,id',
            'used_by_user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $product->current_quantity -= $request->quantity;
        $product->save();

        InventoryTransaction::create([
            'product_id' => $product->id,
            'user_id' => auth()->check() ? auth()->id() : 1,
            'used_by_user_id' => $request->used_by_user_id,
            'type' => 'out',
            'quantity' => $request->quantity,
            'field_id' => $request->field_id,
            'date' => $request->date,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Stock used!');
    }

    public function showAddStockForm(Product $product)
    {
        return view('products.add-stock', compact('product'));
    }

    public function showUseStockForm(Product $product)
    {
        $fields = Field::all();
        $users = User::all();
        return view('products.use-stock', compact('product', 'fields', 'users'));
    }
}
