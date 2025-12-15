<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\PurchaseOrder; 
use App\Models\PurchaseRequisition;
use App\Models\PurchaseOrderItem; 
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;


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
            'unit'          => 'required|string|max:50',
            'supplier_id'   => 'required|exists:suppliers,id', // Ensure the supplier exists

        ]);

        try {
            // 2. UPDATE PRODUCT
            $product->update([
                'item'          => $request->item,
                'description'   => $request->description,
                'unit_price'    => $request->unit_price,
                'supplier_id'   => $request->supplier_id,
                'unit'          => $request->unit,
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
                'unit'        => $product['unit'],
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
        $products = $supplier->products; 

        return view('procurement.purchase_order.create', compact('supplier', 'products'));
    }
    private function generatePoNumber(): string
    {
        $currentYear = now()->year;

        // Find the last PO created this year to determine the next sequence number.
        // We look up the latest record to get its ID. 
        // NOTE: This relies on the database ID being sequential.
        $latestPo = PurchaseOrder::latest('id')->first();
        
        // Start the sequence at 1 if no previous PO exists.
        $nextSequence = 1;

        if ($latestPo) {
            // If an order exists, take its ID, and increment for the new sequence number.
            // Using ID directly is simple, but for true year-specific sequence, 
            // you might need a dedicated sequence number column.
            
            // For simplicity and to ensure uniqueness, we'll use the next AUTO-INCREMENT ID.
            // To get the next ID without saving, we can query the information schema 
            // OR simply rely on the ID after the model is created (less ideal here).
            
            // **Safer approach: Base sequence on the current count of POs + 1, 
            // but this is simpler if you reset the number yearly.**
            $nextSequence = $latestPo->id + 1; 
        }

        // Format the sequence number with leading zeros (e.g., 1 becomes 0001)
        $paddedSequence = str_pad($nextSequence, 4, '0', STR_PAD_LEFT);

        // Combine prefix, year, and sequence
        return "TGL/{$paddedSequence}";
    }

  public function storePurchaseOrder(Request $request) 
{
    // 1. Validation
    $request->validate([
        'supplier_id' => 'required|exists:suppliers,id',
        'project_name' => 'nullable|string|max:255',
        'items'       => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity'   => 'required|integer|min:0',  
        'items.*.discount'   => 'nullable|numeric|min:0|max:100',
        'items.*.unit_price' => 'required|numeric|min:0',
    ]);

    try {
        $grandTotal = 0;
        $orderItems = [];

        foreach ($request->items as $itemData) {

            if ($itemData['quantity'] <= 0) {
                continue;
            }

            $quantity = $itemData['quantity'];
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

        // Ensure at least 1 valid product
        if (empty($orderItems)) {
            return back()
                ->withInput()
                ->with('error', 'Please add at least one product with quantity greater than 0.');
        }

        $poNumber = $this->generatePoNumber();

        $purchaseOrder = PurchaseOrder::create([
            'supplier_id' => $request->supplier_id,
            'project_name' => $request->project_name,
            'total_amount' => $grandTotal,
            'order_date' => now(),
            'status' => 'Draft',
            'order_number' => $poNumber,
        ]);

        $purchaseOrder->items()->createMany($orderItems);

        return redirect()
            ->route('procurement.order.show', $purchaseOrder)
            ->with('success', 'Purchase Order #' . ($purchaseOrder->order_number ?? $purchaseOrder->id) . 
                  ' created successfully! Total: ' . number_format($grandTotal, 2));

    } catch (\Exception $e) {

        Log::error("PURCHASE ORDER SAVE ERROR: " . $e->getMessage());

        return back()
            ->withInput()
            ->with('error', 'An error occurred: ' . $e->getMessage());
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

public function indexPurchaseOrder(Request $request)
    {
        // Start building the query
        $query = PurchaseOrder::query();

        // --- Filtering Logic ---

        // 1. Search by PO # or Project Name
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                // Search by auto-generated ID (if order_number is null) or project_name
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('project_name', 'like', "%{$search}%");
            });
        }

        // 2. Filter by Status
        if ($status = $request->input('status_filter')) {
            $query->where('status', $status);
        }

        // 3. Filter by Project Name (exact match from dropdown)
        if ($project = $request->input('project_filter')) {
            $query->where('project_name', $project);
        }

        // --- Fetch Data ---

        // Get all unique project names for the filter dropdown
        $projects = PurchaseOrder::select('project_name')
                        ->distinct()
                        ->whereNotNull('project_name')
                        ->orderBy('project_name')
                        ->pluck('project_name');

        // Execute the query, eager-load the supplier, and paginate
        $purchaseOrders = $query->with('supplier')
                                ->orderBy('order_date', 'desc')
                                ->paginate(10)
                                ->withQueryString(); // Maintain filters on pagination links

        // --- Return View ---
        return view('procurement.purchase_order.index', [
            'purchaseOrders' => $purchaseOrders,
            'projects' => $projects,
        ]);
    }
    public function createSelectSupplier(Request $request)
    {
        // Start building the query
        $query = Supplier::query();

        // --- Filtering Logic (Reusing logic from Supplier Index) ---

        // Supplier Name Search
        if ($search = $request->input('name_search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Location Filter
        if ($location = $request->input('location_filter')) {
            $query->where('location', $location);
        }

        // --- Fetch Data ---

        // Get all unique locations for the filter dropdown
        $locations = Supplier::select('location')
                            ->distinct()
                            ->whereNotNull('location')
                            ->orderBy('location')
                            ->pluck('location');

        // Execute the query and paginate results
        $suppliers = $query->orderBy('name', 'asc')
                            ->paginate(10)
                            ->withQueryString();

        // --- Return View ---
        // We will reuse the supplier index view but pass a variable 
        // to indicate we are in 'selection' mode.
        return view('procurement.supplier.index', [
            'suppliers' => $suppliers,
            'locations' => $locations,
            'selectionMode' => true, // Flag to modify the view's behavior/buttons
        ]);
    }
    public function editPurchaseOrder(PurchaseOrder $purchaseOrder)
    {
        // 1. Fetch all products (or only those linked to the supplier, if required by your logic)
        $products = Product::where('supplier_id', $purchaseOrder->supplier_id)->get();
        
        // 2. Eager load items and supplier for the PO
        $purchaseOrder->load(['items', 'supplier']);

        // 3. Return the view
        return view('procurement.purchase_order.edit', [
            'purchaseOrder' => $purchaseOrder,
            'products' => $products,
        ]);
    }
    public function updatePurchaseOrder(Request $request, PurchaseOrder $purchaseOrder)
    {
        // 1. Validation
        // This validation is similar to the store method but ensures the PO being updated is not a final status.
        // NOTE: Add a check if the status is not 'Issued' or 'Received' etc. to prevent editing finalized orders.
        if (!in_array($purchaseOrder->status, ['Draft'])) {
             return back()->with('error', 'Only Purchase Orders with a "Draft" status can be modified.');
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id', // Should match the existing PO supplier
            'project_name' => 'nullable|string|max:255',
            'items'       => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:0',
            'items.*.discount'   => 'nullable|numeric|min:0|max:100',
            // IMPORTANT: We trust the unit_price passed from the form for calculation, 
            // but in a production system, it should be re-validated against the product master.
            'items.*.unit_price' => 'required|numeric|min:0', 
        ]);

        try {
            DB::beginTransaction();
            
            // 2. Calculate and Prepare Data
            $grandTotal = 0;
            $orderItems = [];

            foreach ($request->items as $itemData) {
                $quantity = (float)$itemData['quantity'];
                $price = (float)$itemData['unit_price']; 
                $discountPercent = (float)($itemData['discount'] ?? 0);

                $subtotal = $price * $quantity;
                $discountAmount = $subtotal * ($discountPercent / 100);
                $lineTotal = round($subtotal - $discountAmount, 2); // Round to 2 decimal places
                
                $grandTotal += $lineTotal;

                $orderItems[] = [
                    'product_id' => $itemData['product_id'],
                    'quantity'   => $quantity,
                    'unit_price' => $price,
                    'discount'   => $discountPercent,
                    'line_total' => $lineTotal,
                    // Optionally add timestamps if not using createMany
                    // 'created_at' => now(), 
                    // 'updated_at' => now(),
                ];
            }
            
            // 3. Update Purchase Order Header
            $purchaseOrder->update([
                'project_name' => $request->project_name,
                'total_amount' => round($grandTotal, 2),
                'status' => 'Draft', // Ensure status remains Draft on modification
                'updated_at' => now(),
            ]);

            // 4. Synchronize/Replace Line Items
            // We delete all old items and re-create the new ones in one go.
            $purchaseOrder->items()->delete();
            $purchaseOrder->items()->createMany($orderItems);
            
            DB::commit();

            // 5. Return Success
            $poNumber = $purchaseOrder->order_number ?? $purchaseOrder->id;
            return redirect()->route('procurement.order.show', $purchaseOrder)
                             ->with('success', "Purchase Order #{$poNumber} updated successfully! New Total: " . number_format($grandTotal, 2));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("PURCHASE ORDER UPDATE ERROR for PO #{$purchaseOrder->id}: " . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'An error occurred while updating the Purchase Order. Please try again: ' . $e->getMessage());
        }
    }
    public function destroyPurchaseOrder(PurchaseOrder $purchaseOrder)
    {
        // 1. Status Check
        if (!in_array($purchaseOrder->status, ['Draft'])) {
            $poNumber = $purchaseOrder->order_number ?? $purchaseOrder->id;
            return back()->with('error', "Purchase Order #{$poNumber} has a status of '{$purchaseOrder->status}' and cannot be deleted. It must be a 'Draft' to be destroyed.");
        }

        try {
            DB::beginTransaction();

            // Get the PO number/ID for the success message
            $poIdentifier = $purchaseOrder->order_number ?? $purchaseOrder->id;
            
            // 2. Delete Line Items
            // This assumes the relationship is named 'items' and cascades might not be set in the database
            $purchaseOrder->items()->delete();
            
            // 3. Delete the Purchase Order Header
            $purchaseOrder->delete();
            
            DB::commit();

            // 4. Return Success
            return redirect()->route('procurement.order.index')
                             ->with('success', "Purchase Order #{$poIdentifier} and its associated items have been successfully deleted.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("PURCHASE ORDER DELETION ERROR for PO #{$purchaseOrder->id}: " . $e->getMessage());

            return back()
                ->with('error', 'An error occurred while deleting the Purchase Order. Please check logs.');
        }
    }
public function downloadPurchaseOrder(PurchaseOrder $purchaseOrder)
{
    $safeOrderNumber = str_replace(['/', '\\'], '-', $purchaseOrder->order_number);

    $pdf = Pdf::loadView('procurement.purchase_order.pdf', [
        'purchaseOrder' => $purchaseOrder
    ])->setPaper('A4', 'portrait');

    return $pdf->download('PO-' . $safeOrderNumber . '.pdf');
}
public function requisitionsIndex(Request $request)
{
    // Define the stages required for final procurement action (e.g., QS=1, OPM=2)
    $requiredApprovals = [1, 2]; 
    $requiredCount = count($requiredApprovals);

    // --- 1. BUILD THE ID FILTER SUBQUERY ---
    // This query finds the IDs of requisitions that meet the complex approval criteria.
    $requisitionIds = DB::table('purchase_requisitions')
        ->join('approvals as a', 'a.purchase_requisition_id', '=', 'purchase_requisitions.id')
        
        // Filter only for successful approvals
        ->where('a.status', 'approved')
        
        // Filter by the required stages
        ->whereIn('a.stage', $requiredApprovals)
        
        // Exclude requisitions that are already processed (optional, but good practice)
        ->whereNotIn('purchase_requisitions.status', ['Processed', 'Completed'])
        
        // Group by ID to count the distinct approvals received
        ->groupBy('purchase_requisitions.id')
        
        // Filter to ensure all required approvals are present (COUNT = 2)
        ->havingRaw('COUNT(DISTINCT a.stage) = ?', [$requiredCount])
        
        // *** CRITICAL FIX: Only select the ID to satisfy ONLY_FULL_GROUP_BY ***
        ->select('purchase_requisitions.id');

    
    // --- 2. EXECUTE THE MAIN PAGINATED QUERY ---
    // Select the full model data based on the filtered IDs.
    $query = PurchaseRequisition::with('approvals')
        ->whereIn('id', $requisitionIds)
        // Add sorting and filtering directly to the main query
        ->orderBy('created_at', 'desc');

    // Optional: Implement filtering by project/site, date, etc. here using $request
    // E.g. ->when($request->project_id, fn($q, $id) => $q->where('project_id', $id))

    $requisitions = $query->paginate(15)->withQueryString();

    return view('procurement.requisition.index', compact('requisitions'));
}

    // --- NEW: View Approved Requisition to Initiate PO ---
    /**
     * Show an approved Purchase Requisition and provide options to act upon it.
     */
   public function requisitionAction(PurchaseRequisition $requisition)
{
    // Eager load necessary relationships
    $requisition->load(
        'project',
        'initiator', 
        'items.boqMaterial.suppliers', // <-- Eager load the supplier data for each item/material
        'approvals.user'
    );

    // Fetch all suppliers (we still need this for initial load and non-item based selection)
    $suppliers = Supplier::orderBy('name')->get(['id', 'name']);
    
    // --- NEW: Generate the Item-to-Supplier Map ---
    $itemSupplierMap = [];
    foreach ($requisition->items as $item) {
        // Use the ID of the boqMaterial (product) to link suppliers
        $materialId = $item->boqMaterial->id ?? null;
        
        if ($materialId && $item->boqMaterial->suppliers) {
            $itemSupplierMap[$item->id] = $item->boqMaterial->suppliers->pluck('id')->toArray();
        }
    }
    
    // Pass the map to the view
    return view('procurement.requisition.action', compact('requisition', 'suppliers', 'itemSupplierMap'));
}

    // --- NEW: Linking or Creating PO from Requisition ---
    // (This method is complex and often handled by a dedicated service or a multi-step form, 
    // but for now, let's keep it simple as a redirect/link)
    
    /**
     * Handles the initiation of a PO based on the Requisition.
     * This method will likely redirect to the PO creation form, pre-filling data.
     */
    public function initiatePurchaseOrder(PurchaseRequisition $requisition, Request $request): RedirectResponse
    {
        // 1. You could link to an existing PO, but typically you create a new one.
        
        // 2. The most common flow: Redirect to the PO creation page, pre-selecting a supplier
        //    (If the user selected a preferred supplier, or if the system auto-suggests one)

        $request->validate([
            'preferred_supplier_id' => 'nullable|exists:suppliers,id', // Optional field from the action view
        ]);

        $supplierId = $request->preferred_supplier_id;

        if ($supplierId) {
            $supplier = Supplier::find($supplierId);
            // Redirect to the PO creation route, passing the supplier and the requisition ID
            return redirect()->route('procurement.order.create', [
                'supplier' => $supplier->id, 
                'requisition_id' => $requisition->id
            ])->with('info', 'PO creation started. Items from Requisition #' . $requisition->id . ' can be imported.');
        }

        // If no preferred supplier, redirect to the supplier selection page
        return redirect()->route('procurement.order.create.select_supplier')
            ->with('info', 'Please select a supplier to begin processing Requisition #' . $requisition->id . '.')
            ->with('requisition_id', $requisition->id); // Pass the PR ID via session flash for later use
    }

}