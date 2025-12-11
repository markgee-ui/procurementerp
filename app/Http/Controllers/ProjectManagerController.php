<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Boq; // Used for project/BoQ list
use App\Models\PurchaseRequisition; // Primary Model
use App\Models\BoqMaterial; // Used to fetch material details for PR creation
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PurchaseRequisitionItem; // For line items

class ProjectManagerController extends Controller
{
    /**
     * Display the PM Dashboard (index).
     * Shows project overview, pending PRs, and key BoQ data.
     * * @return \Illuminate\View\View
     */
    public function index()
    {
        // 1. Get the current user's projects (for dashboard summary)
        $projects = Boq::withCount('activities')->get();

        // 2. Get the count of PRs waiting for the PM's approval (Stage 1)
        $pendingApprovals = PurchaseRequisition::where('current_stage', 1) 
                                             ->where('status', 'Pending')
                                             ->count();
        
        return view('pm.index', [
            'projects' => $projects,
            'pendingApprovals' => $pendingApprovals,
        ]);
    }

    /**
     * Display a list of all Purchase Requisitions.
     * This method supports searching and filtering for the 'index' blade.
     * * @param Request $request
     * @return \Illuminate\View\View
     */
   public function indexRequisitions(Request $request)
{
    $query = PurchaseRequisition::with('initiator', 'project', 'material');

    // Search by material name
    if ($request->filled('item_search')) {
        $query->whereHas('material', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->item_search . '%');
        });
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('boq_id')) {
        $query->where('boq_id', $request->boq_id);
    }

    $boqs = Boq::select('id', 'project_name')->get();

    $requisitions = $query->orderBy('created_at', 'desc')->paginate(20);

    return view('pm.requisitions.index', [
        'requisitions' => $requisitions,
        'boqs' => $boqs,
    ]);
}

    
    /**
     * Display a specific Purchase Requisition.
     * * @param PurchaseRequisition $requisition
     * @return \Illuminate\View\View
     */
   public function showRequisition(PurchaseRequisition $requisition)
{
    // Eager load necessary relationships for the show blade
    // CHANGE 'user' to 'initiator'
    $requisition->load('initiator', 'project', 'material');

    return view('pm.requisitions.show', [
        'requisition' => $requisition,
    ]);
}

    /**
     * Show the form for creating a new Purchase Requisition (PR).
     * * @param Boq $boq (Using route model binding for the BoQ)
     * @return \Illuminate\View\View
     */
    public function createRequisition(Boq $project) // <-- CHANGE HERE
{
    // Load the BoQ materials grouped by activity
    // Use $project for the relationship
    $activities = $project->activities()->with('materials')->get();
    
    // Crucially, pass it to the view using the EXPECTED variable name ($boq)
    return view('pm.requisitions.create', [
        'boq' => $project, // Pass $project as $boq to satisfy the view logic
        'activities' => $activities,
    ]);
}
    
    /**
     * Store a newly created Purchase Requisition in storage.
     * This method is the simplified, storage-only version requested.
     * * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
public function storeRequisition(Request $request)
{
    // 1. Validation for Main PR and Line Items
    // Validation is now correctly checking the 'boqs' table for the header ID.
    $data = $request->validate([
        // Main PR Details (Header)
        'boq_id'           => 'required|exists:boqs,id', // FIXED: Now requires 'boq_id' and validates against 'boqs' table
        'justification'    => 'required|string',
        'required_by_date' => 'nullable|date',

        // Dynamic Line Items (Must be an array, must have at least one item)
        'items'            => 'required|array|min:1',
        // Validation for each item in the array
        'items.*.boq_material_id' => 'required|exists:boq_materials,id', 
        'items.*.boq_activity_id' => 'required|exists:boq_activities,id',
        'items.*.qty_requested'   => 'required|numeric|min:0.01',
    ]);

    // 2. Create the Parent Purchase Requisition Record (Header)
    $requisition = PurchaseRequisition::create([
        'user_id'          => Auth::id(),
        'boq_id'           => $data['boq_id'], // Using boq_id
        'justification'    => $data['justification'],
        'required_by_date' => $data['required_by_date'] ?? null,
        'status'           => 'Pending',
        'current_stage'    => 1,
    ]);

    // 3. Process and Create Line Items
    $totalEstimatedCost = 0;
    $itemsToCreate = [];

    foreach ($data['items'] as $itemData) {
        $boqMaterialId = $itemData['boq_material_id'];
        $qtyRequested = $itemData['qty_requested'];
        
        try {
            $boqMaterial = BoqMaterial::findOrFail($boqMaterialId);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Rollback the parent PR if any line item is invalid
            $requisition->delete(); 
            throw ValidationException::withMessages(['items' => 'One or more selected materials are invalid or not found.']);
        }

        $estimatedCost = $boqMaterial->rate * $qtyRequested;
        $totalEstimatedCost += $estimatedCost;

        $itemsToCreate[] = [
            'purchase_requisition_id' => $requisition->id,
            'boq_material_id'         => $boqMaterial->id,
            'boq_activity_id'         => $itemData['boq_activity_id'],
            'item_name'               => $boqMaterial->item,
            'unit'                    => $boqMaterial->unit,
            'qty_requested'           => $qtyRequested,
            'unit_cost'               => $boqMaterial->rate,
            'cost_estimate'           => $estimatedCost,
            'created_at'              => now(),
            'updated_at'              => now(),
        ];
    }
    
    // 4. Bulk Insert Line Items
    PurchaseRequisitionItem::insert($itemsToCreate);

    // 5. Update Parent PR with Total Cost (Requires a 'cost_estimate' column on PurchaseRequisition)
    $requisition->update(['cost_estimate' => $totalEstimatedCost]);

    // 6. Success Response
    return redirect()->route('pm.requisitions.show', $requisition)
                     ->with('success', 'Combined Purchase Requisition submitted with ' . count($data['items']) . ' items for approval!');
}
/**
 * Update the specified Purchase Requisition in storage.
 * @param \Illuminate\Http\Request $request
 * @param PurchaseRequisition $requisition
 * @return \Illuminate\Http\RedirectResponse
 */
public function updateRequisition(Request $request, PurchaseRequisition $requisition)
{
    // 1. Authorization & Status Check (Crucial)
    // $this->authorize('update', $requisition); 

    if ($requisition->status !== 'Pending') {
        return redirect()->route('pm.requisitions.show', $requisition)
                         ->with('error', 'Requisition is no longer Pending and cannot be updated.');
    }

    // 2. Validation
    $validated = $request->validate([
        'qty_requested' => 'required|numeric|min:0.01',
        'required_by_date' => 'nullable|date|after_or_equal:today',
        'justification' => 'required|string|max:500',
    ]);

    // 3. Update the Requisition
    $requisition->update($validated);

    // 4. Redirect
    return redirect()->route('pm.requisitions.show', $requisition)
                     ->with('success', 'Purchase Requisition #' . $requisition->id . ' updated successfully.');
}

/**
 * Remove the specified Purchase Requisition from storage (Cancel).
 * @param PurchaseRequisition $requisition
 * @return \Illuminate\Http\RedirectResponse
 */
public function destroyRequisition(PurchaseRequisition $requisition)
{
    // $this->authorize('delete', $requisition);

    if ($requisition->status !== 'Pending' && $requisition->status !== 'Rejected') {
        return back()->with('error', 'Cannot cancel a requisition that is already in procurement or approved.');
    }

    $requisition->delete();

    return redirect()->route('pm.requisitions.index')
                     ->with('success', 'Purchase Requisition was successfully cancelled and deleted.');
}

/**
 * Approves the Purchase Requisition and advances the workflow stage.
 * @param PurchaseRequisition $requisition
 * @return \Illuminate\Http\RedirectResponse
 */
public function approveRequisition(PurchaseRequisition $requisition)
{
    // $this->authorize('approve', $requisition); 
    
    if ($requisition->status !== 'Pending') {
        return back()->with('error', 'Cannot approve a requisition that is not Pending.');
    }

    // Example logic for a 3-stage approval (Site PM -> Finance -> Procurement)
    if ($requisition->current_stage < 3) {
        $requisition->current_stage += 1; // Advance to the next stage
        $message = "Requisition approved at Stage {$requisition->current_stage} and moved to the next approver.";
    } else {
        $requisition->status = 'Approved'; // Final approval
        $message = 'Requisition fully approved and sent to Procurement!';
    }
    
    $requisition->save();
    
    return redirect()->route('pm.requisitions.show', $requisition)
                     ->with('success', $message);
}

/**
 * Rejects the Purchase Requisition.
 * @param PurchaseRequisition $requisition
 * @return \Illuminate\Http\RedirectResponse
 */
public function rejectRequisition(Request $request, PurchaseRequisition $requisition)
{
    // $this->authorize('reject', $requisition);
    
    $request->validate([
        'rejection_notes' => 'required|string|max:500', // You'll need to pass this via form
    ]);

    $requisition->status = 'Rejected';
    $requisition->approval_notes = $request->rejection_notes;
    $requisition->save();
    
    return redirect()->route('pm.requisitions.show', $requisition)
                     ->with('error', 'Purchase Requisition has been rejected.');
}
    public function downloadRequisitionPdf(PurchaseRequisition $requisition)
{
    // Eager load necessary relationships for the PDF view
    $requisition->load('project', 'material', 'initiator');

    // Render the view to HTML
    // We will create 'pm.requisitions.pdf_template' next
    $html = View::make('pm.requisitions.pdf_template', [
        'requisition' => $requisition,
    ])->render();

    // Generate and Stream the PDF
    $pdf = PDF::loadHtml($html);

    // Set paper size and orientation if needed (e.g., A4 portrait)
    $pdf->setPaper('A4', 'portrait');

    // Download the PDF with a specific filename
    return $pdf->download('PR_' . $requisition->id . '_' . now()->format('Ymd') . '.pdf');
}
// app/Http/Controllers/ProjectManagerController.php

// ... other methods (index, create, store, show) ...

/**
 * Show the form for editing the specified purchase requisition.
 * * @param  \App\Models\Requisition  $requisition
 * @return \Illuminate\View\View
 */
public function editRequisition(PurchaseRequisition $requisition)
{
    // The Route Model Binding automatically fetches the Requisition based on the route parameter.
    // We eager load the 'items' relationship for efficient access in the form.
    $requisition->load('items');

    // Add authorization check (optional but recommended)
    // $this->authorize('update', $requisition); 
    
    // Assuming you have an 'edit' view under 'pm/requisitions'
    return view('pm.requisitions.edit', [
        'requisition' => $requisition,
        // You might need to pass lists of available projects, materials, etc., here too
        // 'projects' => Project::all(), 
    ]);
}

// ... other methods (update, destroy) ...
    

// Add other workflow methods (approve, reject, edit, destroy) here as needed...
}