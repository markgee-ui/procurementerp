<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\PurchaseOrder; 
use App\Models\PurchaseOrderItem; 

class ProcurementController extends Controller
{
    /**
     * Show the procurement form
     */
    public function create()
    {
        return view('procurement.create');
    }

     public function supplierIndex(Request $request) // <-- MODIFIED: Accepts Request for filtering
    {
        // Fetch distinct locations for the filter dropdown
        $locations = Supplier::distinct()->pluck('location')->sort();
        
        // Start the Supplier query, ordered by latest creation by default
        $query = Supplier::latest(); 

        // Filter 1: Supplier Name Search
        if ($request->filled('name_search')) {
            $query->where('name', 'like', '%' . $request->name_search . '%');
        }

        // Filter 2: Location Filter
        if ($request->filled('location_filter')) {
            $query->where('location', $request->location_filter);
        }

        // Execute the paginated query, appending current filters to pagination links
        $suppliers = $query->paginate(25)->appends($request->query()); 
        
        // Pass suppliers and locations to the view
        return view('procurement.supplier.index', compact('suppliers', 'locations'));
        
    }
    // --- NEW METHODS FOR SUPPLIER CRUD ACTIONS ---
    public function editSupplier(Supplier $supplier)
    {
        // Create this view in resources/views/procurement/supplier/edit.blade.php
        return view('procurement.supplier.edit', compact('supplier'));
    }
    /**
     * Update the specified supplier in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSupplier(Request $request, Supplier $supplier): RedirectResponse
    {
        // Placeholder validation - update with all your fields
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            // Add validation for KRA Pin, contacts, and payment details
        ]);

        $supplier->update($request->all());

        return redirect()->route('procurement.supplier.index')
                         ->with('success', 'Supplier details updated successfully!');
    }

    /**
     * Remove the specified supplier from storage.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroySupplier(Supplier $supplier): RedirectResponse
    {
        $supplier->delete();

        return redirect()->route('procurement.supplier.index')
                         ->with('success', 'Supplier successfully removed.');
    }
    public function show(Supplier $supplier)
    {
        // The $supplier variable already contains the correct Supplier model 
        // instance thanks to implicit model binding.

        return view('procurement.supplier.show', compact('supplier'));
    }
    /**
     * Display a listing of all Products (New View)
     */
     public function productIndex(Request $request) 
    {
        // Fetch all suppliers for the filter dropdown
        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);

        // Start the Product query
        $query = Product::with('supplier')->orderBy('item');

        // Filter 1: Item Name Search
        if ($request->filled('item_name')) {
            $query->where('item', 'like', '%' . $request->item_name . '%');
        }

        // Filter 2: Supplier ID Filter
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        
        // Execute the paginated query, appending current filters to pagination links
        $products = $query->paginate(25)->appends($request->query()); 
        
        // Pass both products and suppliers to the view
        return view('procurement.product.index', compact('products', 'suppliers'));
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
        'products_data' => 'required|json',
        'kra_pin'               => 'nullable|string|max:255',
        'sales_person_contact'  => 'nullable|string|max:255',
        'shop_photo'            => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Max 2MB       
        'account_number'        => 'nullable|string|max:255',
        'bank_name'             => 'nullable|string|max:255',
        'paybill_number'        => 'nullable|string|max:255',
        'till_number'           => 'nullable|string|max:255',
    ]);

    try {
        // 2. PARSE PRODUCTS JSON
        $products = json_decode($request->products_data, true);

        if (!$products || !is_array($products)) {
            return back()->with('error', 'Invalid product data format.')->withInput();
        }

        // 3. FILE UPLOAD HANDLING (NEW STEP)
        $shopPhotoPath = null;
        if ($request->hasFile('shop_photo')) {
            // Store the file in the 'public/supplier-photos' directory
            $shopPhotoPath = $request->file('shop_photo')->store('supplier-photos', 'public');
        }

        // 4. SAVE SUPPLIER
        $supplier = Supplier::create([
            'name'                  => $request->supplier_name,
            'location'              => $request->location,
            'address'               => $request->address,
            'contact'               => $request->contact,
            'kra_pin'               => $request->kra_pin,
            'sales_person_contact'  => $request->sales_person_contact,
            'shop_photo_path'       => $shopPhotoPath, 
            
            'account_number'        => $request->account_number,
            'bank_name'             => $request->bank_name,
            'paybill_number'        => $request->paybill_number,
            'till_number'           => $request->till_number,
        ]);

        // 5. SAVE EACH PRODUCT
        foreach ($products as $product) {
            Product::create([
                'supplier_id' => $supplier->id,
                'item'        => $product['item'],
                'description' => $product['description'],
                'unit_price'  => $product['unit_price'],
            ]);
        }

        // 6. RETURN SUCCESS
        return redirect()
            ->route('procurement.create')
            ->with('success', 'Supplier and procurement entry saved successfully!');

    } catch (\Exception $e) {
        // Make sure to include the Log facade import at the top: `use Illuminate\Support\Facades\Log;`
        Log::error("PROCUREMENT SAVE ERROR: " . $e->getMessage());

        return back()
            ->withInput()
            ->with('error', 'An error occurred while saving the supplier data. Please try again.');
    }
}

public function createPurchaseOrder(Supplier $supplier) // Uses Route Model Binding
    {
        // Fetch all products associated with this supplier
        // Assuming products are linked by 'supplier_id'
        $products = $supplier->products; // Or whatever your relationship method is called

        return view('procurement.purchase_order.create', compact('supplier', 'products'));
    }

  public function storePurchaseOrder(Request $request) 
    {
        // --- 1. Validation ---
        // (Validation block is correct and remains unchanged)
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items'       => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.discount'   => 'nullable|numeric|min:0|max:100', 
            'items.*.unit_price' => 'required|numeric|min:0', 
        ]);

        try {
            // --- 2. Calculate and Prepare Data ---
            $grandTotal = 0;
            $orderItems = [];

            foreach ($request->items as $itemData) {
                $quantity = $itemData['quantity'];
                // SECURITY NOTE: In a real system, you should re-fetch the price 
                // from the Product model here: $price = Product::find($itemData['product_id'])->unit_price;
                $price = $itemData['unit_price']; 
                $discountPercent = $itemData['discount'] ?? 0;

                $subtotal = $price * $quantity;
                $discountAmount = $subtotal * ($discountPercent / 100);
                $lineTotal = $subtotal - $discountAmount;
                
                $grandTotal += $lineTotal;

                $orderItems[] = [
                    'product_id' => $itemData['product_id'],
                    'quantity'   => $quantity,
                    'unit_price' => $price,
                    'discount'   => $discountPercent,
                    'line_total' => $lineTotal,
                ];
            }

            // --- 3. Save Purchase Order Header ---
            $purchaseOrder = PurchaseOrder::create([
                'supplier_id' => $request->supplier_id,
                'total_amount' => $grandTotal,
                'order_date' => now(),
                'status' => 'Draft', // Set initial status
                // 'order_number' => $this->generatePoNumber(), // Optional: if you have a number generator
            ]);

            // --- 4. Save Line Items (Batch creation is highly recommended) ---
            // Mass creating the items through the relationship is efficient.
            $purchaseOrder->items()->createMany($orderItems);
            
            // --- 5. Return Success ---
            return redirect()->route('procurement.order.show',$purchaseOrder)
                             ->with('success', 'Purchase Order #' . ($purchaseOrder->order_number ?? $purchaseOrder->id) . ' created successfully! Total: ' . number_format($grandTotal, 2));

        } catch (\Exception $e) {
            Log::error("PURCHASE ORDER SAVE ERROR: " . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'An error occurred while creating the Purchase Order: ' . $e->getMessage());
        }
    }
    // ProcurementController.php
public function showPurchaseOrder(PurchaseOrder $purchaseOrder)
{
    // Eager load relationships needed for the view
    $purchaseOrder->load('supplier', 'items.product');

    return view('procurement.purchase_order.show', compact('purchaseOrder'));
}
public function printPurchaseOrder(PurchaseOrder $purchaseOrder)
{
    // Eager load relationships for the print view
    $purchaseOrder->load('supplier', 'items.product');

    // Use a different layout/view without navigation/sidebar for clean printing
    return view('procurement.purchase_order.print', compact('purchaseOrder'));
}
}