<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Supplier;
use App\Models\Product;

class ProcurementController extends Controller
{
    /**
     * Show the procurement form
     */
    public function create()
    {
        return view('procurement.create');
    }

    public function supplierIndex() // <-- EXISTING METHOD
    {
        // Fetch only suppliers, ordered by latest creation
        $suppliers = Supplier::latest()->paginate(15); 
        
        return view('procurement.supplier.index', compact('suppliers'));
    }

    /**
     * Display a listing of all Products (New View)
     */
    public function productIndex() // <-- EXISTING METHOD
    {
        // Fetch all products with their associated supplier, ordered by item name
         $products = Product::with('supplier')->orderBy('item')->paginate(perPage: 15); 
        
        return view('procurement.product.index', compact('products'));
    }

    // --- NEW METHODS FOR PRODUCT CRUD ACTIONS ---

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);
        return view('procurement.product.edit', compact('product','suppliers'));
       
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        // 1. VALIDATION
        $request->validate([
            'item'          => 'required|string|max:255',
            'description'   => 'nullable|string|max:500',
            'unit_price'    => 'required|numeric|min:0',
            'supplier_id'   => 'required|exists:suppliers,id', // Ensure the supplier exists
        ]);

        try {
            // 2. UPDATE PRODUCT
            $product->update([
                'item'          => $request->item,
                'description'   => $request->description,
                'unit_price'    => $request->unit_price,
                'supplier_id'   => $request->supplier_id,
            ]);

            // 3. RETURN SUCCESS
            return redirect()
                ->route('procurement.product.index')
                ->with('success', 'Product "' . $product->item . '" updated successfully!');

        } catch (\Exception $e) {
            Log::error("PRODUCT UPDATE ERROR: " . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'An error occurred while updating the product.');
        }
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        try {
            $product->delete();
            return back()->with('success', 'Product "' . $product->item . '" deleted successfully!');
        } catch (\Exception $e) {
            Log::error("PRODUCT DELETE ERROR: " . $e->getMessage());
            return back()->with('error', 'Error deleting product.');
        }
    }

    // --- EXISTING STORE METHOD ---

    /**
     * Store procurement data into the database
     */
    public function store(Request $request)
    {
        // 1. VALIDATION
        $request->validate([
            'supplier_name' => 'required|string|max:255',
            'location'      => 'required|string|max:255',
            'address'       => 'required|string|max:500',
            'contact'       => 'required|string|max:255',
            'products_data' => 'required|json'
        ]);

        try {
            // 2. PARSE PRODUCTS JSON
            $products = json_decode($request->products_data, true);

            if (!$products || !is_array($products)) {
                return back()->with('error', 'Invalid product data format.');
            }

            // 3. SAVE SUPPLIER
            $supplier = Supplier::create([
                'name'     => $request->supplier_name,
                'location' => $request->location,
                'address'  => $request->address,
                'contact'  => $request->contact
            ]);

            // 4. SAVE EACH PRODUCT
            foreach ($products as $product) {
                Product::create([
                    'supplier_id' => $supplier->id,
                    'item'        => $product['item'],
                    'description' => $product['description'],
                    'unit_price'  => $product['unit_price'],
                ]);
            }

            // 5. RETURN SUCCESS
            return redirect()
                ->route('procurement.create')
                ->with('success', 'Procurement entry saved successfully!');

        } catch (\Exception $e) {
            Log::error("PROCUREMENT SAVE ERROR: " . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'An error occurred while saving the procurement entry.');
        }
    }
}