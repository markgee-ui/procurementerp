<?php

namespace App\Http\Controllers;

use App\Models\Boq;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class QuantitySurveyorController extends Controller
{
    /**
     * Display the QS module dashboard/index page.
     * * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        // This remains the dashboard/index route for the QS module (e.g., /qs)
        // You will eventually return the main QS dashboard view here.
        return view('qs.index'); 
    }
    public function indexBoq(Request $request)
    {
        // Start building the query
        $query = Boq::query();

        // --- Filtering Logic ---
        if ($request->has('search') && $request->search != '') {
            $query->where('project_name', 'like', '%' . $request->search . '%');
        }

        // Example: Filter by project budget range
        if ($request->has('min_budget') && is_numeric($request->min_budget)) {
            $query->where('project_budget', '>=', $request->min_budget);
        }
        
        // Fetch the BoQs with pagination
        $boqs = $query->latest()->paginate(10); 
        
        // Pass the BoQs and current filter values to the view
        return view('qs.boq.index', compact('boqs'));
    }

    /**
     * Show the form for creating a new Bill of Quantities (BoQ) for a project.
     * Corresponds to the BoQ creation form view.
     * * @return \Illuminate\View\View
     */
    public function createBoq()
    {
        // Renders the view containing the BoQ input form (the one we just created)
        return view('qs.boq.create');
    }

    /**
     * Store a newly created Bill of Quantities (BoQ) in storage.
     * This method handles the POST request from the BoQ creation form.
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
        
        // Validate the main activities array
        'activities' => 'required|array',
        'activities.*.name' => 'required|string|max:100', // e.g., 'foundation'
        'activities.*.budget' => 'nullable|numeric|min:0',
        
        // Validate the nested materials array
        'activities.*.materials' => 'required|array',
        'activities.*.materials.*.item' => 'required|string|max:255',
        'activities.*.materials.*.specs' => 'nullable|string|max:255',
        'activities.*.materials.*.unit' => 'nullable|string|max:50',
        'activities.*.materials.*.qty' => 'required|numeric|min:0.01',
        'activities.*.materials.*.rate' => 'required|numeric|min:0', 
        'activities.*.materials.*.remarks' => 'nullable|string|max:500',
    ]);
    
    try {
        // 2. Save the main BoQ/Project record
        $boq = Boq::create([
            'project_name' => $data['project_name'],
            'project_budget' => $data['project_budget'],
            // 'user_id' => auth()->id(), // Optional: Link to a user/QS
        ]);

        // 3. Loop through activities and save materials
        foreach ($data['activities'] as $activityData) {
            
            // Create the BoQ Activity record
            $activity = $boq->activities()->create([
                'name' => $activityData['name'],
                'budget' => $activityData['budget'],
            ]);

            // Prepare materials array: array_values() ensures the materials array is 
            // numerically indexed (0, 1, 2...), which is compatible with createMany(), 
            // even if the form keys were non-sequential.
            $materialsData = array_values($activityData['materials']);

            // Save the materials using createMany(), which automatically sets the boq_activity_id
            $activity->materials()->createMany($materialsData); 
        }

        // 4. Redirection
        return redirect()
            ->route('qs.index') 
            ->with('success', 'Bill of Quantities successfully saved for **' . $data['project_name'] . '**. All ' . count($data['activities']) . ' activities recorded.');
            
    } catch (\Exception $e) {
        // Log the error for debugging
        \Log::error("BoQ Store Error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        
        return back()
            ->withInput()
            ->with('error', 'Failed to save the BoQ. Please check the data and try again.');
    }
}
/**
     * Display the specified Bill of Quantities (BoQ) and its details.
     * Corresponds to the 'qs.boq.show' route.
     *
     * @param \App\Models\Boq $boq
     * @return \Illuminate\View\View
     */
    public function showBoq(Boq $boq)
    {
        // Eager load related activities and materials for efficient display
        $boq->load('activities.materials');

        return view('qs.boq.show', compact('boq'));
    }
    /**
     * Show the form for editing the specified Bill of Quantities (BoQ).
     * Corresponds to the 'qs.boq.edit' route.
     *
     * @param \App\Models\Boq $boq
     * @return \Illuminate\View\View
     */
    public function editBoq(Boq $boq)
    {
        // Eager load data needed for the form
        $boq->load('activities.materials');

        // Example data for activity options (Same as in your JS template)
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
     * Remove the specified BoQ from storage.
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyBoq(Boq $boq)
    {
        $boq->delete();

        return redirect()
            ->route('qs.boq.index')
            ->with('success', 'Bill of Quantities for **' . $boq->project_name . '** deleted successfully.');
    }
    /**
 * Downloads the detailed BoQ view as a PDF.
 * Corresponds to the 'qs.boq.download' route.
 *
 * @param Boq $boq
 * @return \Illuminate\Http\Response
 */
public function downloadBoq(Boq $boq)
{
    // Load necessary relationships for the view
    $boq->load('activities.materials'); 

    // Load the Blade view 'qs.boq.show' to be rendered as PDF
    $pdf = PDF::loadView('qs.boq.show', compact('boq'));
    
    // Set a meaningful file name
    $filename = 'BoQ-' . str_replace(' ', '_', $boq->project_name) . '.pdf';

    // Stream or download the PDF
    return $pdf->download($filename);
}
}