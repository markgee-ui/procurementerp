@extends('layouts.app') 

@section('content')
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
            <button type="submit" class="px-6 py-3 text-lg font-bold text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
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
        const existingBoqData = @json($boq->load('activities.materials'));
        
        const activityOptions = [
             { value: 'foundation', text: 'Foundation' },
             { value: 'masonry', text: 'Walling/Masonry' },
             { value: 'roofing', text: 'Roofing' },
             { value: 'finishes', text: 'Finishes' },
             { value: 'services', text: 'Services (Plumbing/Electrical)' },
        ];
        
        const container = document.getElementById('activity-sections-container');
        const addActivityBtn = document.getElementById('add-activity-btn');
        let activityIndex = 0; // Will be initialized to the highest index after loading
        
        // --- TEMPLATE FUNCTIONS ---

        function getMaterialRowTemplate(activityKey, rowKey, material = {}) {
            // Use existing values or defaults
            const item = material.item || '';
            const specs = material.specs || '';
            const unit = material.unit || '';
            const qty = material.qty || 1;
            const rate = material.rate || 0.00;
            const remarks = material.remarks || '';

            return `
                <tr data-row-id="${activityKey}-${rowKey}">
                    <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">${rowKey}</td>
                    <td class="px-6 py-2 whitespace-nowrap"><input type="text" name="activities[${activityKey}][materials][${rowKey}][item]" value="${item}" class="w-full text-sm border-gray-300 rounded-md p-1" required></td>
                    <td class="px-6 py-2 whitespace-nowrap"><input type="text" name="activities[${activityKey}][materials][${rowKey}][specs]" value="${specs}" class="w-full text-sm border-gray-300 rounded-md p-1"></td>
                    <td class="px-3 py-2 whitespace-nowrap"><input type="text" name="activities[${activityKey}][materials][${rowKey}][unit]" value="${unit}" class="w-full text-sm border-gray-300 rounded-md p-1"></td>
                    <td class="px-3 py-2 whitespace-nowrap"><input type="number" name="activities[${activityKey}][materials][${rowKey}][qty]" value="${qty}" class="w-full text-sm border-gray-300 rounded-md p-1" required min="0.01" step="0.01"></td>
                    <td class="px-3 py-2 whitespace-nowrap"><input type="number" name="activities[${activityKey}][materials][${rowKey}][rate]" value="${rate}" class="w-full text-sm border-gray-300 rounded-md p-1" step="0.01" min="0"></td>
                    <td class="px-6 py-2 whitespace-nowrap"><input type="text" name="activities[${activityKey}][materials][${rowKey}][remarks]" value="${remarks}" class="w-full text-sm border-gray-300 rounded-md p-1"></td>
                    <td class="px-2 py-2 whitespace-nowrap">
                        <button type="button" class="text-red-600 hover:text-red-900 remove-row-btn" data-activity-id="${activityKey}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" /></svg>
                        </button>
                    </td>
                </tr>
            `;
        }

        function getActivitySectionTemplate(activityKey, activity = {}) {
            const name = activity.name || '';
            const budget = activity.budget || 0.00;
            
            let optionsHtml = activityOptions.map(opt => 
                `<option value="${opt.value}" ${opt.value === name ? 'selected' : ''}>${opt.text}</option>`
            ).join('');

            return `
                <div class="activity-section bg-white p-4 rounded-lg border border-gray-200" data-activity-id="${activityKey}">
                    <div class="flex justify-between items-center cursor-pointer bg-gray-100 p-3 rounded-md mb-3 hover:bg-gray-200 toggle-section">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Activity: <span class="activity-name-display">${name || 'New Activity'}</span>
                        </h3>
                        <div class="flex items-center space-x-3">
                            <button type="button" class="text-red-600 hover:text-red-900 remove-activity-btn" title="Remove Activity">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" /></svg>
                            </button>
                        </div>
                    </div>
                    <div class="activity-content space-y-4">
                        <div class="mb-4 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Select Activity Group:</label>
                                <select name="activities[${activityKey}][name]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm p-2 border activity-selector">
                                    <option value="">-- Select Activity Group --</option>
                                    ${optionsHtml}
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Budget for this Activity:</label>
                                <input type="number" name="activities[${activityKey}][budget]" step="0.01" value="${budget}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm p-2 border">
                            </div>
                        </div>
                        <h4 class="text-md font-semibold text-gray-700 mt-4 border-b pb-1">Materials Breakdown:</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                        <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                        <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Specs</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                                        <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="material-rows-container bg-white divide-y divide-gray-200" data-activity-id="${activityKey}">
                                    ${activity.materials && activity.materials.length > 0 ? activity.materials.map((m, mIdx) => getMaterialRowTemplate(activityKey, mIdx + 1, m)).join('') : getMaterialRowTemplate(activityKey, 1)}
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="add-material-row-btn mt-2 px-3 py-1 text-xs font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600" data-activity-id="${activityKey}">
                            + Add Material Item
                        </button>
                    </div>
                </div>
            `;
        }

        // --- HANDLER FUNCTIONS ---
        
        function updateActivityDisplayName(event) {
            const selectElement = event.target;
            const activitySection = selectElement.closest('.activity-section');
            const displaySpan = activitySection.querySelector('.activity-name-display');
            displaySpan.textContent = selectElement.options[selectElement.selectedIndex].text;
        }

        function removeActivitySection(event) {
            if (!confirm('Are you sure you want to remove this entire activity section?')) return;
            event.target.closest('.activity-section').remove();
        }

        function removeMaterialRow(event) {
            event.target.closest('tr').remove();
        }
        
        function toggleSection(event) {
             const header = event.target.closest('.toggle-section');
             if (!header) return;
             const content = header.nextElementSibling;
             if (content.style.display === 'none' || content.style.display === '') {
                 content.style.display = 'block';
             } else {
                 content.style.display = 'none';
             }
        }

        function addActivitySection() {
            activityIndex++;
            const newActivityHtml = getActivitySectionTemplate(activityIndex);
            container.insertAdjacentHTML('beforeend', newActivityHtml);
            attachListeners();
        }

        function addMaterialRow(event) {
            const button = event.target;
            const activityKey = button.getAttribute('data-activity-id');
            const tbody = button.closest('.activity-section').querySelector('.material-rows-container');
            const nextRowKey = tbody.children.length + 1;
            
            const newRowHtml = getMaterialRowTemplate(activityKey, nextRowKey);
            tbody.insertAdjacentHTML('beforeend', newRowHtml);
            attachListeners(); 
        }

        function attachListeners() {
            // Attach/Re-attach listeners for dynamically added elements
            container.querySelectorAll('.add-material-row-btn').forEach(btn => {
                btn.onclick = addMaterialRow;
            });
            container.querySelectorAll('.remove-row-btn').forEach(btn => {
                btn.onclick = removeMaterialRow;
            });
            container.querySelectorAll('.remove-activity-btn').forEach(btn => {
                btn.onclick = removeActivitySection;
            });
            container.querySelectorAll('.activity-selector').forEach(select => {
                select.onchange = updateActivityDisplayName;
            });
            container.querySelectorAll('.toggle-section').forEach(header => {
                 // Clone and replace to prevent multiple event listeners on the same element
                const clone = header.cloneNode(true);
                header.parentNode.replaceChild(clone, header);
                clone.onclick = toggleSection;
            });
        }
        
        // --- INITIALIZATION FUNCTION ---
        
        function loadExistingBoq() {
            let maxId = 0;
            
            // 1. Find the highest ID among existing activities
            existingBoqData.activities.forEach(activity => {
                if (activity.id && activity.id > maxId) {
                    maxId = activity.id;
                }
            });
            // 2. Set the index for the next *new* activity
            activityIndex = maxId > 0 ? maxId + 1 : existingBoqData.activities.length + 1;
            
            // 3. Render existing activities
            existingBoqData.activities.forEach((activity, activityKey) => {
                // Use the activity's actual ID for the name grouping if available, 
                // otherwise use a safe large index. This is crucial for distinguishing 
                // existing vs. new records if you plan on complex update logic later.
                const key = activity.id || (existingBoqData.activities.length + activityKey + 1);

                // We leverage the getActivitySectionTemplate to render both activity and materials
                const activitySectionHtml = getActivitySectionTemplate(key, activity);
                
                container.insertAdjacentHTML('beforeend', activitySectionHtml);
            });

            // 4. Attach listeners to all loaded elements
            attachListeners();
            
            // Hide the activity content for all sections initially for a cleaner look
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