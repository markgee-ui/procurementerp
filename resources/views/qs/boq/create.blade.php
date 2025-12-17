@extends('layouts.app') 
{{-- Assuming you have a main layout file --}}

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
    <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">
        Bill of Quantities (BoQ) 
    </h1>

    <form action="{{ route('qs.boq.store') }}" method="POST">
        @csrf

        {{-- Project Header Details --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 p-4 bg-gray-50 rounded-lg border">
            <div>
                <label for="project_name" class="block text-sm font-medium text-gray-700">Project Name:</label>
                <input type="text" id="project_name" name="project_name" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border" 
                        placeholder="Enter Project Name">
            </div>
             <div>
                <label for="project_budget" class="block text-sm font-medium text-gray-700">Budget (Total Project Estimate):</label>
                <input type="number" id="project_budget" name="project_budget" step="0.01"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border" 
                        placeholder="KSH 0.00">
            </div>
        </div>

        <h2 class="text-xl font-bold text-gray-800 mt-8 mb-4">Project Activities & Materials</h2>

        {{-- Container for dynamically added Activity Sections --}}
        <div id="activity-sections-container" class="space-y-6">
            {{-- Initial Activity Section will be added by JS below --}}
        </div>
        
        <div class="flex space-x-4 mt-6 pt-4 border-t">
            {{-- Button to add a new Activity section (e.g., 'Walling', 'Roofing') --}}
            <button type="button" id="add-activity-btn" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                + Add Project Activity/Section
            </button>
        </div>


        <div class="mt-8 pt-4 border-t">
            <button type="submit" class="px-6 py-3 text-lg font-bold text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" id="submit-boq-btn">
                Save Complete BoQ
            </button>
        </div>
    </form>
</div>
@endsection

{{-- JavaScript to handle dynamic sections and budget checks --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('activity-sections-container');
        const addActivityBtn = document.getElementById('add-activity-btn');
        const submitBtn = document.getElementById('submit-boq-btn');
        let activityIndex = 0; 
        
        const activityOptions = [
            { value: 'Foundation', text: 'Foundation' },
            { value: 'Masonry', text: 'Masonry' },
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
            { value: 'Miscellaneous', text: 'Miscellaneous' },

        ];

        // --- TEMPLATE FUNCTIONS ---

        function getMaterialRowTemplate(activityId, rowId) {
            return `
                <tr data-row-id="${activityId}-${rowId}" class="material-row">
                    <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900 index-display">${rowId}</td>
                    <td class="px-6 py-2 whitespace-nowrap"><input type="text" name="activities[${activityId}][materials][${rowId}][item]" class="w-full text-sm border-gray-300 rounded-md p-1" placeholder="e.g., Cement, Window Frame" required></td>
                    <td class="px-6 py-2 whitespace-nowrap"><input type="text" name="activities[${activityId}][materials][${rowId}][specs]" class="w-full text-sm border-gray-300 rounded-md p-1" placeholder="e.g., 40kg, Aluminum"></td>
                    <td class="px-3 py-2 whitespace-nowrap"><input type="text" name="activities[${activityId}][materials][${rowId}][unit]" class="w-full text-sm border-gray-300 rounded-md p-1" placeholder="e.g., Bag, SqM"></td>
                    
                    {{-- INPUTS FOR CALCULATION --}}
                    <td class="px-3 py-2 whitespace-nowrap">
                        <input type="number" name="activities[${activityId}][materials][${rowId}][qty]" 
                               class="w-full text-sm border-gray-300 rounded-md p-1 input-qty" 
                               required min="1" value="1">
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                        <input type="number" name="activities[${activityId}][materials][${rowId}][rate]" 
                               class="w-full text-sm border-gray-300 rounded-md p-1 input-rate" 
                               step="0.01" min="0" placeholder="0.00" value="0.00">
                    </td>
                    
                    <td class="px-6 py-2 whitespace-nowrap">
                        <span class="text-sm font-semibold line-total">0.00</span> 
                    </td>
                    
                    <td class="px-6 py-2 whitespace-nowrap"><input type="text" name="activities[${activityId}][materials][${rowId}][remarks]" class="w-full text-sm border-gray-300 rounded-md p-1"></td>
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

        function getActivitySectionTemplate(activityId) {
            let optionsHtml = activityOptions.map(opt => 
                `<option value="${opt.value}">${opt.text}</option>`
            ).join('');

            return `
                <div class="activity-section bg-white p-4 rounded-lg border border-gray-200" data-activity-id="${activityId}">
                    
                    {{-- Section Header/Toggle with Total Cost Display --}}
                    <div class="flex justify-between items-center cursor-pointer bg-gray-100 p-3 rounded-md mb-3 hover:bg-gray-200 toggle-section" data-action="toggle-section">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Activity <span class="activity-name-display">#${activityId}</span>
                        </h3>
                        <span class="text-md font-semibold mr-4">
                            Total Cost: <span class="activity-total-cost budget-ok-text">KSH 0.00</span>
                        </span>
                        <div class="flex items-center space-x-3">
                             <button type="button" class="remove-activity-btn text-red-500 hover:text-red-700" data-action="remove-activity">
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

                        {{-- Activity Selector and Budget Input --}}
                        <div class="mb-4 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Select Activity Group:</label>
                                <select name="activities[${activityId}][name]" required data-action="update-name"
                                         class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm p-2 border activity-selector">
                                    <option value="">-- Select Activity Group --</option>
                                    ${optionsHtml}
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Budget for this Activity (KSH):</label>
                                <input type="number" name="activities[${activityId}][budget]" step="0.01" value="0.00"
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
                                <tbody class="material-rows-container bg-white divide-y divide-gray-200" data-activity-id="${activityId}">
                                    ${getMaterialRowTemplate(activityId, 1)}
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

        // --- HANDLER FUNCTIONS ---
        
        function updateRowIndexes(activityId) {
            const materialContainer = container.querySelector(`.material-rows-container[data-activity-id="${activityId}"]`);
            if (!materialContainer) return;
            
            materialContainer.querySelectorAll('.material-row').forEach((row, index) => {
                // Update the visible Sr. No
                row.querySelector('.index-display').textContent = index + 1; 
            });
        }
        
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
                submitBtn.textContent = '❌ Fix Budget Errors Before Saving';
            } else {
                submitBtn.textContent = 'Save Complete BoQ';
            }
        }
        
        function toggleSection(header) {
            const content = header.nextElementSibling;
            const chevron = header.querySelector('.chevron');
            
            const isVisible = content.style.display === 'block' || content.style.display === '';

            content.style.display = isVisible ? 'none' : 'block';
            chevron.classList.toggle('rotate-180', isVisible);
        }
        
        function updateActivityDisplayName(selectElement) {
            const activitySection = selectElement.closest('.activity-section');
            const displayNameSpan = activitySection.querySelector('.activity-name-display');
            const selectedText = selectElement.options[selectElement.selectedIndex].text;
            
            if (displayNameSpan) {
                displayNameSpan.textContent = selectedText && selectElement.value ? selectedText : `Activity #${activitySection.dataset.activityId}`;
            }
        }
        
        function addActivitySection() {
            activityIndex++;
            const newSectionHtml = getActivitySectionTemplate(activityIndex);
            container.insertAdjacentHTML('beforeend', newSectionHtml);
            // Immediately calculate/validate the newly added section
            calculateAndValidateActivity(activityIndex);
        }

        // --- EVENT DELEGATION ---

        document.addEventListener('click', function(e) {
            const target = e.target.closest('[data-action]');
            if (!target) return;

            const activitySection = e.target.closest('.activity-section');
            const activityId = activitySection ? activitySection.dataset.activityId : null;
            const materialContainer = activityId ? container.querySelector(`.material-rows-container[data-activity-id="${activityId}"]`) : null;

            switch (target.dataset.action) {
                case 'add-row':
                    if (materialContainer) {
                        const currentRowCount = materialContainer.querySelectorAll('.material-row').length;
                        const newRowId = currentRowCount + 1;
                        materialContainer.insertAdjacentHTML('beforeend', getMaterialRowTemplate(activityId, newRowId));
                        // After adding a row, recalculate and revalidate
                        calculateAndValidateActivity(activityId); 
                    }
                    break;
                case 'remove-row':
                    const rowToRemove = target.closest('tr');
                    if (rowToRemove) {
                        rowToRemove.remove();
                        // Re-index visually and recalculate after removal
                        updateRowIndexes(activityId);
                        calculateAndValidateActivity(activityId); 
                    }
                    break;
                case 'remove-activity':
                    if (activitySection) {
                        activitySection.remove();
                        // Check budgets globally after removing a section
                        checkAllBudgets();
                    }
                    break;
                case 'toggle-section':
                    if (e.target.closest('.remove-activity-btn')) {
                         return;
                    }
                    toggleSection(target);
                    break;
            }
        });
        
        document.addEventListener('change', function(e) {
            if (e.target.dataset.action === 'update-name') {
                updateActivityDisplayName(e.target);
            }
        });

        document.addEventListener('input', function(e) {
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
        });

        // --- INITIALIZATION ---
        addActivitySection(); 
        addActivityBtn.onclick = addActivitySection;
    });
</script>
@endpush