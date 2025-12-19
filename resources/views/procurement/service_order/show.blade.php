@extends('layouts.app')

@section('title', 'Service Order Details #' . ($serviceOrder->order_number ?? $serviceOrder->id))

@section('content')

<div class="max-w-6xl mx-auto space-y-8">

    {{-- Back Button and Header --}}
    <div class="flex justify-between items-center pb-4 border-b border-gray-200">
        <a href="{{ route('procurement.service-order.index') }}" 
           class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-indigo-600 transition">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Service Orders
        </a>

        <h1 class="text-3xl font-bold text-gray-800">Service Order Details</h1>
        
        <span class="px-3 py-1 text-sm font-semibold rounded-full 
            @if($serviceOrder->status == 'Draft') bg-yellow-100 text-yellow-800
            @elseif($serviceOrder->status == 'Approved') bg-green-100 text-green-800
            @else bg-gray-100 text-gray-800
            @endif">
            {{ strtoupper($serviceOrder->status) }}
        </span>
    </div>

    {{-- Action Buttons --}}
    <div class="flex justify-end items-center space-x-4">
        {{-- Display Options --}}
        <div class="flex items-center space-x-4 bg-gray-100 px-4 py-2 rounded-md border border-gray-200">
            <span class="text-sm font-bold text-gray-700">Display Options:</span>
            <div class="flex items-center">
                <input id="vat_toggle" type="checkbox" checked class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 cursor-pointer">
                <label for="vat_toggle" class="ml-2 block text-sm font-medium text-gray-700 cursor-pointer select-none">Include VAT (16%)</label>
            </div>
            <div class="flex items-center">
                <input id="wht_toggle" type="checkbox" checked class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 cursor-pointer">
                <label for="wht_toggle" class="ml-2 block text-sm font-medium text-gray-700 cursor-pointer select-none">Include WHT (2%)</label>
            </div>
        </div>

        {{-- Download PDF Button --}}
        <a href="{{ route('procurement.service-order.download', $serviceOrder->id) }}" 
           id="btn_download"
           data-base-href="{{ route('procurement.service-order.download', $serviceOrder->id) }}"
           target="_blank"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 transition">
            <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            Download PDF
        </a>

        <a href="{{ route('procurement.service-order.edit', $serviceOrder->id) }}" 
           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
            Edit SO
        </a>
    </div>

    {{-- SO Header Info --}}
    <div class="bg-white shadow-xl rounded-xl p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <h3 class="text-xs font-semibold uppercase text-gray-500">SO Number</h3>
            <p class="text-xl font-bold text-indigo-600">{{ $serviceOrder->order_number }}</p>
        </div>
        <div>
            <h3 class="text-xs font-semibold uppercase text-gray-500">Order Date</h3>
            <p class="text-lg text-gray-900">{{ $serviceOrder->order_date ? $serviceOrder->order_date->format('M d, Y') : 'N/A' }}</p>
        </div>
        <div>
            <h3 class="text-xs font-semibold uppercase text-gray-500">Project</h3>
            <p class="text-lg text-gray-900">{{ $serviceOrder->project_name ?? 'N/A' }}</p>
        </div>
        
        <div class="col-span-1 md:col-span-3">
            <h3 class="text-xs font-semibold uppercase text-gray-500">Supplier</h3>
            <p class="text-xl font-bold text-gray-900">{{ $serviceOrder->supplier->name }}</p>
            <p class="text-sm text-gray-600">{{ $serviceOrder->supplier->location }} | {{ $serviceOrder->supplier->contact }}</p>
        </div>
    </div>

    {{-- Service Details Table (Single Row) --}}
    <div class="bg-white shadow-xl rounded-xl p-6">
        <h2 class="text-2xl font-semibold text-gray-700 mb-6">Service Details</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service Description</th>
                        <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                        <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">Discount (%)</th>
                        <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total (KES)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-3 py-4 text-sm text-gray-900">
                            <div class="font-medium text-gray-900 whitespace-pre-line">{{ $serviceOrder->service_description }}</div>
                        </td>
                        <td class="px-3 py-4 text-sm text-right text-gray-700">
                            {{ number_format($serviceOrder->unit_price, 2) }}
                        </td>
                        <td class="px-3 py-4 text-sm text-right text-red-500">
                            {{ number_format($serviceOrder->discount, 1) }}%
                        </td>
                        <td class="px-3 py-4 text-sm font-bold text-right text-gray-900">
                            {{ number_format($serviceOrder->total_amount, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    {{-- Summary Footer --}}
    <div class="flex justify-end mt-8">
        <div class="w-1/3 p-6 bg-gray-50 rounded-lg shadow-inner space-y-3 border border-gray-200">
            <div class="flex justify-between text-sm text-gray-600">
                <span>Subtotal (Before Discount):</span>
                <span>{{ number_format($serviceOrder->unit_price, 2) }} KES</span>
            </div>
            <div class="flex justify-between text-sm text-red-600 border-b pb-2">
                <span>Total Discount:</span>
                <span>-{{ number_format(($serviceOrder->unit_price * $serviceOrder->discount / 100), 2) }} KES</span>
            </div>
            <div class="flex justify-between font-extrabold text-xl pt-2">
                <span>GRAND TOTAL:</span>
                <span class="text-indigo-700">{{ number_format($serviceOrder->total_amount, 2) }} KES</span>
            </div>
            <p class="text-[10px] text-gray-400 italic text-right mt-2">* Tax amounts are calculated during PDF export.</p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const vatToggle = document.getElementById('vat_toggle');
        const whtToggle = document.getElementById('wht_toggle');
        const downloadBtn = document.getElementById('btn_download');

        function updateUrls() {
            // Read the state of each toggle, using '1' (on) or '0' (off)
            const includeVat = vatToggle.checked ? '1' : '0';
            const includeWht = whtToggle.checked ? '1' : '0';
            
            // Build the query string
            const queryString = `?include_vat=${includeVat}&include_wht=${includeWht}`;

            // Update the Download Button link dynamically
            if (downloadBtn) {
                const baseHref = downloadBtn.getAttribute('data-base-href');
                downloadBtn.href = baseHref + queryString;
            }
        }

        // Initialize on load
        updateUrls();

        // Listen for checkbox changes
        vatToggle.addEventListener('change', updateUrls);
        whtToggle.addEventListener('change', updateUrls);
    });
</script>

@endsection