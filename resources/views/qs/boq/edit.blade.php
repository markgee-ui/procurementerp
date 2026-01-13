@extends('layouts.app') 

@section('content')
<style>
    /* Styling for budget feedback */
    .activity-over-budget {
        border: 2px solid red !important;
    }
    .budget-exceeded-text {
        color: red;
        font-weight: bold;
    }
    .budget-ok-text {
        color: green;
        font-weight: bold;
    }
    .activity-section {
        transition: border-color 0.3s;
    }
</style>

<div class="p-6 bg-white rounded-lg shadow-xl">
    <a href="{{ route('qs.boq.index') }}" class="text-sm text-blue-600 hover:text-blue-800 mb-4 inline-block">&larr; Back to BoQ List</a>
    <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">
        Edit Bill of Quantities (BoQ): {{ $boq->project_name }}
    </h1>

    <form action="{{ route('qs.boq.update', $boq) }}" method="POST">
        @csrf
        @method('PUT') {{-- Required for the update method --}}

        {{-- Project Header Details (Pre-populated) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 p-4 bg-gray-50 rounded-lg border">
            <div>
                <label for="project_name" class="block text-sm font-medium text-gray-700">Project Name:</label>
                <input type="text" id="project_name" name="project_name" value="{{ old('project_name', $boq->project_name) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border" 
                        placeholder="Enter Project Name">
            </div>
            <div>
                <label for="project_budget" class="block text-sm font-medium text-gray-700">Budget (Total):</label>
                <input type="number" id="project_budget" name="project_budget" step="0.01" value="{{ old('project_budget', $boq->project_budget) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border" 
                        placeholder="KSH 0.00">
            </div>
        </div>
        
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Validation Error!</strong>
                <span class="block sm:inline">Please check the form below for errors.</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <h2 class="text-xl font-bold text-gray-800 mt-8 mb-4">Project Activities & Materials</h2>

        {{-- Container for dynamically added Activity Sections --}}
        <div id="activity-sections-container" class="space-y-6">
            {{-- Existing activities will be loaded here by JavaScript --}}
        </div>
        
        <div class="flex space-x-4 mt-6 pt-4 border-t">
            <button type="button" id="add-activity-btn" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                + Add Project Activity/Section
            </button>
        </div>

        <div class="mt-8 pt-4 border-t">
            <button type="submit" class="px-6 py-3 text-lg font-bold text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" id="submit-boq-btn">
                Update BoQ
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- DATA Initialization from PHP ---
        // Using existingBoqData to load initial values
        const existingBoqData = @json($boq->load('activities.materials'));
        
        const activityOptions = [
            { value: 'Foundation', text: 'Foundation' },
            { value: 'Walling', text: 'Walling' },
            { value: 'Roofing', text: 'Roofing' },
            { value: 'Wall Finishes', text: 'Wall Finishes' },
            { value: 'Floor Finishes', text: 'Floor Finishes' },
            { value: 'Staircase Railing Touch-Up', text: 'Staircase Railing Touch-Up' },
            { value: 'Furniture & Fittings', text: 'Furniture & Fittings' },
            { value: 'Branding Works', text: 'Branding Works' },
            { value: 'Plumbing', text: 'Plumbing' },
            { value: 'Foul Drainage', text: 'Foul Drainage' },
            { value: 'Doors', text: 'Doors' },
            { value: 'Windows', text: 'Windows' },
            { value: 'Electrical', text: 'Electrical' },
            { value: 'Ceiling', text: 'Ceiling' },
            { value: 'Hvac Installations', text: 'HVAC Installations' },
            { value: 'Landscaping', text: 'Landscaping' },
            { value: 'Joinery Works', text: 'Joinery Works' },
            { value: 'Preliminaries', text: 'Preliminaries' },
            { value: 'Site Clearance & Top Soil Stripping', text: 'Site Clearance' },
            { value: 'Earthworks', text: 'Earthworks' },
            { value: 'Natural Material Base & Subbase', text: 'Natural Material Base and Subbase' },
            { value: 'Surface Course', text: 'Surface Course' },
            { value: 'Road Furniture', text: 'Road Furniture' },
            { value: 'Street Lighting', text: 'Street Lighting' },
            { value: 'Valves Hydrants & air valves', text: 'Valves Hydrants & air valves' },
            { value: 'Fittings, bends and transition pieces', text: 'Fittings, bends and transition pieces' },
            { value: 'Civil works and trenching', text: 'Civil works and trenching' },
            { value: 'Spares, QA & Trenching', text: 'Spares, QA & Trenching' },
            { value: 'Provisional', text: 'Provisional' },
            { value: 'Miscellaneous', text: 'Miscellaneous' },
        ];
        
        const container = document.getElementById('activity-sections-container');
        const addActivityBtn = document.getElementById('add-activity-btn');
        const submitBtn = document.getElementById('submit-boq-btn');
        let activityIndex = 0; 
        
        // --- TEMPLATE FUNCTIONS ---

        function getMaterialRowTemplate(activityKey, rowKey, material = {}) {
            // Include material ID for update logic
            const materialId = material.id || '';
            const item = material.item || '';
            const specs = material.specs || '';
            const unit = material.unit || '';
            const qty = material.qty || 1;
            const rate = material.rate || 0.00;
            const remarks = material.remarks || '';
            const lineTotal = (qty * rate).toFixed(2);

            return `
                <tr data-row-id="${activityKey}-${rowKey}" class="material-row">
                    <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900 index-display">${rowKey}</td>
                    <td class="px-6 py-2 whitespace-nowrap">
                        ${materialId ? `<input type="hidden" name="activities[${activityKey}][materials][${rowKey}][id]" value="${materialId}">` : ''}
                        <input type="text" name="activities[${activityKey}][materials][${rowKey}][item]" value="${item}" class="w-full text-sm border-gray-300 rounded-md p-1" required>
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap"><input type="text" name="activities[${activityKey}][materials][${rowKey}][specs]" value="${specs}" class="w-full text-sm border-gray-300 rounded-md p-1"></td>
                    <td class="px-3 py-2 whitespace-nowrap"><input type="text" name="activities[${activityKey}][materials][${rowKey}][unit]" value="${unit}" class="w-full text-sm border-gray-300 rounded-md p-1"></td>
                    
                    {{-- INPUTS FOR CALCULATION --}}
                    <td class="px-3 py-2 whitespace-nowrap">
                        <input type="number" name="activities[${activityKey}][materials][${rowKey}][qty]" value="${qty}" class="w-full text-sm border-gray-300 rounded-md p-1 input-qty" required min="0.01" step="0.01">
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                        <input type="number" name="activities[${activityKey}][materials][${rowKey}][rate]" value="${rate}" class="w-full text-sm border-gray-300 rounded-md p-1 input-rate" step="0.01" min="0">
                    </td>
                    
                    <td class="px-6 py-2 whitespace-nowrap">
                        <span class="text-sm font-semibold line-total">${lineTotal}</span> 
                    </td>
                    
                    <td class="px-6 py-2 whitespace-nowrap"><input type="text" name="activities[${activityKey}][materials][${rowKey}][remarks]" value="${remarks}" class="w-full text-sm border-gray-300 rounded-md p-1"></td>
                    <td class="px-2 py-2 whitespace-nowrap">
                        <button type="button" class="text-red-600 hover:text-red-900 remove-row-btn" data-action="remove-row">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 pointer-events-none">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </td>
                </tr>
            `;
        }

        function getActivitySectionTemplate(activityKey, activity = {}) {
            // Include activity ID for update logic
            const activityId = activity.id || ''; 
            const name = activity.name || '';
            const budget = activity.budget || 0.00;
            
            let optionsHtml = activityOptions.map(opt => 
                `<option value="${opt.value}" ${opt.value === name ? 'selected' : ''}>${opt.text}</option>`
            ).join('');

            return `
                <div class="activity-section bg-white p-4 rounded-lg border border-gray-200" data-activity-id="${activityKey}">
                    
                    {{-- Section Header/Toggle with Total Cost Display --}}
                    <div class="flex justify-between items-center cursor-pointer bg-gray-100 p-3 rounded-md mb-3 hover:bg-gray-200 toggle-section" data-action="toggle-section">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Activity: <span class="activity-name-display">${name || 'New Activity'}</span>
                        </h3>
                        <span class="text-md font-semibold mr-4">
                            Total Cost: <span class="activity-total-cost budget-ok-text">KSH 0.00</span>
                        </span>
                        <div class="flex items-center space-x-3">
                             <button type="button" class="remove-activity-btn text-red-500 hover:text-red-700" data-action="remove-activity" title="Remove Activity">
                                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 pointer-events-none">
                                     <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0-.97-4.85-2.091-2.091m7.136-1.92L21 21M7.879 2.479c1.522-.733 3.064-.975 4.6-.975 2.87 0 5.4 1.13 7.375 2.97l.115.118a1.5 1.5 0 0 1 0 2.122l-2.9 2.9h-5.25v2.25M11.25 10.5h-5.25v2.25M18 13.5h-2.25v2.25M18 15.75h-2.25v2.25M18 18h-2.25v2.25M18 20.25h-2.25v2.25" />
                                 </svg>
                             </button>
                             <svg class="w-5 h-5 transform rotate-0 transition-transform duration-300 chevron" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                 <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                             </svg>
                        </div>
                    </div>

                    {{-- Section Content (Collapsible) --}}
                    <div class="activity-content space-y-4" style="display: block;">
                        ${activityId ? `<input type="hidden" name="activities[${activityKey}][id]" value="${activityId}">` : ''}

                        {{-- Activity Selector and Budget Input --}}
                        <div class="mb-4 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Select Activity Group:</label>
                                <select name="activities[${activityKey}][name]" required data-action="update-name"
                                         class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm p-2 border activity-selector">
                                    <option value="">-- Select Activity Group --</option>
                                    ${optionsHtml}
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Budget for this Activity (KSH):</label>
                                <input type="number" name="activities[${activityKey}][budget]" step="0.01" value="${budget}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm p-2 border input-activity-budget" 
                                        placeholder="KSH Activity Budget">
                            </div>
                        </div>

                        <h4 class="text-md font-semibold text-gray-700 mt-4 border-b pb-1">Materials Breakdown:</h4>

                        {{-- Materials Table --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">Sr. No</th>
                                        <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/5">Material/ Item</th>
                                        <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/5">Specifications</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Unit</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Qty</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Rate (KSH)</th> 
                                        <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Line Total</th>
                                        <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                                        <th class="px-2 py-2 w-10"></th>
                                    </tr>
                                </thead>
                                <tbody class="material-rows-container bg-white divide-y divide-gray-200" data-activity-id="${activityKey}">
                                    ${activity.materials && activity.materials.length > 0 ? activity.materials.map((m, mIdx) => getMaterialRowTemplate(activityKey, mIdx + 1, m)).join('') : getMaterialRowTemplate(activityKey, 1)}
                                </tbody>
                            </table>
                        </div>

                        <button type="button" class="add-material-row-btn mt-2 px-3 py-1 text-xs font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600" data-action="add-row">
                            + Add Material Item
                        </button>
                    </div>
                </div>
            `;
        }

        // --- CALCULATION AND VALIDATION FUNCTIONS (NEW) ---

        function calculateAndValidateActivity(activityId) {
            const activitySection = container.querySelector(`.activity-section[data-activity-id="${activityId}"]`);
            if (!activitySection) return;

            const budgetInput = activitySection.querySelector('.input-activity-budget');
            const totalCostDisplay = activitySection.querySelector('.activity-total-cost');
            
            const activityBudget = parseFloat(budgetInput.value) || 0;
            let activityRunningTotal = 0;

            // 1. Loop through all material rows in this activity
            activitySection.querySelectorAll('.material-row').forEach(row => {
                const qty = parseFloat(row.querySelector('.input-qty').value) || 0;
                const rate = parseFloat(row.querySelector('.input-rate').value) || 0;
                const lineTotal = qty * rate;

                // Update the line total display
                row.querySelector('.line-total').textContent = lineTotal.toFixed(2);
                activityRunningTotal += lineTotal;
            });
            
            // Format total
            const formattedTotal = 'KSH ' + activityRunningTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            // 2. Compare Total Cost vs. Budget
            let isOverBudget = false;

            if (activityBudget > 0 && activityRunningTotal > activityBudget) {
                isOverBudget = true;
                activitySection.classList.add('activity-over-budget');
                totalCostDisplay.classList.remove('budget-ok-text');
                totalCostDisplay.classList.add('budget-exceeded-text');
                totalCostDisplay.textContent = `${formattedTotal} (EXCEEDS KSH ${activityBudget.toFixed(2)})`;
            } else {
                activitySection.classList.remove('activity-over-budget');
                totalCostDisplay.classList.remove('budget-exceeded-text');
                totalCostDisplay.classList.add('budget-ok-text');
                totalCostDisplay.textContent = formattedTotal;
            }
            
            // 3. Update main submit button state
            checkAllBudgets();
        }

        function checkAllBudgets() {
            // Checks if ANY activity section is marked as over budget
            const isAnyOverBudget = document.querySelector('.activity-over-budget') !== null;
            submitBtn.disabled = isAnyOverBudget;
            if (isAnyOverBudget) {
                submitBtn.textContent = '❌ Fix Budget Errors Before Updating';
            } else {
                submitBtn.textContent = 'Update BoQ';
            }
        }
        
        // --- HANDLER FUNCTIONS ---

        function updateRowIndexes(activityId) {
            const materialContainer = container.querySelector(`.material-rows-container[data-activity-id="${activityId}"]`);
            if (!materialContainer) return;
            
            materialContainer.querySelectorAll('.material-row').forEach((row, index) => {
                row.querySelector('.index-display').textContent = index + 1; 
                // Re-assign name attribute keys to maintain sequential indexing on submission
                const newKey = index + 1;
                row.querySelectorAll('input').forEach(input => {
                    const originalName = input.name;
                    // Regex to replace the material index key
                    const newName = originalName.replace(/(\[materials\]\[)\d+(\]\[)/, `$1${newKey}$2`);
                    input.name = newName;
                });
            });
        }
        
        function updateActivityDisplayName(event) {
            const selectElement = event.target;
            const activitySection = selectElement.closest('.activity-section');
            const displaySpan = activitySection.querySelector('.activity-name-display');
            displaySpan.textContent = selectElement.options[selectElement.selectedIndex].text;
        }

        function removeActivitySection(event) {
            if (!confirm('Are you sure you want to remove this entire activity section?')) return;
            event.target.closest('.activity-section').remove();
            checkAllBudgets(); // Re-check after removal
        }

        function removeMaterialRow(event) {
            const rowToRemove = event.target.closest('tr');
            const activityId = rowToRemove.closest('.activity-section').dataset.activityId;
            rowToRemove.remove();
            updateRowIndexes(activityId);
            calculateAndValidateActivity(activityId);
        }
        
        function toggleSection(event) {
            const header = event.target.closest('.toggle-section');
            if (!header) return;
            // Prevent toggling if the remove button was clicked
            if (event.target.closest('.remove-activity-btn')) return; 

            const content = header.nextElementSibling;
            const isVisible = content.style.display === 'block' || content.style.display === '';

            content.style.display = isVisible ? 'none' : 'block';
        }

        function addActivitySection() {
            activityIndex++;
            const newActivityHtml = getActivitySectionTemplate(activityIndex);
            container.insertAdjacentHTML('beforeend', newActivityHtml);
            attachListeners();
            // Recalculate and validate the new section
            calculateAndValidateActivity(activityIndex);
        }

        function addMaterialRow(event) {
            const button = event.target;
            const activitySection = button.closest('.activity-section');
            const activityKey = activitySection.dataset.activityId;
            const tbody = activitySection.querySelector('.material-rows-container');
            const nextRowKey = tbody.children.length + 1;
            
            const newRowHtml = getMaterialRowTemplate(activityKey, nextRowKey);
            tbody.insertAdjacentHTML('beforeend', newRowHtml);
            // Recalculate and validate after adding a row
            calculateAndValidateActivity(activityKey);
        }

        function attachListeners() {
            // Use event delegation for dynamic inputs (Qty, Rate, Budget)
             document.removeEventListener('input', inputChangeHandler);
             document.addEventListener('input', inputChangeHandler);

            // Re-attach listeners for dynamically added elements
            container.querySelectorAll('.add-material-row-btn').forEach(btn => {
                 // Remove existing listener before adding the new one
                 btn.removeEventListener('click', addMaterialRow);
                 btn.addEventListener('click', addMaterialRow);
            });
            container.querySelectorAll('.remove-row-btn').forEach(btn => {
                 btn.removeEventListener('click', removeMaterialRow);
                 btn.addEventListener('click', removeMaterialRow);
            });
            container.querySelectorAll('.remove-activity-btn').forEach(btn => {
                 btn.removeEventListener('click', removeActivitySection);
                 btn.addEventListener('click', removeActivitySection);
            });
            container.querySelectorAll('.activity-selector').forEach(select => {
                 select.removeEventListener('change', updateActivityDisplayName);
                 select.addEventListener('change', updateActivityDisplayName);
            });
            container.querySelectorAll('.toggle-section').forEach(header => {
                 header.removeEventListener('click', toggleSection);
                 header.addEventListener('click', toggleSection);
            });
        }

        function inputChangeHandler(e) {
            const target = e.target;
            // Check if the input affects cost calculation (Qty, Rate, or Activity Budget)
            if (target.classList.contains('input-qty') || 
                target.classList.contains('input-rate') ||
                target.classList.contains('input-activity-budget')) 
            {
                const activitySection = target.closest('.activity-section');
                const activityId = activitySection ? activitySection.dataset.activityId : null;
                if (activityId) {
                    calculateAndValidateActivity(activityId);
                }
            }
        }
        
        // --- INITIALIZATION FUNCTION ---
        
        function loadExistingBoq() {
            let maxId = 0;
            
            // 1. Render existing activities
            existingBoqData.activities.forEach((activity) => {
                 // Use the activity's actual ID for the array key
                const key = activity.id;
                maxId = Math.max(maxId, key); 
                
                const activitySectionHtml = getActivitySectionTemplate(key, activity);
                container.insertAdjacentHTML('beforeend', activitySectionHtml);
            });

            // 2. Set the index for the next *new* activity
            activityIndex = maxId + 1;
            
            // 3. Attach listeners to all loaded elements
            attachListeners();
            
            // 4. Run initial validation/calculation on all loaded sections
            existingBoqData.activities.forEach(activity => {
                 calculateAndValidateActivity(activity.id);
            });

            // 5. Hide the activity content for all sections initially for a cleaner look
            container.querySelectorAll('.activity-content').forEach(content => {
                content.style.display = 'none';
            });
        }

        // --- STARTUP ---
        loadExistingBoq(); 
        addActivityBtn.onclick = addActivitySection;
        
    });
</script>
@endpush