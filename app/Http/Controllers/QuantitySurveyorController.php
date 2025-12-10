<?php

namespace App\Http\Controllers;

use App\Models\Boq;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule; // Added for validation

class QuantitySurveyorController extends Controller
{
    /**
     * Display the QS module dashboard/index page.
     * * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        return view('qs.index');
    }
    
    public function indexBoq(Request $request)
    {
        $query = Boq::query();
        if ($request->has('search') && $request->search != '') {
            $query->where('project_name', 'like', '%' . $request->search . '%');
        }
        if ($request->has('min_budget') && is_numeric($request->min_budget)) {
            $query->where('project_budget', '>=', $request->min_budget);
        }
        
        $boqs = $query->latest()->paginate(10);
        
        return view('qs.boq.index', compact('boqs'));
    }

    public function createBoq()
    {
        return view('qs.boq.create');
    }

    /**
     * Store a newly created Bill of Quantities (BoQ) in storage, including budget check.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeBoq(Request $request)
    {
        // 1. Comprehensive Validation
        $data = $request->validate([
            'project_name' => 'required|string|max:255',
            'project_budget' => 'nullable|numeric|min:0',
            
            'activities' => 'required|array',
            'activities.*.name' => 'required|string|max:100',
            'activities.*.budget' => 'nullable|numeric|min:0',
            
            'activities.*.materials' => 'required|array',
            'activities.*.materials.*.item' => 'required|string|max:255',
            'activities.*.materials.*.specs' => 'nullable|string|max:255',
            'activities.*.materials.*.unit' => 'nullable|string|max:50',
            'activities.*.materials.*.qty' => 'required|numeric|min:0.01',
            'activities.*.materials.*.rate' => 'required|numeric|min:0', 
            'activities.*.materials.*.remarks' => 'nullable|string|max:500',
        ]);
        
        try {
            // Server-Side Budget Checks BEFORE saving anything
            foreach ($data['activities'] as $activityData) {
                $activityBudget = (float) $activityData['budget'];
                $calculatedActivityTotal = 0;
                
                // Calculate total cost for the materials in this activity
                if (isset($activityData['materials']) && is_array($activityData['materials'])) {
                    foreach ($activityData['materials'] as $material) {
                        $calculatedActivityTotal += ((float) $material['qty'] * (float) $material['rate']);
                    }
                }
                
                // Check if budget is defined AND total exceeds it
                if ($activityBudget > 0 && $calculatedActivityTotal > $activityBudget) {
                    return back()
                        ->withInput()
                        ->with('error', "Budget Error: The calculated total cost (KSH " . number_format($calculatedActivityTotal, 2) . ") for activity '{$activityData['name']}' exceeds the allocated budget (KSH " . number_format($activityBudget, 2) . ").");
                }
            }
            
            // 2. Save the main BoQ/Project record
            $boq = Boq::create([
                'project_name' => $data['project_name'],
                'project_budget' => $data['project_budget'],
            ]);

            // 3. Loop through activities and save materials (Now that checks have passed)
            foreach ($data['activities'] as $activityData) {
                
                $activity = $boq->activities()->create([
                    'name' => $activityData['name'],
                    'budget' => $activityData['budget'],
                ]);

                $materialsData = array_values($activityData['materials']);
                $activity->materials()->createMany($materialsData); 
            }

            // 4. Redirection
            return redirect()
                ->route('qs.index') 
                ->with('success', 'Bill of Quantities successfully saved for **' . $data['project_name'] . '**. All ' . count($data['activities']) . ' activities recorded.');
                
        } catch (\Exception $e) {
            \Log::error("BoQ Store Error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            
            return back()
                ->withInput()
                ->with('error', 'Failed to save the BoQ. Please check the data and try again.');
        }
    }

    public function showBoq(Boq $boq)
    {
        $boq->load('activities.materials');
        return view('qs.boq.show', compact('boq'));
    }

    public function editBoq(Boq $boq)
    {
        $boq->load('activities.materials');

        $activityOptions = [
            'foundation' => 'Foundation',
            'masonry' => 'Walling/Masonry',
            'roofing' => 'Roofing',
            'finishes' => 'Finishes',
            'services' => 'Services (Plumbing/Electrical)',
        ];

        return view('qs.boq.edit', compact('boq', 'activityOptions'));
    }

    /**
     * Update the specified BoQ in storage, including budget check.
     * * @param \Illuminate\Http\Request $request
     * @param \App\Models\Boq $boq
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateBoq(Request $request, Boq $boq) 
    {
        // 1. Validation (Similar to store, but account for IDs if present)
        $data = $request->validate([
            'project_name' => 'required|string|max:255',
            'project_budget' => 'nullable|numeric|min:0',
            
            'activities' => 'required|array',
            'activities.*.id' => 'nullable|integer|exists:boq_activities,id', // For existing activities
            'activities.*.name' => 'required|string|max:100',
            'activities.*.budget' => 'nullable|numeric|min:0',
            
            'activities.*.materials' => 'required|array',
            'activities.*.materials.*.id' => 'nullable|integer|exists:boq_materials,id', // For existing materials
            'activities.*.materials.*.item' => 'required|string|max:255',
            'activities.*.materials.*.specs' => 'nullable|string|max:255',
            'activities.*.materials.*.unit' => 'nullable|string|max:50',
            'activities.*.materials.*.qty' => 'required|numeric|min:0.01',
            'activities.*.materials.*.rate' => 'required|numeric|min:0', 
            'activities.*.materials.*.remarks' => 'nullable|string|max:500',
        ]);

        try {
             // Server-Side Budget Checks BEFORE saving anything
            foreach ($data['activities'] as $activityData) {
                $activityBudget = (float) $activityData['budget'];
                $calculatedActivityTotal = 0;
                
                if (isset($activityData['materials']) && is_array($activityData['materials'])) {
                    foreach ($activityData['materials'] as $material) {
                        $calculatedActivityTotal += ((float) $material['qty'] * (float) $material['rate']);
                    }
                }
                
                if ($activityBudget > 0 && $calculatedActivityTotal > $activityBudget) {
                    return back()
                        ->withInput()
                        ->with('error', "Budget Error: The calculated total cost (KSH " . number_format($calculatedActivityTotal, 2) . ") for activity '{$activityData['name']}' exceeds the allocated budget (KSH " . number_format($activityBudget, 2) . ").");
                }
            }

            // 2. Update the main BoQ record
            $boq->update([
                'project_name' => $data['project_name'],
                'project_budget' => $data['project_budget'],
            ]);

            // 3. Sync Activities and Materials (Complex but necessary for dynamic nested forms)
            $activityIdsToKeep = [];

            foreach ($data['activities'] as $activityData) {
                
                // Determine if we are updating an existing activity or creating a new one
                if (isset($activityData['id'])) {
                    $activity = $boq->activities()->find($activityData['id']);
                    // If activity is found, update it. If not found, skip or log (depends on your desired error handling)
                    if ($activity) {
                        $activity->update([
                            'name' => $activityData['name'],
                            'budget' => $activityData['budget'],
                        ]);
                    } else {
                        // Skip or log if existing ID is provided but model not found
                        continue;
                    }
                } else {
                    // Create a new activity
                    $activity = $boq->activities()->create([
                        'name' => $activityData['name'],
                        'budget' => $activityData['budget'],
                    ]);
                }

                $activityIdsToKeep[] = $activity->id;
                
                $materialIdsToKeep = [];
                
                foreach ($activityData['materials'] as $materialData) {
                    if (isset($materialData['id'])) {
                        // Update existing material
                        $material = $activity->materials()->find($materialData['id']);
                        if ($material) {
                            $material->update($materialData);
                        }
                    } else {
                        // Create new material
                        $material = $activity->materials()->create($materialData);
                    }
                    $materialIdsToKeep[] = $material->id;
                }
                
                // Delete materials that were removed from the form
                $activity->materials()->whereNotIn('id', $materialIdsToKeep)->delete();
            }

            // Delete activities that were removed from the form
            $boq->activities()->whereNotIn('id', $activityIdsToKeep)->delete();


            // 4. Redirection
            return redirect()
                ->route('qs.boq.index')
                ->with('success', 'Bill of Quantities for **' . $boq->project_name . '** updated successfully!');

        } catch (\Exception $e) {
            \Log::error("BoQ Update Error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            
            return back()
                ->withInput()
                ->with('error', 'Failed to update the BoQ. Please check the data and try again.');
        }
    }


    public function destroyBoq(Boq $boq)
    {
        $boq->delete();

        return redirect()
            ->route('qs.boq.index')
            ->with('success', 'Bill of Quantities for **' . $boq->project_name . '** deleted successfully.');
    }

    public function downloadBoq(Boq $boq)
    {
        $boq->load('activities.materials'); 

        $pdf = Pdf::loadView('qs.boq.boq_pdf', compact('boq'));
        
        $filename = 'BoQ-' . str_replace(' ', '_', $boq->project_name) . '.pdf';

        return $pdf->download($filename);
    }
}