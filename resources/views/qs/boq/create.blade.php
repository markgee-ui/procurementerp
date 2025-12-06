@extends('layouts.app') 
{{-- Assuming you have a main layout file --}}

@section('content')
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
                <label for="project_budget" class="block text-sm font-medium text-gray-700">Budget (Total):</label>
                <input type="number" id="project_budget" name="project_budget" step="0.01"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border" 
                       placeholder="KSH 0.00">
            </div>
        </div>

        <h2 class="text-xl font-bold text-gray-800 mt-8 mb-4">Project Activities & Materials</h2>

        {{-- Container for dynamically added Activity Sections --}}
        <div id="activity-sections-container" class="space-y-6">
            {{-- Initial Activity Section will be added by JS below or via an @include --}}
        </div>
        
        <div class="flex space-x-4 mt-6 pt-4 border-t">
            {{-- Button to add a new Activity section (e.g., 'Walling', 'Roofing') --}}
            <button type="button" id="add-activity-btn" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                + Add Project Activity/Section
            </button>
        </div>


        <div class="mt-8 pt-4 border-t">
            <button type="submit" class="px-6 py-3 text-lg font-bold text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Save Complete BoQ
            </button>
        </div>
    </form>
</div>
@endsection

{{-- JavaScript to handle dynamic sections and rows --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('activity-sections-container');
        const addActivityBtn = document.getElementById('add-activity-btn');
        let activityIndex = 0; // Counter for unique activity keys
        
        // Example list of activities for the dropdown
        const activityOptions = [
            { value: 'foundation', text: 'Foundation' },
            { value: 'masonry', text: 'Walling/Masonry' },
            { value: 'roofing', text: 'Roofing' },
            { value: 'finishes', text: 'Finishes' },
            { value: 'services', text: 'Services (Plumbing/Electrical)' },
        ];

        // --- TEMPLATE FUNCTIONS ---

        // Template for a single material row
        function getMaterialRowTemplate(activityId, rowId) {
            return `
                <tr data-row-id="${activityId}-${rowId}">
                    <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">${rowId}</td>
                    <td class="px-6 py-2 whitespace-nowrap"><input type="text" name="activities[${activityId}][materials][${rowId}][item]" class="w-full text-sm border-gray-300 rounded-md p-1" placeholder="e.g., Cement, Window Frame" required></td>
                    <td class="px-6 py-2 whitespace-nowrap"><input type="text" name="activities[${activityId}][materials][${rowId}][specs]" class="w-full text-sm border-gray-300 rounded-md p-1" placeholder="e.g., 40kg, Aluminum"></td>
                    <td class="px-3 py-2 whitespace-nowrap"><input type="text" name="activities[${activityId}][materials][${rowId}][unit]" class="w-full text-sm border-gray-300 rounded-md p-1" placeholder="e.g., Bag, SqM"></td>
                    <td class="px-3 py-2 whitespace-nowrap"><input type="number" name="activities[${activityId}][materials][${rowId}][qty]" class="w-full text-sm border-gray-300 rounded-md p-1" required min="1"></td>
                    
                    {{-- 💡 NEW RATE INPUT ADDED HERE --}}
                    <td class="px-3 py-2 whitespace-nowrap"><input type="number" name="activities[${activityId}][materials][${rowId}][rate]" class="w-full text-sm border-gray-300 rounded-md p-1" step="0.01" min="0" placeholder="0.00"></td>
                    
                    <td class="px-6 py-2 whitespace-nowrap"><input type="text" name="activities[${activityId}][materials][${rowId}][remarks]" class="w-full text-sm border-gray-300 rounded-md p-1"></td>
                    <td class="px-2 py-2 whitespace-nowrap">
                        <button type="button" class="text-red-600 hover:text-red-900 remove-row-btn" data-activity-id="${activityId}" data-row="${rowId}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </td>
                </tr>
            `;
        }

        // Template for a full Activity section (Walling, Roofing, etc.)
        function getActivitySectionTemplate(activityId) {
            let optionsHtml = activityOptions.map(opt => 
                `<option value="${opt.value}">${opt.text}</option>`
            ).join('');

            return `
                <div class="activity-section bg-white p-4 rounded-lg border border-gray-200" data-activity-id="${activityId}">
                    
                    {{-- Section Header/Toggle --}}
                    <div class="flex justify-between items-center cursor-pointer bg-gray-100 p-3 rounded-md mb-3 hover:bg-gray-200 toggle-section">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Activity <span class="activity-name-display">#${activityId}</span>: <span class="text-indigo-600">(Select Below)</span>
                        </h3>
                        <div class="flex items-center space-x-3">
                             <button type="button" class="remove-activity-btn text-red-500 hover:text-red-700">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
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

                        {{-- Activity Selector --}}
                        <div class="mb-4 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Select Activity Group:</label>
                                <select name="activities[${activityId}][name]" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm p-2 border activity-selector">
                                    <option value="">-- Select Activity Group --</option>
                                    ${optionsHtml}
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Budget for this Activity:</label>
                                <input type="number" name="activities[${activityId}][budget]" step="0.01"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm p-2 border" 
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
                                        <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Material/ Item</th>
                                        <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Specifications</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Unit</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Qty</th>
                                        
                                        {{-- 💡 NEW COLUMN HEADER --}}
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Rate (KSH)</th> 
                                        
                                        <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                                        <th class="px-2 py-2 w-10"></th>
                                    </tr>
                                </thead>
                                <tbody class="material-rows-container bg-white divide-y divide-gray-200" data-activity-id="${activityId}">
                                    ${getMaterialRowTemplate(activityId, 1)}
                                </tbody>
                            </table>
                        </div>

                        <button type="button" class="add-material-row-btn mt-2 px-3 py-1 text-xs font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600" data-activity-id="${activityId}">
                            + Add Material Item
                        </button>
                    </div>
                </div>
            `;
        }

        // --- HANDLER FUNCTIONS ---

        function addMaterialRow(activityId) {
            const materialContainer = container.querySelector(`.material-rows-container[data-activity-id="${activityId}"]`);
            if (!materialContainer) return;

            // Get the current number of rows in this specific activity container
            const currentRowCount = materialContainer.querySelectorAll('tr').length;
            const newRowId = currentRowCount + 1;
            
            const newRowHtml = getMaterialRowTemplate(activityId, newRowId);
            materialContainer.insertAdjacentHTML('beforeend', newRowHtml);
            attachListeners(); // Re-attach listeners to the new button
        }

        function removeMaterialRow(button) {
            button.closest('tr').remove();
            // Re-index row numbers visually (optional but good practice)
        }
        
        function removeActivitySection(button) {
            button.closest('.activity-section').remove();
        }

        function toggleSection(header) {
            const content = header.nextElementSibling;
            const chevron = header.querySelector('.chevron');
            
            if (content.style.display === 'block' || content.style.display === '') {
                content.style.display = 'none';
                chevron.classList.add('rotate-180');
            } else {
                content.style.display = 'block';
                chevron.classList.remove('rotate-180');
            }
        }
        
        function updateActivityDisplayName(selectElement) {
            const activitySection = selectElement.closest('.activity-section');
            const displayNameSpan = activitySection.querySelector('.activity-name-display');
            const selectedText = selectElement.options[selectElement.selectedIndex].text;
            
            if (displayNameSpan) {
                 displayNameSpan.textContent = selectedText || `Activity #${activitySection.dataset.activityId}`;
            }
        }


        function addActivitySection() {
            activityIndex++;
            const newSectionHtml = getActivitySectionTemplate(activityIndex);
            container.insertAdjacentHTML('beforeend', newSectionHtml);
            attachListeners(); // Re-attach all listeners
        }

        function attachListeners() {
            // Remove previous listeners to prevent duplicates
            // A more efficient way is to use event delegation on the container.
            
            // 1. Add Material Row Button
            document.querySelectorAll('.add-material-row-btn').forEach(btn => {
                btn.onclick = (e) => addMaterialRow(e.currentTarget.dataset.activityId);
            });
            
            // 2. Remove Material Row Button
            document.querySelectorAll('.remove-row-btn').forEach(btn => {
                btn.onclick = (e) => removeMaterialRow(e.currentTarget);
            });
            
            // 3. Remove Activity Button
            document.querySelectorAll('.remove-activity-btn').forEach(btn => {
                btn.onclick = (e) => removeActivitySection(e.currentTarget);
            });
            
            // 4. Toggle Section Header
            document.querySelectorAll('.toggle-section').forEach(header => {
                // Prevent listener duplication on subsequent additions
                if (!header.dataset.listenerAttached) {
                     header.onclick = (e) => {
                        // Ensure click target is not a button inside the header
                        if (!e.target.closest('button')) {
                            toggleSection(e.currentTarget);
                        }
                    };
                    header.dataset.listenerAttached = true;
                }
            });
            
            // 5. Activity Selector Change
            document.querySelectorAll('.activity-selector').forEach(select => {
                 if (!select.dataset.listenerAttached) {
                    select.onchange = (e) => updateActivityDisplayName(e.currentTarget);
                    select.dataset.listenerAttached = true;
                }
            });
        }
        
        // --- INITIALIZATION ---
        
        // Add one initial section when the page loads
        addActivitySection(); 
        
        // Listener for the main Add Activity button
        addActivityBtn.onclick = addActivitySection;
        
    });
</script>
@endpush
