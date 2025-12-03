@extends('layouts.app')

@section('title', 'Purchase Order Details #' . ($purchaseOrder->order_number ?? $purchaseOrder->id))

@section('content')

<div class="max-w-6xl mx-auto space-y-8">

    {{-- Back Button and Header --}}
    <div class="flex justify-between items-center pb-4 border-b border-gray-200">
        <a href="{{ route('procurement.supplier.index') }}" 
           class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-indigo-600 transition">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Supplier List
        </a>

        <h1 class="text-3xl font-bold text-gray-800">Purchase Order Details</h1>
        
        {{-- Status Badge --}}
        <span class="px-3 py-1 text-sm font-semibold rounded-full 
            @if($purchaseOrder->status == 'Draft') bg-yellow-100 text-yellow-800
            @elseif($purchaseOrder->status == 'Issued') bg-green-100 text-green-800
            @else bg-gray-100 text-gray-800
            @endif">
            {{ strtoupper($purchaseOrder->status) }}
        </span>
    </div>

    {{-- Action Buttons --}}
    <div class="flex justify-end items-center space-x-4">
    
    {{-- 1. The Checkbox --}}
    <div class="flex items-center space-x-4 bg-gray-100 px-4 py-2 rounded-md border border-gray-200">
    <span class="text-sm font-bold text-gray-700">Display Options:</span>
    
    {{-- VAT Checkbox --}}
    <div class="flex items-center">
        <input id="vat_toggle" type="checkbox" checked
            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 cursor-pointer">
        <label for="vat_toggle" class="ml-2 block text-sm font-medium text-gray-700 cursor-pointer select-none">
            Include VAT (16%)
        </label>
    </div>

    {{-- Withholding Tax Checkbox --}}
    <div class="flex items-center">
        <input id="wht_toggle" type="checkbox" checked
            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 cursor-pointer">
        <label for="wht_toggle" class="ml-2 block text-sm font-medium text-gray-700 cursor-pointer select-none">
            Include Withholding Tax (2%)
        </label>
    </div>

    {{-- Payable Amount Toggle (Optional but recommended for clarity) --}}
    <div class="flex items-center">
        <input id="payable_toggle" type="checkbox" checked
            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 cursor-pointer">
        <label for="payable_toggle" class="ml-2 block text-sm font-medium text-gray-700 cursor-pointer select-none">
            Show Payable Amount
        </label>
    </div>
</div>

    {{-- 2. Print Button --}}
    {{-- NOTICE: id="btn_print" and data-base-href are CRITICAL --}}
    <a href="{{ route('procurement.order.print', $purchaseOrder->id) }}?include_tax=1" 
       target="_blank"
       id="btn_print"
       data-base-href="{{ route('procurement.order.print', $purchaseOrder->id) }}"
       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm-5-8h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2z"></path></svg>
        Print
    </a>

    {{-- 3. Download Button --}}
    {{-- NOTICE: id="btn_download" and data-base-href are CRITICAL --}}
    <a href="{{ route('procurement.order.download', $purchaseOrder->id) }}?include_tax=1" 
       target="_blank"
       id="btn_download"
       data-base-href="{{ route('procurement.order.download', $purchaseOrder->id) }}"
       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
        Download PDF
    </a>

    {{-- Edit Button (unchanged) --}}
    <a href="{{ route('procurement.order.edit', $purchaseOrder->id) }}" 
       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        Edit PO
    </a>
</div>

    {{-- PO Header Info Block --}}
    <div class="bg-white shadow-xl rounded-xl p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <h3 class="text-xs font-semibold uppercase text-gray-500">PO Number / ID</h3>
            <p class="text-xl font-bold text-indigo-600">{{ $purchaseOrder->order_number ?? $purchaseOrder->id }}</p>
        </div>
        <div>
            <h3 class="text-xs font-semibold uppercase text-gray-500">Order Date</h3>
            <p class="text-lg text-gray-900">{{ $purchaseOrder->order_date->format('M d, Y') }}</p>
        </div>
        <div>
            <h3 class="text-xs font-semibold uppercase text-gray-500">Required By</h3>
            <p class="text-lg text-gray-900">{{ $purchaseOrder->required_by_date ? $purchaseOrder->required_by_date->format('M d, Y') : 'N/A' }}</p>
        </div>
        
        <div class="col-span-1 md:col-span-3">
            <h3 class="text-xs font-semibold uppercase text-gray-500">Supplier</h3>
            <a href="{{ route('procurement.supplier.show', $purchaseOrder->supplier->id) }}" 
               class="text-xl font-bold text-gray-900 hover:text-indigo-600 transition">
                {{ $purchaseOrder->supplier->name }}
            </a>
            <p class="text-sm text-gray-600">{{ $purchaseOrder->supplier->location }} | {{ $purchaseOrder->supplier->contact }}</p>
        </div>
    </div>

    {{-- Line Items Table --}}
    <div class="bg-white shadow-xl rounded-xl p-6">
        <h2 class="text-2xl font-semibold text-gray-700 mb-6">Line Items ({{ $purchaseOrder->items->count() }})</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                        <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                        <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                        <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total (KSH)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($purchaseOrder->items as $item)
                    <tr>
                        <td class="px-3 py-4 text-sm text-gray-900">
                            {{ $item->product->item }}
                            <div class="text-xs text-gray-500">{{ $item->product->description }}</div>
                        </td>
                        <td class="px-3 py-4 text-sm text-gray-500">
                            {{ $item->product->unit ?? 'N/A' }}
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap text-sm text-right text-gray-700">
                            {{ number_format($item->unit_price, 2) }}
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-700">
                            {{ $item->quantity }}
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap text-sm text-right text-red-500">
                            {{ number_format($item->discount, 1) }}%
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap text-sm font-bold text-right text-gray-900">
                            {{ number_format($item->line_total, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    {{-- Totals and Notes Footer --}}
    <div class="flex justify-between mt-8">
        <div class="w-1/2 pr-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Project Name</h3>
            <p class="text-gray-700 p-3 bg-gray-50 rounded-md italic text-sm">{{ $purchaseOrder->project_name ?? 'No notes provided.' }}</p>
        </div>

        <div class="w-1/3">
            <div class="p-6 bg-gray-50 rounded-lg shadow-inner space-y-3">
                <div class="flex justify-between text-base text-gray-700">
                    <span>Subtotal:</span>
                    <span>{{ number_format($purchaseOrder->items->sum(fn($item) => $item->quantity * $item->unit_price), 2) }} KSH</span>
                </div>
                <div class="flex justify-between text-base text-red-600 border-b pb-3">
                    <span>Total Discount:</span>
                    <span>-{{ number_format($purchaseOrder->items->sum(fn($item) => ($item->quantity * $item->unit_price) * ($item->discount / 100)), 2) }} KSH</span>
                </div>
                <div class="flex justify-between font-extrabold text-xl pt-2">
                    <span>GRAND TOTAL:</span>
                    <span class="text-indigo-700">{{ number_format($purchaseOrder->total_amount, 2) }} KSH</span>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // New Checkbox IDs
        const vatToggle = document.getElementById('vat_toggle');
        const whtToggle = document.getElementById('wht_toggle');
        const payableToggle = document.getElementById('payable_toggle'); // New

        // Button IDs (Assuming they are named btn_print and btn_download)
        const printBtn = document.getElementById('btn_print');
        const downloadBtn = document.getElementById('btn_download');

        function updateUrls() {
            // Read the state of each toggle, using '1' (on) or '0' (off)
            const includeVat = vatToggle.checked ? '1' : '0';
            const includeWht = whtToggle.checked ? '1' : '0';
            const showPayable = payableToggle.checked ? '1' : '0'; // New parameter
            
            // Build the new query string
            const newQuery = `?include_vat=${includeVat}&include_wht=${includeWht}&show_payable=${showPayable}`;

            // Update both buttons
            if (printBtn) {
                printBtn.href = printBtn.getAttribute('data-base-href') + newQuery;
            }
            if (downloadBtn) {
                downloadBtn.href = downloadBtn.getAttribute('data-base-href') + newQuery;
            }
            
            // Optional: Log the URL to console for debugging
            console.log('New Print URL:', printBtn.href); 
        }

        // 1. Run immediately on load
        updateUrls();

        // 2. Run whenever any checkbox state changes
        vatToggle.addEventListener('change', updateUrls);
        whtToggle.addEventListener('change', updateUrls);
        payableToggle.addEventListener('change', updateUrls); // New listener
    });
</script>
@endsection