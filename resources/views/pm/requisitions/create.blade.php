@extends('layouts.app')

@section('content')
<div class="p-6 bg-white rounded-lg shadow-xl max-w-6xl mx-auto">
    
    {{-- HEADER BLOCK WITH BACK BUTTON --}}
    <div class="flex justify-between items-center mb-6 border-b pb-2">
        <h1 class="text-3xl font-bold text-gray-800">
            New Multi-Item Purchase Requisition
        </h1>
        <a href="{{ route('pm.requisitions.index') }}" 
           class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition duration-150">
            &larr; Back to PR List
        </a>
    </div>

    <h2 class="text-xl text-indigo-600 mb-6">
        Project: {{ $boq->project_name }}
    </h2>

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <form action="{{ route('pm.requisitions.store') }}" method="POST" id="requisition-form">
        @csrf
        {{-- Using project_id as per the controller update --}}
        <input type="hidden" name="boq_id" value="{{ $boq->id }}">

        {{-- 1. LINE ITEM INPUT AREA (Always visible) --}}
        <div class="bg-gray-100 p-4 rounded-md mb-6 border-2 border-dashed border-gray-300">
            <h3 class="text-xl font-semibold mb-3 text-gray-700">Add Items</h3>
            
            <div class="grid grid-cols-12 gap-3">
                {{-- Activity Selector for adding a new item --}}
                <div class="col-span-4">
                    <label for="new_activity_id" class="block text-xs font-medium text-gray-700">Activity</label>
                    <select id="new_activity_id" class="w-full rounded-md border-gray-300 p-2 border text-sm">
                        <option value="">-- Select Activity --</option>
                        @foreach ($activities as $activity)
                            <option value="{{ $activity->id }}" data-materials="{{ $activity->materials->toJson() }}">
                                {{ strtoupper($activity->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Material Selector for adding a new item --}}
                <div class="col-span-5">
                    <label for="new_material_id" class="block text-xs font-medium text-gray-700">Material</label>
                    <select id="new_material_id" class="w-full rounded-md border-gray-300 p-2 border text-sm" disabled>
                        <option value="">-- Select Material Item --</option>
                    </select>
                </div>
                
                {{-- Quantity Input for adding a new item --}}
                <div class="col-span-2">
                    <label for="new_qty" class="block text-xs font-medium text-gray-700">Qty</label>
                    <input type="number" id="new_qty" step="0.01" min="0.01" class="w-full rounded-md border-gray-300 p-2 border text-sm" disabled>
                </div>

                {{-- Add Button --}}
                <div class="col-span-1 flex items-end">
                    <button type="button" id="add-item-btn" disabled
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-3 rounded text-sm disabled:opacity-50">
                        Add
                    </button>
                </div>
            </div>
            <p id="item-feedback" class="text-xs mt-2 text-red-600 hidden">Please select material and enter a valid quantity.</p>
        </div>


        {{-- 2. DYNAMIC LINE ITEM TABLE --}}
        <h3 class="text-xl font-semibold mb-3 text-gray-700">Requisition Items</h3>
        <div class="overflow-x-auto mb-6 border rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-3/12">Activity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-4/12">Material</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">Qty</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-2/12">Actions</th>
                    </tr>
                </thead>
                <tbody id="line-items-tbody" class="bg-white divide-y divide-gray-200">
                    {{-- Dynamic rows will be inserted here --}}
                    <tr><td colspan="6" class="p-4 text-center text-gray-500" id="no-items-row">No items added yet.</td></tr>
                </tbody>
            </table>
        </div>

        {{-- 3. GLOBAL DETAILS (Required Date, Justification) --}}
        <div class="grid grid-cols-2 gap-4 mt-6">
            <div>
                <label for="required_by" class="block text-sm font-medium text-gray-700">Required By Date:</label>
                <input type="date" id="required_by" name="required_by_date" value="{{ old('required_by_date') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border">
            </div>
            <div>
                <label for="justification" class="block text-sm font-medium text-gray-700">Overall Justification/Remarks:</label>
                <textarea id="justification" name="justification" rows="3" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border"
                    placeholder="State the main reason for this combined request.">{{ old('justification') }}</textarea>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="mt-8 pt-4 border-t flex justify-end">
            <button type="submit" id="submit-pr-btn" disabled
                    class="px-6 py-3 text-lg font-bold text-white bg-green-600 rounded-md hover:bg-green-700 disabled:opacity-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Submit Combined Purchase Requisition
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const activitySelector = document.getElementById('new_activity_id');
        const materialSelector = document.getElementById('new_material_id');
        const qtyInput = document.getElementById('new_qty');
        const addItemBtn = document.getElementById('add-item-btn');
        const lineItemsTbody = document.getElementById('line-items-tbody');
        const noItemsRow = document.getElementById('no-items-row');
        const submitBtn = document.getElementById('submit-pr-btn');
        const itemFeedback = document.getElementById('item-feedback');

        let lineItemCounter = 0; 
        
        // Global map to store material details (name, unit, activity name)
        const materialsDataMap = {};
        
        // 1. Initial Data Setup: Populate the materialsDataMap on initial load
        activitySelector.querySelectorAll('option:not([value=""])').forEach(option => {
            const activityId = option.value;
            const activityName = option.textContent.trim();
            try {
                const materials = JSON.parse(option.dataset.materials);
                materials.forEach(material => {
                    materialsDataMap[material.id] = {
                        item: material.item,
                        unit: material.unit || 'N/A',
                        activityName: activityName,
                        activityId: activityId, // Store the ID for the hidden input!
                        // In a real app, you'd fetch: boqRemaining: material.remaining_qty, siteStock: material.site_stock
                    };
                });
            } catch (e) {
                console.error("Error parsing materials JSON for activity:", activityName, e);
            }
        });

        // 2. ACTIVITY SELECTION: Populates the Material Selector
        activitySelector.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            materialSelector.innerHTML = '<option value="">-- Select Material Item --</option>';
            materialSelector.disabled = true;
            addItemBtn.disabled = true;
            qtyInput.disabled = true;
            qtyInput.value = '';

            if (selectedOption.value) {
                try {
                    const materials = JSON.parse(selectedOption.dataset.materials);
                    materials.forEach(material => {
                        const option = document.createElement('option');
                        option.value = material.id;
                        option.textContent = `${material.item} (${material.unit || 'N/A'}) - BoQ Qty: ${material.qty}`;
                        materialSelector.appendChild(option);
                    });
                    materialSelector.disabled = false;
                    qtyInput.disabled = false;
                } catch (e) {
                    console.error("Failed to parse materials:", e);
                }
            }
        });

        // 3. MATERIAL/QTY CHANGE: Enables Add button
        [materialSelector, qtyInput].forEach(element => {
            element.addEventListener('input', function() {
                const materialSelected = materialSelector.value !== '';
                const qtyValid = parseFloat(qtyInput.value) > 0;
                addItemBtn.disabled = !(materialSelected && qtyValid);
                itemFeedback.classList.add('hidden'); // Hide feedback on change
            });
        });
        
        // 4. ADD ITEM BUTTON: Adds the selected item to the table
        addItemBtn.addEventListener('click', function() {
            const materialId = materialSelector.value;
            const qty = parseFloat(qtyInput.value);
            
            if (!materialId || qty <= 0) {
                itemFeedback.textContent = "Please select material and enter a valid quantity.";
                itemFeedback.classList.remove('hidden');
                return;
            }
            
            // Check for duplicate material ID
            const existingRow = lineItemsTbody.querySelector(`input[name$="[boq_material_id]"][value="${materialId}"]`);
            if (existingRow) {
                itemFeedback.textContent = "This material is already in the list. Please remove or update the existing row.";
                itemFeedback.classList.remove('hidden');
                return;
            }
            
            itemFeedback.classList.add('hidden');
            const materialDetails = materialsDataMap[materialId];
            
            if (noItemsRow) { noItemsRow.remove(); }
            
            lineItemCounter++;
            
            const newRow = document.createElement('tr');
            newRow.classList.add('line-item-row', 'hover:bg-gray-50');
            newRow.innerHTML = `
                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">${lineItemsTbody.querySelectorAll('.line-item-row').length + 1}</td>
                <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900">${materialDetails.activityName}</td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">${materialDetails.item}</td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">${materialDetails.unit}</td>
                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900">
                    <input type="number" step="0.01" min="0.01" name="items[${lineItemCounter}][qty_requested]" value="${qty.toFixed(2)}" class="w-20 text-center border rounded p-1 text-xs" required>
                    <input type="hidden" name="items[${lineItemCounter}][boq_material_id]" value="${materialId}">
                    <input type="hidden" name="items[${lineItemCounter}][boq_activity_id]" value="${materialDetails.activityId}">
                </td>
                <td class="px-6 py-2 whitespace-nowrap text-sm font-medium">
                    <button type="button" class="text-red-600 hover:text-red-900 remove-item-btn text-xs font-semibold">Remove</button>
                </td>
            `;
            
            lineItemsTbody.appendChild(newRow);
            
            // Reset input fields
            activitySelector.value = '';
            materialSelector.innerHTML = '<option value="">-- Select Material Item --</option>';
            materialSelector.disabled = true;
            qtyInput.value = '';
            qtyInput.disabled = true;
            addItemBtn.disabled = true;
            
            updateSubmitButton();
        });
        
        // 5. REMOVE ITEM BUTTON: Removes the item from the table
        lineItemsTbody.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-item-btn')) {
                e.target.closest('tr').remove();
                
                // Re-index row numbers (for display only)
                lineItemsTbody.querySelectorAll('.line-item-row').forEach((row, index) => {
                    row.querySelector('td:first-child').textContent = index + 1;
                });
                updateSubmitButton();
            }
        });
        
        // 6. UPDATE SUBMIT BUTTON STATE
        function updateSubmitButton() {
            const rowCount = lineItemsTbody.querySelectorAll('.line-item-row').length;
            submitBtn.disabled = rowCount === 0;
            
            if (rowCount === 0 && !document.getElementById('no-items-row')) {
                const emptyRow = document.createElement('tr');
                emptyRow.innerHTML = '<td colspan="6" class="p-4 text-center text-gray-500" id="no-items-row">No items added yet.</td>';
                lineItemsTbody.appendChild(emptyRow);
            }
        }
    });
</script>
@endpush