@extends('layouts.app')

@section('title', 'Supplier Details: ' . $supplier->name)

@section('content')

{{-- Main Container - Centered and max width set to large --}}
<div class="max-w-6xl mx-auto"> 

    {{-- Back Button --}}
    <div class="mb-6">
        <a href="{{ route('procurement.supplier.index') }}" 
           class="inline-flex items-center text-base font-semibold text-gray-500 hover:text-indigo-600 transition duration-150 ease-in-out">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Supplier List
        </a>
    </div>

    {{-- Header & Action Button --}}
    <div class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <header>
            <h1 class="text-4xl font-extrabold text-gray-900">{{ $supplier->name }}</h1>
            <p class="text-sm text-gray-500 mt-1">Supplier ID: {{ $supplier->id }} | Registered {{ $supplier->created_at->diffForHumans() ?? 'N/A' }}</p>
        </header>
        
        {{-- Edit Button --}}
        <a href="{{ route('procurement.supplier.edit', $supplier->id) }}" 
           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-full shadow-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out transform hover:scale-105">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            Edit Supplier
        </a>
    </div>

    {{-- Supplier Detail Cards --}}
    <div class="space-y-8">
        
        {{-- Primary Information Section --}}
        <div class="bg-white shadow-xl rounded-xl p-8  ">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center mb-6">
                <svg class="w-6 h-6 mr-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                General Contact Information
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                {{-- Card Item: Legal Name --}}
                <div class="border p-4 rounded-lg bg-gray-50/50">
                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Legal Name</dt>
                    <dd class="mt-1 text-xl font-extrabold text-gray-900">{{ $supplier->name }}</dd>
                </div>
                
                {{-- Card Item: KRA PIN --}}
                <div class="border p-4 rounded-lg bg-gray-50/50">
                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">KRA PIN</dt>
                    <dd class="mt-1 text-base font-medium text-gray-700">{{ $supplier->kra_pin ?? 'N/A' }}</dd>
                </div>

                 {{-- Card Item: Location --}}
                <div class="border p-4 rounded-lg bg-gray-50/50">
                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Location / City</dt>
                    <dd class="mt-1 text-base font-medium text-gray-700">{{ $supplier->location ?? 'N/A' }}</dd>
                </div>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-6">
                {{-- Card Item: Phone --}}
                <div class="border p-4 rounded-lg bg-white shadow-sm">
                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Main Contact Number</dt>
                    <dd class="mt-1 text-base font-medium text-gray-700">{{ $supplier->phone ?? 'N/A' }}</dd>
                </div>
                
                {{-- Card Item: Email --}}
                <div class="border p-4 rounded-lg bg-white shadow-sm">
                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Email Address</dt>
                    <dd class="mt-1 text-base text-indigo-600 truncate">{{ $supplier->email ?? 'N/A' }}</dd>
                </div>

                {{-- Card Item: Sales Contact --}}
                <div class="border p-4 rounded-lg bg-white shadow-sm">
                    <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Sales Person Contact</dt>
                    <dd class="mt-1 text-base font-medium text-gray-700">{{ $supplier->sales_person_contact ?? 'N/A' }}</dd>
                </div>
            </div>
            {{-- **NEW SECTION: SHOP PHOTO DISPLAY** --}}
            <div class="p-4 rounded-lg bg-indigo-50/50 border border-indigo-200">
                <h3 class="text-lg font-bold text-gray-800 flex items-center mb-4">
                    <svg class="w-5 h-5 mr-2 text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L15 15m0 0l4.586-4.586a2 2 0 012.828 0L20 18m-9.5-2.5h.01M7 10h.01M16 18h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2h2"></path></svg>
                    Shop / Hardware Photo
                </h3>
                
                @if ($supplier->shop_photo_path)
                    <div class="mt-4 border border-gray-300 rounded-lg overflow-hidden max-w-lg shadow-md">
                        {{-- Laravel's asset() helper generates the full URL to the file in the storage/app/public directory --}}
                        <img src="{{ asset('storage/' . $supplier->shop_photo_path) }}" 
                             alt="Photo of {{ $supplier->name }}" 
                             class="w-full h-auto object-cover" 
                             style="max-height: 300px;"
                        >
                    </div>
                    <p class="text-sm text-gray-600 mt-2">File path: `{{ $supplier->shop_photo_path }}`</p>
                @else
                    <p class="text-gray-500 italic mt-2">No shop photo has been uploaded for this supplier yet.</p>
                @endif
            </div>
        </div>
        
        {{-- Payment Details Section --}}
        <div class="bg-white shadow-xl rounded-xl p-8 ">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center mb-6">
                 <svg class="w-6 h-6 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Financial & Payment Information
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                {{-- Bank Account Card --}}
                <div class="border p-6 rounded-lg bg-green-50/50">
                    <h3 class="text-base font-semibold text-gray-700 flex items-center mb-2">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        Bank Details
                    </h3>
                    @if ($supplier->bank_name && $supplier->account_number)
                        <dl class="mt-2 space-y-1">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Bank Name</dt>
                                <dd class="text-lg font-bold text-gray-900">{{ $supplier->bank_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Account Number</dt>
                                <dd class="text-lg font-bold text-gray-900">{{ $supplier->account_number }}</dd>
                            </div>
                        </dl>
                    @else
                        <p class="text-gray-500 italic mt-2">Bank details are not provided.</p>
                    @endif
                </div>

                {{-- M-Pesa Details Card --}}
                <div class="border p-6 rounded-lg bg-green-50/50">
                    <h3 class="text-base font-semibold text-gray-700 flex items-center mb-2">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        Mobile Money (M-Pesa)
                    </h3>
                    @if ($supplier->paybill_number || $supplier->till_number)
                         <dl class="mt-2 space-y-1">
                            @if ($supplier->paybill_number)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Paybill Number</dt>
                                    <dd class="text-lg font-bold text-gray-900">{{ $supplier->paybill_number }}</dd>
                                </div>
                            @endif
                            @if ($supplier->till_number)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Till Number (Buy Goods)</dt>
                                    <dd class="text-lg font-bold text-gray-900">{{ $supplier->till_number }}</dd>
                                </div>
                            @endif
                         </dl>
                    @else
                        <p class="text-gray-500 italic mt-2">Mobile money details are not provided.</p>
                    @endif
                </div>
                
            </div>
        </div>
        
    </div>
</div>

@endsection