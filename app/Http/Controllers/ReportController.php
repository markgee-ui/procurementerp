<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequisition;
use App\Models\PurchaseOrder;
use App\Models\Boq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
{
    $role = Auth::user()->role;
    // Fetch unique project names from the Boq model
    $projects = Boq::select('project_name')->distinct()->get();
    
    return view('reports.index', compact('role', 'projects'));
}

   public function exportCSV(Request $request, $type)
{
    $user = Auth::user();
    $fileName = $type . '_report_' . date('Y-m-d') . '.csv';

    $callback = function() use ($type, $user, $request) {
        $file = fopen('php://output', 'w');
        
        $serialNo = 1;

        if ($type === 'requisitions') {
            // Updated Headers for Detailed Requisition Report
            fputcsv($file, [
                'Serial No', 
                'PR Date', 
                'PR No', 
                'Item Description', 
                'Unit', 
                'Qty Ordered', 
                'Project Name', 
                'Project Activity', 
                'Status'
            ]);

            // Eager load items, the material details, the activity, and the main project
            $query = PurchaseRequisition::with(['items.boqMaterial', 'items.boqActivity', 'project']);
        } else {
            // Purchase Order Headers
            fputcsv($file, ['Serial No', 'P.O Date', 'P.O No', 'Supplier Name', 'Item Description', 'Unit', 'Unit Price', 'Qty', 'Total Amt', 'Project Name']);
            $query = PurchaseOrder::with(['supplier', 'items.product']);
        }

        // --- Role Scoping ---
        if ($user->role === 'pm') {
            if ($type === 'requisitions') {
                // FIXED: Changed 'initiator_id' to 'user_id' to match your Model
                $query->where('user_id', $user->id);
            } else {
                // Scoping POs by project_name string
                $query->where('project_name', $user->site_name ?? 'NONE');
            }
        }

        // --- Filters ---
        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->project_name && $request->project_name !== 'all') {
    if ($type === 'requisitions') {
        // Requisitions link to Boq model via boq_id
        $query->whereHas('project', function($q) use ($request) {
            $q->where('project_name', $request->project_name);
        });
    } else {
        // Purchase Orders have project_name as a direct string column
        $query->where('project_name', $request->project_name);
    }
}

        $query->chunk(100, function($records) use ($file, $type, &$serialNo) {
            foreach ($records as $record) {
                if ($type === 'requisitions') {
                    // Iterate through each item in the Requisition
                    foreach ($record->items as $item) {
                        fputcsv($file, [
                            $serialNo++,                                         // Serial No
                            $record->created_at->format('Y-m-d'),                // PR Date
                            'PR-' . $record->id,                                 // PR No
                            $item->item_name ?? $item->boqMaterial->item ?? 'N/A', // Item Description
                            $item->unit ?? 'N/A',                                // Unit
                            $item->qty_requested,                                // Qty Ordered
                            $record->project->project_name ?? 'N/A',             // Project Name
                            $item->boqActivity->name ?? 'N/A',          // Project Activity
                            $record->status                                      // Status
                        ]);
                    }
                } else {
                    // Purchase Order Item Loop
                    foreach ($record->items as $item) {
                        fputcsv($file, [
                            $serialNo++,
                            $record->order_date ? $record->order_date->format('Y-m-d') : $record->created_at->format('Y-m-d'),
                            $record->order_number,
                            $record->supplier->name ?? 'N/A',
                            $item->product->item ?? 'N/A',
                            $item->product->description ?? 'N/A',
                            $item->product->unit ?? 'N/A',
                            number_format($item->unit_price, 2),
                            $item->quantity,
                            number_format($item->line_total, 2),
                            $record->project_name
                        ]);
                    }
                }
            }
        });
        fclose($file);
    };

    return response()->stream($callback, 200, [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
    ]);
}
}