<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Boq;
use App\Models\PurchaseRequisitionItem; // NEW: Import the line item model
use App\Models\User; 
use App\Models\Approval;

class PurchaseRequisition extends Model
{
    use HasFactory;

    protected $table = 'purchase_requisitions';

    protected $fillable = [
        'user_id',              // Initiator (Site Manager/PM)
        'boq_id',               // The overall BoQ/Project the PR is for
        
        // REMOVED: 'boq_material_id' and 'qty_requested' (Now in PurchaseRequisitionItem)
        
        'required_by_date',
        'justification',
        'category',             // e.g., Material, Tooling, Service (If you still use this on the header)
        
        // NEW: Field added to hold the calculated total cost of all line items
        'cost_estimate',
        
        // Workflow fields
        'status',               
        'current_stage',        
        'approval_notes',       
    ];

    protected $casts = [
        'required_by_date' => 'date',
    ];

    // --- Relationships ---

    /**
     * The User (Site Manager/Initiator) who created the PR.
     */
    public function initiator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The main BoQ project this requisition is linked to.
     */
    public function project()
    {
        return $this->belongsTo(Boq::class, 'boq_id');
    }

    // REMOVED: public function material() (It no longer links to a single material)

    /**
     * NEW RELATIONSHIP: The multiple line items attached to this requisition.
     */
    public function items()
    {
        return $this->hasMany(PurchaseRequisitionItem::class, 'purchase_requisition_id');
    }
    
    /**
     * Helper method to determine the role responsible for the current approval stage.
     */
    public function currentApproverRole(): string
    {
        $stages = [
            1 => 'Quantity Surveyor',
            2 => 'Office Project Manager',
            3 => 'Procurement Officer',
        ];

        return $stages[$this->current_stage] ?? 'Unknown Approver (Stage ' . $this->current_stage . ')';
    }
     public function material()
    {
        return $this->belongsTo(BoqMaterial::class, 'boq_material_id');
    }

    public function approvals()
    {
        // Assuming your Approval model is named 'Approval' and has a foreign key 'purchase_requisition_id'
        return $this->hasMany(Approval::class, 'purchase_requisition_id');
    }
}