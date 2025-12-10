<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Boq;
use App\Models\BoqMaterial;
use App\Models\User; // Assuming 'User' model for the initiator

class PurchaseRequisition extends Model
{
    use HasFactory;

    // The table name is typically pluralized, but explicitly define it for clarity
    protected $table = 'purchase_requisitions';

    protected $fillable = [
        'user_id',             // Initiator (Site Manager/PM)
        'boq_id',          // The overall BoQ/Project the PR is for
        'boq_material_id',     // The specific BoQ item being requested
        'qty_requested',
        'required_by_date',
        'justification',
        'category',            // e.g., Material, Tooling, Service
        
        // Workflow fields
        'status',              // e.g., Pending, Approved, Rejected, Procurement
        'current_stage',       // 1 (Site Manager), 2 (Office PM), 3 (Procurement)
        'approval_notes',      // Notes from the Office PM/Approver
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

    /**
     * The specific material item requested from the BoQ.
     */
    public function material()
    {
        return $this->belongsTo(BoqMaterial::class, 'boq_material_id');
    }
}