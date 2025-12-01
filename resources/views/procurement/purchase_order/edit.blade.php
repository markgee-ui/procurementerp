@extends('layouts.app')

@section('title', 'Edit Purchase Order #' . ($purchaseOrder->order_number ?? $purchaseOrder->id))

@section('content')

<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Purchase Order: {{ $purchaseOrder->order_number ?? 'TGL' . $purchaseOrder->id }}</h1>

    {{-- The form action must use the PUT method for updating --}}
    <form action="{{ route('procurement.order.update', $purchaseOrder->id) }}" method="POST" class="bg-white shadow-xl rounded-xl p-6">
        @csrf
        @method('PUT')
        
        {{-- Hidden field to pass supplier ID --}}
        <input type="hidden" name="supplier_id" value="{{ $purchaseOrder->supplier_id }}">

        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Order Details</h2>

        <div class="grid grid-cols-2 gap-6 mb-6">
            {{-- Supplier Name (Read-only) --}}
            <div class="col-span-1">
                <label class="block text-sm font-medium text-gray-700">Supplier</label>
                <p class="mt-1 block w-full font-bold text-gray-800">{{ $purchaseOrder->supplier->name ?? 'N/A' }}</p>
            </div>
            
            {{-- Project Name Input --}}
            <div class="col-span-1">
                <label for="project_name" class="block text-sm font-medium text-gray-700">
                    Project Name (Optional)
                </label>
                <input type="text" name="project_name" id="project_name"
                       value="{{ old('project_name', $purchaseOrder->project_name) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                       placeholder="e.g., Office Renovation">
                @error('project_name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <h3 class="text-xl font-semibold text-gray-700 mb-4 border-t pt-4">Order Items</h3>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="order-items-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-4/12">Product</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-2/12">Unit Price</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">Quantity</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">Disc (%)</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-2/12">Total</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    
                    {{-- Loop through EXISTING items to pre-populate --}}
                    @forelse ($purchaseOrder->items as $index => $item)
                        <tr class="item-row">
                            <td class="px-3 py-4">
                                {{-- The index here is just for array keying in the POST request --}}
                                <select name="items[{{ $index }}][product_id]" class="product-selector w-full p-2 border rounded-md text-sm" required>
                                    <option value="">Select a Product</option>
                                    {{-- Loop through all products --}}
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" 
                                                data-price="{{ $product->unit_price }}" 
                                                {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                            {{ $product->item }} ({{ $product->unit ?? 'Unit' }})
                                        </option>
                                    @endforeach
                                </select>
                                {{-- Use the SAVED unit price for submission (client price can be volatile) --}}
                                <input type="hidden" name="items[{{ $index }}][unit_price]" class="unit-price-hidden" value="{{ old("items.$index.unit_price", $item->unit_price) }}">
                            </td>
                            <td class="px-3 py-4 text-sm text-gray-700">
                                <span class="unit-price-display">{{ number_format($item->unit_price, 2) }}</span>
                            </td>
                            <td class="px-3 py-4">
                                <input type="number" name="items[{{ $index }}][quantity]" 
                                        value="{{ old("items.$index.quantity", $item->quantity) }}" min="1" 
                                        class="quantity-input w-full p-2 border rounded-md text-sm text-center" required>
                            </td>
                            <td class="px-3 py-4">
                                <input type="number" name="items[{{ $index }}][discount]" 
                                        value="{{ old("items.$index.discount", $item->discount) }}" min="0" max="100" 
                                        class="discount-input w-full p-2 border rounded-md text-sm text-center">
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                <span class="line-total">{{ number_format($item->line_total, 2) }}</span>
                            </td>
                            <td class="px-3 py-4">
                                <button type="button" class="remove-item-button text-red-600 hover:text-red-900 text-sm">Remove</button>
                            </td>
                        </tr>
                    @empty
                        {{-- If the PO was saved with no items (unlikely but safe), show placeholder --}}
                        <tr id="empty-row-placeholder">
                            <td colspan="6" class="p-4 text-center text-gray-500">
                                No items found. Click "Add Item" to add products.
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        <div class="flex justify-between items-center mt-4">
            <button type="button" id="add-item-button"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                + Add Item
            </button>
            
            <div class="mt-4 pt-4 border-t border-gray-200 text-right">
                <div class="text-xl font-bold text-gray-800">
                    GRAND TOTAL: <span id="grand-total">{{ number_format($purchaseOrder->total_amount, 2) }}</span>
                </div>
            </div>
        </div>
        
        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end">
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Update Purchase Order
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
    // Initialize item index using the count of existing items to prevent key conflicts
    let itemIndex = {{ $purchaseOrder->items->count() }}; 
    const productsData = @json($products);

    // --- Core Functions ---

    function calculateLineTotal(row) {
        const unitPriceElement = row.querySelector('.unit-price-hidden');
        const quantityInput = row.querySelector('.quantity-input');
        const discountInput = row.querySelector('.discount-input');
        const lineTotalDisplay = row.querySelector('.line-total');

        const unitPrice = parseFloat(unitPriceElement.value) || 0;
        const quantity = parseFloat(quantityInput.value) || 0;
        const discountPercent = parseFloat(discountInput.value) || 0;

        const subtotal = unitPrice * quantity;
        const discountAmount = subtotal * (discountPercent / 100);
        const lineTotal = subtotal - discountAmount;

        lineTotalDisplay.textContent = lineTotal.toFixed(2);
        updateGrandTotal();
    }

    function updateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.line-total').forEach(span => {
            grandTotal += parseFloat(span.textContent) || 0;
        });
        document.getElementById('grand-total').textContent = grandTotal.toFixed(2);
    }
    
    function attachEventListeners(row) {
        const productSelector = row.querySelector('.product-selector');
        const quantityInput = row.querySelector('.quantity-input');
        const discountInput = row.querySelector('.discount-input');
        const removeButton = row.querySelector('.remove-item-button');

        [productSelector, quantityInput, discountInput].forEach(input => {
            input.addEventListener('change', () => calculateLineTotal(row));
            input.addEventListener('input', () => calculateLineTotal(row));
        });

        removeButton.addEventListener('click', function() {
            row.remove();
            updateGrandTotal();
        });
        
        // Price update logic on product selection
        productSelector.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const price = parseFloat(selectedOption.getAttribute('data-price') || 0);
            
            row.querySelector('.unit-price-display').textContent = price.toFixed(2);
            row.querySelector('.unit-price-hidden').value = price;
            calculateLineTotal(row);
        });
        
        // Initial calculation for pre-loaded rows
        calculateLineTotal(row);
    }

    // --- Dynamic Row Addition ---

    document.getElementById('add-item-button').addEventListener('click', function() {
        const tableBody = document.querySelector('#order-items-table tbody');
        const placeholder = document.getElementById('empty-row-placeholder');
        if (placeholder) {
            placeholder.remove();
        }

        const newRow = document.createElement('tr');
        newRow.classList.add('item-row');
        newRow.innerHTML = `
            <td class="px-3 py-4">
                <select name="items[${itemIndex}][product_id]" class="product-selector w-full p-2 border rounded-md text-sm" required>
                    <option value="">Select a Product</option>
                    ${productsData.map(product => `<option value="${product.id}" data-price="${product.unit_price}">${product.item} (${product.unit ?? 'Unit'})</option>`).join('')}
                </select>
                <input type="hidden" name="items[${itemIndex}][unit_price]" class="unit-price-hidden" value="0">
            </td>
            <td class="px-3 py-4 text-sm text-gray-700">
                <span class="unit-price-display">0.00</span>
            </td>
            <td class="px-3 py-4">
                <input type="number" name="items[${itemIndex}][quantity]" value="1" min="1" 
                        class="quantity-input w-full p-2 border rounded-md text-sm text-center" required>
            </td>
            <td class="px-3 py-4">
                <input type="number" name="items[${itemIndex}][discount]" value="0" min="0" max="100" 
                        class="discount-input w-full p-2 border rounded-md text-sm text-center">
            </td>
            <td class="px-3 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                <span class="line-total">0.00</span>
            </td>
            <td class="px-3 py-4">
                <button type="button" class="remove-item-button text-red-600 hover:text-red-900 text-sm">Remove</button>
            </td>
        `;

        tableBody.appendChild(newRow);
        attachEventListeners(newRow);
        itemIndex++;
    });

    // --- Initialization: Attach listeners to all existing rows on load ---
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.item-row').forEach(row => {
            attachEventListeners(row);
        });
        updateGrandTotal(); // Recalculate totals on load just to be safe
    });
</script>
@endpush