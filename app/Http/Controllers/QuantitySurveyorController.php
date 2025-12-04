<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        // 1. Validation (Example)
        $request->validate([
            'project_name' => 'required|string|max:255',
            'activity_phase' => 'required|string',
            'materials' => 'required|array',
            'materials.*.item' => 'required|string',
            'materials.*.qty' => 'required|numeric|min:1',
            // Add more specific validation rules for specs, unit, etc.
        ]);

        // 2. Processing/Saving Logic
        // Example: $project = Project::create(['name' => $request->project_name, ...]);
        // Example: $project->boqMaterials()->createMany($request->materials);

        // 3. Redirection
        return redirect()
            ->route('qs.index') // Redirect to the main QS index or the BoQ list
            ->with('success', 'Bill of Quantities (BoQ) successfully created for ' . $request->project_name);
    }
}