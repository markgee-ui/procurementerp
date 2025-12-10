<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Boq; // Used for project/BoQ list
use App\Models\PurchaseRequisition; // Primary Model
use App\Models\BoqMaterial; // Used to fetch material details for PR creation
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

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
        $requisition->load('user', 'boq', 'boqMaterial');

        return view('pm.requisitions.show', [
            'requisition' => $requisition,
        ]);
    }

    /**
     * Show the form for creating a new Purchase Requisition (PR).
     * * @param Boq $boq (Using route model binding for the BoQ)
     * @return \Illuminate\View\View
     */
    public function createRequisition(Boq $boq)
    {
        // Load the BoQ materials grouped by activity
        $activities = $boq->activities()->with('materials')->get();
        
        return view('pm.requisitions.create', [
            'boq' => $boq,
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
        // 1. Validation (Ensures all necessary fields are present and exist)
        $data = $request->validate([
            'boq_id'           => 'required|exists:boqs,id',           // The project/budget header ID
            'boq_material_id'  => 'required|exists:boq_materials,id',  // The specific material line item
            'qty_requested'    => 'required|numeric|min:0.01',         // The quantity requested
            'justification'    => 'required|string',                     // A reason for the request
            'required_by_date' => 'nullable|date',                      // Added as per migration update
        ]);

        $qtyRequested = $data['qty_requested'];

        // 2. Retrieve BoQ Material details for consistent record creation
        try {
            // Find the BoQ Material line to extract item name, unit, and rate
            $boqMaterial = BoqMaterial::findOrFail($data['boq_material_id']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw ValidationException::withMessages(['boq_material_id' => 'The selected material line is invalid or not found.']);
        }

        // 3. Create the PR record
        $requisition = PurchaseRequisition::create([
            'user_id'           => Auth::id(),
            'boq_id'            => $data['boq_id'],
            'boq_material_id'   => $boqMaterial->id,
            
            // Populate item details from the BoQ Material line
            'item_name'         => $boqMaterial->item,
            'unit'              => $boqMaterial->unit,
            'qty_requested'     => $qtyRequested,
            'required_by_date'  => $data['required_by_date'] ?? null,
            'justification'     => $data['justification'],
            'cost_estimate'     => $boqMaterial->rate * $qtyRequested, // Calculate estimated cost
            
            // Set initial approval status
            'status'            => 'Pending',
            'current_stage'     => 1, // Ready for Office PM approval
        ]);

        // 4. Success Response
        return redirect()->route('pm.requisitions.show', $requisition)
                         ->with('success', 'Purchase Requisition submitted for approval!');
    }
/**
 * Show the form for editing the specified Purchase Requisition.
 * @param PurchaseRequisition $requisition
 * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
 */
public function editRequisition(PurchaseRequisition $requisition)
{
    // You should use an authorization check here (e.g., $this->authorize('update', $requisition))
    
    // Only allow editing if the PR is still pending.
    if ($requisition->status !== 'Pending') {
        return redirect()->route('pm.requisitions.show', $requisition)
                         ->with('error', 'Only Pending requisitions can be edited.');
    }
    
    // Load BoQ details if needed for dynamic forms
    $boq = $requisition->boq;
    $activities = $boq->activities()->with('materials')->get();
    
    return view('pm.requisitions.edit', [
        'requisition' => $requisition,
        'boq' => $boq,
        'activities' => $activities,
    ]);
}

/**
 * Update the specified Purchase Requisition in storage.
 * @param \Illuminate\Http\Request $request
 * @param PurchaseRequisition $requisition
 * @return \Illuminate\Http\RedirectResponse
 */
public function updateRequisition(Request $request, PurchaseRequisition $requisition)
{
    // $this->authorize('update', $requisition);
    
    if ($requisition->status !== 'Pending') {
        return back()->with('error', 'Cannot update a requisition that is not Pending.');
    }
    
    $data = $request->validate([
        'qty_requested'    => 'required|numeric|min:0.01',
        'justification'    => 'required|string',
        'required_by_date' => 'nullable|date',
        // Note: Changing boq_material_id/boq_id is complex, so we usually restrict those.
    ]);
    
    // Re-calculate cost estimate if quantity changed
    $boqMaterial = $requisition->boqMaterial;
    $data['cost_estimate'] = $boqMaterial->rate * $data['qty_requested'];

    $requisition->update($data);

    return redirect()->route('pm.requisitions.show', $requisition)
                     ->with('success', 'Purchase Requisition updated successfully.');
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
    
    // Add other workflow methods (approve, reject, edit, destroy) here as needed...
}