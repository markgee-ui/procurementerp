@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Procurement Action for PR-{{ $requisition->id }}</h2>
    
    <div class="grid grid-cols-3 gap-6 mb-6">
        {{-- Requisition Details Card (Col 1 & 2) --}}
        <div class="col-span-2 bg-white shadow-lg rounded-lg p-6">
            <h3 class="text-xl font-bold mb-4 border-b pb-2">Requisition Details</h3>
            
            {{-- FIX: Null safety for project name, initiator name, and date format --}}
            <p><strong>Project:</strong> {{ $requisition->project->project_name ?? 'N/A' }}</p>
            <p><strong>Site PM:</strong> {{ $requisition->initiator?->name ?? 'User Missing' }}</p>
            <p><strong>Required On:</strong> {{ $requisition->required_by_date?->format('M d, Y') ?? 'Date Not Specified' }}</p>

            <h4 class="text-lg font-semibold mt-6 mb-3">Items Requested</h4>
            <table class="min-w-full divide-y divide-gray-200" id="requisition-items-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-2 px-2 text-left text-xs font-medium text-gray-500 uppercase">
                            <input type="checkbox" id="select-all-items" class="rounded text-indigo-600">
                        </th>
                        <th class="py-2 text-left text-xs font-medium text-gray-500 uppercase">Item / Material</th>
                        <th class="py-2 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                        <th class="py-2 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($requisition->items as $item)
                    <tr>
                        <td class="py-2 px-2 text-sm text-gray-900">
                            {{-- CHECKBOX FOR SELECTION (used by JS to filter suppliers) --}}
                            <input type="checkbox" name="item_ids[]" value="{{ $item->id }}" data-material-id="{{ $item->boqMaterial->id ?? 0 }}" class="item-checkbox rounded border-gray-300 text-indigo-600 shadow-sm">
                        </td>
                        <td class="py-2 text-sm text-gray-900">
                            {{ $item->item_name }} 
                            <span class="text-xs text-gray-400">({{ $item->boqMaterial->name ?? 'N/A' }})</span>
                        </td>
                        <td class="py-2 text-sm text-gray-900">{{ $item->qty_requested }}</td>
                        <td class="py-2 text-sm text-gray-500">{{ $item->unit }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <h4 class="text-lg font-semibold mt-6 mb-3">Approval History</h4>
            {{-- FIX: Included the previously missing component view --}}
            @include('components.approval-history', ['approvals' => $requisition->approvals])
        </div>

        {{-- Action Card: Select Supplier (Col 3) --}}
        {{-- 1. Update the Form in the Action Card (Col 3) --}}
<div class="col-span-1 bg-white shadow-lg rounded-lg p-6 h-fit">
    <h3 class="text-xl font-bold mb-4 border-b pb-2 text-indigo-600">Initiate Purchase Order</h3>
    
    <form action="{{ route('procurement.requisition.initiate_po', $requisition) }}" method="POST" id="initiate-po-form">
        @csrf
        {{-- Hidden container for selected item IDs --}}
        <div id="selected-items-inputs"></div>

        <div class="mb-4">
            <label for="preferred_supplier_id" class="block text-sm font-medium text-gray-700">Select Supplier</label>
            <select id="preferred_supplier_id" name="preferred_supplier_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="" data-available="true">-- Select a Supplier --</option>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                @endforeach
            </select>
            <p id="supplier-filter-info" class="text-xs text-red-500 mt-1 hidden">Please select item(s) to filter suppliers based on material availability.</p>
        </div>

        <p class="text-xs text-gray-500 mb-4">Selecting a supplier will create a Purchase Order draft with only the <strong>selected</strong> items.</p>
        
        <button type="submit" id="create-po-button" class="btn-primary w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md" disabled>
            Proceed to Create PO
        </button>
    </form>
</div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const supplierSelect = document.getElementById('preferred_supplier_id');
    const selectAllCheckbox = document.getElementById('select-all-items');
    const createPoButton = document.getElementById('create-po-button');
    const filterInfo = document.getElementById('supplier-filter-info');
    const hiddenInputsContainer = document.getElementById('selected-items-inputs');

    const itemSupplierMap = @json($itemSupplierMap); 
    
    const allSupplierIds = Array.from(supplierSelect.options)
                                .filter(o => o.value)
                                .map(o => parseInt(o.value));

    function getSelectedItemIds() {
        return Array.from(itemCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => parseInt(cb.value));
    }

    // NEW: Function to sync checkboxes to the form submission
    function updateHiddenInputs() {
        hiddenInputsContainer.innerHTML = ''; // Clear previous
        getSelectedItemIds().forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_item_ids[]';
            input.value = id;
            hiddenInputsContainer.appendChild(input);
        });
    }

    function filterSuppliers() {
        const selectedItemIds = getSelectedItemIds();
        
        // Sync selected items to the form
        updateHiddenInputs();

        createPoButton.disabled = selectedItemIds.length === 0 || !supplierSelect.value;

        if (selectedItemIds.length === 0) {
            Array.from(supplierSelect.options).forEach(option => {
                option.style.display = '';
            });
            filterInfo.classList.remove('hidden');
            return;
        }

        filterInfo.classList.add('hidden');
        
        let commonSupplierIds = allSupplierIds;

        selectedItemIds.forEach(itemId => {
            const currentItemSuppliers = itemSupplierMap[itemId] || [];
            commonSupplierIds = commonSupplierIds.filter(supplierId => 
                currentItemSuppliers.includes(supplierId)
            );
        });

        Array.from(supplierSelect.options).forEach(option => {
            const supplierId = parseInt(option.value);
            if (!supplierId) {
                option.style.display = '';
                return;
            }
            option.style.display = commonSupplierIds.includes(supplierId) ? '' : 'none';
        });

        if (supplierSelect.selectedOptions[0]?.style.display === 'none') {
            supplierSelect.value = '';
            createPoButton.disabled = true;
        }
    }
    
    supplierSelect.addEventListener('change', function() {
        createPoButton.disabled = getSelectedItemIds().length === 0 || !supplierSelect.value;
    });

    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', filterSuppliers);
    });

    selectAllCheckbox.addEventListener('change', function() {
        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
        filterSuppliers();
    });

    filterSuppliers(); 
});
</script>
@endpush
@endsection