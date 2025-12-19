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
            <p class="text-lg text-gray-900">{{ $serviceOrder->order_date->format('M d, Y') }}</p>
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

    {{-- Line Items Table --}}
    <div class="bg-white shadow-xl rounded-xl p-6">
        <h2 class="text-2xl font-semibold text-gray-700 mb-6">Service Items ({{ $serviceOrder->items->count() }})</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item/Description</th>
                        <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total (KES)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
    <tr>
        <td class="px-3 py-4 text-sm text-gray-900">
            {{-- Pulling directly from ServiceOrder now --}}
            <div class="font-medium text-gray-900">{{ $serviceOrder->service_description }}</div>
        </td>
        <td class="px-3 py-4 text-sm font-bold text-right text-gray-900">
            {{ number_format($serviceOrder->total_amount, 2) }}
        </td>
    </tr>
</tbody>
            </table>
        </div>
    </div>
    
    <div class="flex justify-end mt-8">
        <div class="w-1/3 p-6 bg-gray-50 rounded-lg shadow-inner space-y-3">
            <div class="flex justify-between font-extrabold text-xl pt-2">
                <span>GRAND TOTAL:</span>
                <span class="text-indigo-700">{{ number_format($serviceOrder->total_amount, 2) }} KES</span>
            </div>
        </div>
    </div>
</div>
@endsection