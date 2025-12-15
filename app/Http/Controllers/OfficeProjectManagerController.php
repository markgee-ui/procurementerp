<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequisition;
use App\Models\User; // Assuming User model is needed for filtering/initiator
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OfficeProjectManagerController extends Controller
{
    /**
     * Display the OPM dashboard or main index.
     */
    public function index(): View
    {
        // This index can show summaries, quick stats, or simply redirect to the requisitions list.
        $pendingCount = PurchaseRequisition::where('current_stage', 2) // PRs awaiting OPM approval
                                            ->count();

        return view('opm.index', compact('pendingCount'));
    }

    /**
     * Display a list of Purchase Requisitions awaiting OPM (Stage 2) approval.
     */
    public function indexRequisitions(Request $request): View
    {
        // Define the target stage for OPM
        $targetStage = 2; 

        $query = PurchaseRequisition::where('current_stage', $targetStage)
            ->with(['project', 'initiator'])
            ->latest();

        // Optional: Filter by project (if needed)
        if ($request->filled('boq_id')) {
            $query->where('boq_id', $request->boq_id);
        }

        $requisitions = $query->paginate(15);
        
        // Fetch projects for the filter dropdown (assuming BoQ model holds project info)
        $boqs = DB::table('boqs')->select('id', 'project_name')->get();

        return view('opm.requisitions.index', compact('requisitions', 'boqs'));
    }

    /**
     * Display the details of a specific Purchase Requisition for review.
     */
    public function showRequisition(PurchaseRequisition $requisition): View|RedirectResponse
    {
        // Load relationships (Project, Initiator, Items, and their nested relationships)
        $requisition->load([
            'project', 
            'initiator', 
            'items.boqMaterial', 
            'items.boqActivity',
            'approvals' // To show history/notes
        ]);
        
        // Ensure the PR is at the correct stage for this role
        if ($requisition->current_stage !== 2) {
             return redirect()->route('opm.requisitions.index')
                             ->with('error', 'The requisition is not currently awaiting Office PM approval.');
        }

        return view('opm.requisitions.show', compact('requisition'));
    }

    /**
     * Approve the Purchase Requisition (moves to Stage 3 or final).
     */
    public function approveRequisition(Request $request, PurchaseRequisition $requisition)
    {
        // Basic check to ensure it's at the correct stage
        if ($requisition->current_stage !== 2) {
            return back()->with('error', 'Cannot approve: Requisition is not awaiting Office PM review.');
        }

        try {
            DB::transaction(function () use ($requisition) {
                // 1. Log the approval
                $requisition->approvals()->create([
                    'user_id' => Auth::id(),
                    'stage' => 2, // OPM Approval Stage
                    'status' => 'approved',
                    'notes' => 'Approved by Office Project Manager.',
                ]);
                
                // 2. Advance the stage
                $requisition->current_stage = 3; // Assuming Stage 3 is Procurement or Final
                $requisition->save();
            });

            return redirect()->route('opm.requisitions.index')->with('success', 'Purchase Requisition #'.$requisition->id.' approved successfully. It has been forwarded to the next stage (Procurement/Final).');

        } catch (\Exception $e) {
            return back()->with('error', 'Approval failed. Please try again. Error: ' . $e->getMessage());
        }
    }

    /**
     * Reject the Purchase Requisition and set the rejection notes.
     */
    public function rejectRequisition(Request $request, PurchaseRequisition $requisition)
    {
        $request->validate([
            'rejection_notes' => 'required|string|min:10',
        ]);
        
        // Basic check to ensure it's at the correct stage
        if ($requisition->current_stage !== 2) {
            return back()->with('error', 'Cannot reject: Requisition is not awaiting Office PM review.');
        }
        
        try {
            DB::transaction(function () use ($requisition, $request) {
                // 1. Log the rejection
                $requisition->approvals()->create([
                    'user_id' => Auth::id(),
                    'stage' => 2, // OPM Approval Stage
                    'status' => 'rejected',
                    'notes' => $request->rejection_notes,
                ]);

                // 2. Set the PR status to rejected and change the stage flag
                $requisition->is_approved = false;
                $requisition->current_stage = 99; // Using 99 to denote final rejection
                $requisition->save();
            });

            return redirect()->route('opm.requisitions.index')->with('success', 'Purchase Requisition #'.$requisition->id.' has been rejected and the initiator has been notified.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Rejection failed. Please try again.');
        }
    }
}