@extends('layouts.app')

@section('title', 'Create Purchase Order for ' . $supplier->name)

@section('content')

<div class="max-w-4xl mx-auto space-y-8">
    
    {{-- Back Button --}}
    <a href="{{ route('procurement.supplier.index') }}" 
       class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-indigo-600 transition">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Supplier List
    </a>

    <header class="pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-bold text-gray-800">New Purchase Order</h1>
        <p class="text-xl text-indigo-600">Supplier: {{ $supplier->name }}</p>
    </header>

    {{-- Form for PO creation (using Alpine.js for dynamic rows is ideal, but here's the basic HTML structure) --}}
   <form action="{{route('procurement.order.store') }}" method="POST" class="bg-white shadow-xl rounded-xl p-6">
    @csrf
    {{-- Hidden field to pass supplier ID --}}
    <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">

    <h2 class="text-2xl font-semibold text-gray-700 mb-6">Order Details</h2>
    <div class="mb-6">
        <label for="project_name" class="block text-sm font-medium text-gray-700">
            Project Name (Optional)
        </label>
        <input type="text" name="project_name" id="project_name"
               class="mt-1 block w-1/2 border rounded-md sm:text-sm"
>
        @error('project_name')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    {{-- End New Input --}}

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Quantity</th>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Discount (%)</th>
                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                
                @forelse ($products as $product)
                <tr class="hover:bg-gray-50">
                    {{-- Product Description (Item and Unit) --}}
                    <td class="px-3 py-4">
                        <input type="hidden" name="items[{{ $product->id }}][product_id]" value="{{ $product->id }}">
                        <div class="text-sm font-medium text-gray-900">{{ $product->item }}</div>
                        <div class="text-xs text-gray-500">{{ $product->description }}</div>
                        <div class="text-xs font-semibold text-indigo-500">{{ $product->unit ?? 'Unit' }}</div>
                    </td>

                    {{-- Unit Price --}}
                    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{-- Assuming Unit Price is a known value (e.g., $100.00) --}}
                        <span data-price="{{ $product->unit_price }}">{{ number_format($product->unit_price, 2) }}</span>
                        <input type="hidden" name="items[{{ $product->id }}][unit_price]" value="{{ $product->unit_price }}">
                    </td>

                    {{-- Quantity Input --}}
                    <td class="px-3 py-4">
                        <input type="number" name="items[{{ $product->id }}][quantity]" value="0" min="0" 
                                class="quantity-input w-full p-2 border rounded-md text-sm text-center">
                    </td>

                    {{-- Discount Input --}}
                    <td class="px-3 py-4">
                        <input type="number" name="items[{{ $product->id }}][discount]" value="0" min="0" max="100" 
                                class="discount-input w-full p-2 border rounded-md text-sm text-center">
                    </td>
                    
                    {{-- Calculated Total (Requires JavaScript for real-time calculation) --}}
                    <td class="px-3 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                        <span class="line-total">0.00</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-6 text-center text-gray-500">No products are currently associated with this supplier.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-8 pt-6 border-t border-gray-200 flex justify-between items-center">
        <div class="text-xl font-bold text-gray-800">
            GRAND TOTAL: <span id="grand-total">0.00</span>
        </div>
        
        <div class="space-x-3">
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Create Purchase Order
            </button>
        </div>
    </div>
</form>
</div>

{{-- JAVASCRIPT FOR REAL-TIME CALCULATION --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tableBody = document.querySelector('.min-w-full tbody');
        
        function calculateLineTotal(row) {
            const priceElement = row.querySelector('[data-price]');
            const quantityInput = row.querySelector('.quantity-input');
            const discountInput = row.querySelector('.discount-input');
            const totalElement = row.querySelector('.line-total');

            const price = parseFloat(priceElement.dataset.price);
            const quantity = parseInt(quantityInput.value) || 0;
            const discount = parseFloat(discountInput.value) || 0;
            
            if (quantity <= 0) {
                totalElement.textContent = '0.00';
                return 0;
            }

            let subtotal = price * quantity;
            let finalTotal = subtotal * (1 - discount / 100);

            totalElement.textContent = finalTotal.toFixed(2);
            return finalTotal;
        }

        function calculateGrandTotal() {
            let grandTotal = 0;
            tableBody.querySelectorAll('tr').forEach(row => {
                // Safely find the line total span
                const totalSpan = row.querySelector('.line-total');
                if (totalSpan) {
                    grandTotal += parseFloat(totalSpan.textContent) || 0;
                }
            });
            document.getElementById('grand-total').textContent = grandTotal.toFixed(2);
        }
        
        // Attach listeners to all quantity and discount fields
        tableBody.querySelectorAll('tr').forEach(row => {
            const quantityInput = row.querySelector('.quantity-input');
            const discountInput = row.querySelector('.discount-input');

            if (quantityInput) {
                quantityInput.addEventListener('input', () => {
                    calculateLineTotal(row);
                    calculateGrandTotal();
                });
            }
            if (discountInput) {
                discountInput.addEventListener('input', () => {
                    calculateLineTotal(row);
                    calculateGrandTotal();
                });
            }
            // Initial calculation
            calculateLineTotal(row);
        });
        
        calculateGrandTotal();
    });
</script>

@endsection