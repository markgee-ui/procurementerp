@extends('layouts.app')

@section('title', 'Edit Service Order #' . ($serviceOrder->order_number))

@section('content')
{{-- Increased width to max-w-7xl --}}
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Edit Service Order: {{ $serviceOrder->order_number }}</h1>
        
        {{-- Added Back Button --}}
        <a href="{{ route('procurement.service-order.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-900 font-medium">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to List
        </a>
    </div>

    <form action="{{ route('procurement.service-order.update', $serviceOrder->id) }}" method="POST" class="bg-white shadow-xl rounded-xl p-8">
        @csrf
        @method('PUT')
        
        <input type="hidden" name="supplier_id" value="{{ $serviceOrder->supplier_id }}">

        <div class="grid grid-cols-2 gap-6 mb-8">
            <div class="col-span-1">
                <label class="block text-sm font-medium text-gray-500 uppercase">Supplier</label>
                <p class="mt-1 text-lg font-bold text-gray-800">{{ $serviceOrder->supplier->name }}</p>
            </div>
            
            <div class="col-span-1">
                <label for="project_name" class="block text-sm font-medium text-gray-700">Project Name</label>
                <input type="text" name="project_name" id="project_name"
                       value="{{ old('project_name', $serviceOrder->project_name) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
        </div>
        
        <h3 class="text-xl font-semibold text-gray-700 mb-6 border-t pt-6">Service Details</h3>

        <div class="space-y-6">
            {{-- Description --}}
            <div>
                <label for="service_description" class="block text-sm font-medium text-gray-700">Service Description</label>
                <textarea name="service_description" id="service_description" rows="3" 
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                          placeholder="Describe the service provided...">{{ old('service_description', $serviceOrder->service_description) }}</textarea>
            </div>

            <div class="grid grid-cols-3 gap-6">
                {{-- Unit Price --}}
                <div>
                    <label for="unit_price" class="block text-sm font-medium text-gray-700">Service Price (KES)</label>
                    <input type="number" step="0.01" name="unit_price" id="unit_price" 
                           value="{{ old('unit_price', $serviceOrder->unit_price) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm calculation-trigger">
                </div>

                {{-- Final Total Display --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Final Total</label>
                    <div class="mt-1 block w-full p-2 bg-gray-50 border border-gray-200 rounded-md font-bold text-gray-900">
                        KES <span id="total_amount_display">{{ number_format($serviceOrder->total_amount, 2) }}</span>
                    </div>
                    <input type="hidden" name="total_amount" id="total_amount_hidden" value="{{ $serviceOrder->total_amount }}">
                </div>
            </div>
        </div>
        
        <div class="mt-10 flex justify-end space-x-3">
            <a href="{{ route('procurement.service-order.index') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition duration-150">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 shadow-md font-semibold transition duration-150">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const priceInput = document.getElementById('unit_price');
        const discountInput = document.getElementById('discount');
        const totalDisplay = document.getElementById('total_amount_display');
        const totalHidden = document.getElementById('total_amount_hidden');

        function calculateTotal() {
            const price = parseFloat(priceInput.value) || 0;
            const discountPercent = parseFloat(discountInput.value) || 0;

            const discountAmount = price * (discountPercent / 100);
            const finalTotal = price - discountAmount;

            totalDisplay.textContent = finalTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            totalHidden.value = finalTotal.toFixed(2);
        }

        // Trigger calculation on input change
        document.querySelectorAll('.calculation-trigger').forEach(input => {
            input.addEventListener('input', calculateTotal);
        });
    });
</script>
@endpush