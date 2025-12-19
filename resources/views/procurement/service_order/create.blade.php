@extends('layouts.app')

@section('title', 'Create Service Order for ' . $supplier->name)

@section('content')

@php
    $inputClass = 'mt-1 block w-full border border-gray-300 rounded-md shadow-sm sm:text-sm p-3 focus:ring-indigo-500 focus:border-indigo-500';
@endphp

<div class="max-w-7xl mx-auto space-y-8">
    
    {{-- Back Button --}}
    <a href="{{ route('procurement.supplier.index') }}" 
        class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-indigo-600 transition">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Supplier List
    </a>

    <header class="pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-bold text-gray-800">New Service Order</h1>
        <p class="text-xl text-indigo-600">Supplier: {{ $supplier->name }}</p>
    </header>

    {{-- Form for SO creation --}}
    <form action="{{ route('procurement.service-order.store') }}" method="POST" class="bg-white shadow-xl rounded-xl p-8">
        @csrf
        
        {{-- Hidden field to pass supplier ID --}}
        <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">

        <div class="grid grid-cols-1 gap-y-6">
            
            {{-- Project Name --}}
            <div>
                <label for="project_name" class="block text-sm font-medium text-gray-700">
                    Project Name / Site <span class="text-red-500">*</span>
                </label>
                <input type="text" name="project_name" id="project_name"
                       class="{{ $inputClass }}"
                       value="{{ old('project_name') }}"
                       placeholder="e.g. Westside Heights Plumbing Works"
                       required>
                @error('project_name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Service Description --}}
            <div>
                <label for="service_description" class="block text-sm font-medium text-gray-700">
                    Service Description <span class="text-red-500">*</span>
                </label>
                <textarea name="service_description" id="service_description" rows="6"
                          class="{{ $inputClass }}"
                          placeholder="Provide details of the labor, repairs, or works to be performed..."
                          required>{{ old('service_description') }}</textarea>
                <p class="mt-2 text-xs text-gray-500 italic">Clearly specify the scope of work to avoid billing disputes.</p>
                @error('service_description')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Amount --}}
            <div class="max-w-xs">
                <label for="amount" class="block text-sm font-medium text-gray-700">
                    Total Agreed Amount (KES) <span class="text-red-500">*</span>
                </label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">KES</span>
                    </div>
                    <input type="number" name="amount" id="amount" step="0.01"
                           class="{{ $inputClass }} pl-12"
                           value="{{ old('amount') }}"
                           placeholder="0.00"
                           required>
                </div>
                @error('amount')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end items-center">
            <div class="space-x-3">
                <a href="{{ route('procurement.supplier.index') }}" 
                   class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-500">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Save Service Order
                </button>
            </div>
        </div>
    </form>

    {{-- Supplier Info Footer --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200 text-sm text-gray-600">
        <div><span class="font-bold">Location:</span> {{ $supplier->location ?? 'N/A' }}</div>
        <div><span class="font-bold">Contact:</span> {{ $supplier->contact ?? 'N/A' }}</div>
        <div class="md:text-right"><span class="font-bold">Date:</span> {{ date('d M, Y') }}</div>
    </div>
</div>

@endsection