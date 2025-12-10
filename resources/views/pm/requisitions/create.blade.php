@extends('layouts.app') 

@section('content')
<div class="p-6 bg-white rounded-lg shadow-xl max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">
        New Purchase Requisition
    </h1>
    <h2 class="text-xl text-indigo-600 mb-6 border-b pb-2">
        Project: {{ $project->project_name }}
    </h2>

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <form action="{{ route('pm.requisitions.store') }}" method="POST" id="requisition-form">
        @csrf
        <input type="hidden" name="boq_id" value="{{ $project->id }}">

        <div class="space-y-6">

            {{-- 1. Activity Selector --}}
            <div class="bg-gray-50 p-4 rounded-md border">
                <label for="activity_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Select Activity/Work Section:
                </label>
                <select id="activity_id" name="activity_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border activity-selector">
                    <option value="">-- Select Project Activity --</option>
                    @foreach ($activities as $activity)
                        <option value="{{ $activity->id }}" data-materials="{{ $activity->materials->toJson() }}">
                            {{ strtoupper($activity->name) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 2. Material Item Selector (Dynamically Populated) --}}
            <div class="p-4 rounded-md border" id="material-selection-container" style="display: none;">
                <label for="material_item_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Select Material Item from BoQ:
                </label>

                {{-- FIX: ID and correct name --}}
                <select id="material_item_id" name="boq_material_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border material-selector">
                    <option value="">-- Select Material Item --</option>
                </select>
                
                {{-- Feedback --}}
                <div class="mt-3 text-sm font-semibold p-2 border border-dashed rounded-md bg-white">
                    <p>BoQ Remaining: <span id="boq-remaining" class="text-blue-600">--</span></p>
                    <p>Current Site Stock: <span id="site-stock" class="text-orange-600">--</span></p>
                    <p class="mt-1">Net Qty to Order: <span id="net-order-qty" class="text-green-600">--</span></p>
                </div>
            </div>

            {{-- 3. Quantity and Details --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="qty_requested" class="block text-sm font-medium text-gray-700">Quantity to Request:</label>
                    <input type="number" id="qty_requested" name="qty_requested" step="0.01" required min="1"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border input-qty" 
                        placeholder="e.g., 50.00">
                    <p class="text-xs text-red-600 mt-1 hidden" id="qty-error">
                        Requested quantity exceeds BoQ remaining! Please adjust or provide justification below.
                    </p>
                </div>
                <div>
                    <label for="required_by" class="block text-sm font-medium text-gray-700">Required By Date:</label>

                    {{-- FIX: required_by_date --}}
                    <input type="date" id="required_by" name="required_by_date" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border">
                </div>
            </div>
            
            {{-- 4. Justification --}}
            <div>
                <label for="justification" class="block text-sm font-medium text-gray-700">
                    Justification/Remarks:
                </label>
                <textarea id="justification" name="justification" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border" 
                    placeholder="State the reason for this request"></textarea>
            </div>

            {{-- Submit Button --}}
            <div class="mt-6 pt-4 border-t flex justify-end">
                <button type="submit" id="submit-pr-btn" 
                        class="px-6 py-3 text-lg font-bold text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Submit Purchase Requisition
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const activitySelector = document.getElementById('activity_id');
        const materialSelector = document.getElementById('material_item_id');
        const qtyInput = document.getElementById('qty_requested');
        const materialContainer = document.getElementById('material-selection-container');
        const boqRemainingDisplay = document.getElementById('boq-remaining');
        const siteStockDisplay = document.getElementById('site-stock');
        const netOrderQtyDisplay = document.getElementById('net-order-qty');
        const qtyError = document.getElementById('qty-error');
        const submitBtn = document.getElementById('submit-pr-btn');
        
        let selectedMaterialData = null;

        // Mock data
        const mockBoQRemaining = {'1':500,'2':1500,'3':5};
        const mockSiteStock = {'1':50,'2':0,'3':1};

        activitySelector.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            materialSelector.innerHTML = '<option value="">-- Select Material Item --</option>';
            materialContainer.style.display = 'none';
            selectedMaterialData = null;
            updateFeedback(0, 0, 0);

            if (selectedOption.value) {
                const materials = JSON.parse(selectedOption.dataset.materials);
                materials.forEach(material => {
                    const option = document.createElement('option');
                    option.value = material.id;
                    option.textContent = `${material.item} (${material.specs || 'N/A'})`;
                    option.dataset.unit = material.unit || ''; 
                    materialSelector.appendChild(option);
                });
                materialContainer.style.display = 'block';
            }
        });

        materialSelector.addEventListener('change', function() {
            const materialId = this.value;
            const requestedQty = parseFloat(qtyInput.value) || 0;
            
            selectedMaterialData = {
                boqRemaining: mockBoQRemaining[materialId] || 0,
                siteStock: mockSiteStock[materialId] || 0,
            };

            updateFeedback(selectedMaterialData.boqRemaining, selectedMaterialData.siteStock, requestedQty);
        });

        qtyInput.addEventListener('input', function() {
            const requestedQty = parseFloat(this.value) || 0;
            if (selectedMaterialData) {
                updateFeedback(selectedMaterialData.boqRemaining, selectedMaterialData.siteStock, requestedQty);
            } else {
                updateFeedback(0, 0, requestedQty);
            }
        });

        function updateFeedback(boqRemaining, siteStock, requestedQty) {
            boqRemainingDisplay.textContent = boqRemaining.toFixed(2);
            siteStockDisplay.textContent = siteStock.toFixed(2);
            netOrderQtyDisplay.textContent = Math.max(0, requestedQty - siteStock).toFixed(2);
            
            let isOverBoQ = boqRemaining > 0 && requestedQty > boqRemaining;
            qtyError.classList.toggle('hidden', !isOverBoQ);

            submitBtn.disabled = !materialSelector.value || requestedQty <= 0;
        }
        
        updateFeedback(0, 0, 0); 
    });
</script>
@endpush
