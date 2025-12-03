@extends('layouts.app')

@section('title', 'All Suppliers')

@section('content')

{{-- MODIFIED HEADER SECTION TO INCLUDE THE BUTTON --}}
<div class="flex justify-between items-center mb-8">


{{-- Quick Add Button --}}
{{-- Assuming 'procurement.create' is the route to add a new supplier --}}
<a href="{{ route('procurement.create') }}" 
   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
    {{-- Plus Icon --}}
    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
    </svg>
    Add New Supplier
</a>
</div>

{{-- NEW: Filters Section --}}
<div class="bg-white p-6 rounded-xl shadow-lg mb-6">
<h3 class="text-xl font-semibold text-gray-700 mb-4">Filter Suppliers</h3>
{{-- Assuming 'procurement.supplier.index' is the route for the main supplier list --}}
<form method="GET" action="{{ route('procurement.supplier.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

    {{-- Supplier Name Search --}}
    <div class="col-span-1">
        <label for="name_search" class="block text-sm font-medium text-gray-700">Supplier Name</label>
        <input type="text" name="name_search" id="name_search" 
                value="{{ request('name_search') }}"
                placeholder="Search by supplier name"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
    </div>

    {{-- Location Filter Dropdown (Requires $locations from Controller) --}}
    <div class="col-span-1">
        <label for="location_filter" class="block text-sm font-medium text-gray-700">Filter by Location</label>
        <select id="location_filter" name="location_filter" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            <option value="">-- All Locations --</option>
            {{-- Loop through distinct locations passed from the controller --}}
            @foreach ($locations as $location)
                <option value="{{ $location }}" 
                        {{ request('location_filter') == $location ? 'selected' : '' }}>
                    {{ $location }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Action Buttons --}}
    <div class="col-span-2 flex space-x-3">
        <button type="submit" 
                class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 w-full sm:w-auto">
            {{-- Filter Icon --}}
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707v3.586a1 1 0 01-1.555.832l-2.5-1A1 1 0 018 17.586V15a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            Apply Filters
        </button>
        
        {{-- Reset Button (Only shows if filters are currently active) --}}
        @if (request()->hasAny(['name_search', 'location_filter']))
            <a href="{{ route('procurement.supplier.index') }}" 
               class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 w-full sm:w-auto">
                Reset
            </a>
        @endif
    </div>
</form>
</div>

@if ($suppliers->isEmpty())
    {{-- Display message if no suppliers are found --}}
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-yellow-800">No suppliers match your current filters or none have been added yet.</p>
            </div>
        </div>
    </div>
@else
    {{-- Display table if suppliers exist --}}
    <div class="overflow-x-auto shadow-lg rounded-xl">
        <div class="inline-block min-w-full align-middle">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Name</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Contact</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Location</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">KRA Pin</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Sales Person Contact</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Payment Details</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($suppliers as $supplier)
                        <tr class="hover:bg-gray-50">
                            {{-- Name and Email --}}
                            <td class="px-4 py-3 whitespace-nowrap border-r border-gray-200">
                                <div class="text-sm font-medium text-gray-900">{{ $supplier->name }}</div>
                                <div class="text-xs text-gray-500">{{ $supplier->email ?? 'No Email' }}</div>
                            </td>

                            {{-- Phone --}}
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 border-r border-gray-200">
                                {{ $supplier->phone ?? 'N/A' }}
                            </td>

                            {{-- Location --}}
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 border-r border-gray-200">
                                {{ $supplier->location ?? 'N/A' }}
                            </td>
                            
                            {{-- KRA Pin --}}
<td class="px-4 py-3 whitespace-nowrap text-sm border-r border-gray-200">

    @if ($supplier->kra_pin)
        <span class="px-2 py-1 text-green-700 bg-green-100 border border-green-300 rounded-full text-xs font-semibold">
            {{ $supplier->kra_pin }}
        </span>
    @else
        <span class="px-2 py-1 text-red-700 bg-red-100 border border-red-300 rounded-full text-xs font-semibold">
            Not Compliant
        </span>
    @endif

</td>

                            {{-- Sales Person Contact --}}
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 border-r border-gray-200">
                                {{ $supplier->sales_person_contact ?? 'N/A' }}
                            </td>

                            {{-- Payment Details (Combined) --}}
                            <td class="px-4 py-3 text-sm text-gray-500 border-r border-gray-200">
                                {{-- Display Bank details if available --}}
                                @if ($supplier->bank_name && $supplier->account_number)
                                    <div class="truncate">
                                        <span class="font-semibold text-gray-800">{{ $supplier->bank_name }}</span><br>
                                        <span class="text-xs text-gray-500">Acc: {{ $supplier->account_number }}</span>
                                    </div>
                                {{-- Otherwise, display M-Pesa details if available --}}
                                @elseif ($supplier->paybill_number || $supplier->till_number)
                                    @if ($supplier->paybill_number)
                                        <div class="text-xs">
                                            <span class="font-semibold">Paybill:</span> {{ $supplier->paybill_number }}
                                        </div>
                                    @endif
                                    @if ($supplier->till_number)
                                        <div class="text-xs">
                                            <span class="font-semibold">Till:</span> {{ $supplier->till_number }}
                                        </div>
                                    @endif
                                @else
                                    N/A
                                @endif
                            </td>

                            {{-- NEW: Actions Cell --}}
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    {{-- Edit Button --}}
                                    {{-- Assuming 'procurement.supplier.edit' is the edit route --}}
                                    <a href="{{ route('procurement.order.create', ['supplier' => $supplier->id]) }}" 
           title="Create Purchase Order"
           class="text-green-600 hover:text-green-900 transition duration-150 ease-in-out p-1 rounded-md hover:bg-green-100">
            {{-- Shopping Cart Icon --}}
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        </a>
                                    <a href="{{ route('procurement.supplier.edit', $supplier->id) }}" 
                                        title="Edit Supplier"
                                        class="text-indigo-600 hover:text-indigo-900 transition duration-150 ease-in-out p-1 rounded-md hover:bg-indigo-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-7-7l-2 2m5-5l2 2m-2-2l-3-3m3 3l-3 3"></path></svg>
                                    </a>
                                  <a href="{{ route('procurement.supplier.show', $supplier->id) }}" 
    title="View Supplier Details"
    class="text-teal-600 hover:text-teal-900 transition duration-150 ease-in-out p-1 rounded-md hover:bg-teal-100">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
</a>


                                    {{-- Delete Form/Button --}}
                                    {{-- Assuming 'procurement.supplier.destroy' is the delete route --}}
                                    <form action="{{ route('procurement.supplier.destroy', $supplier->id) }}" method="POST" 
                                            onsubmit="return confirm('Are you sure you want to delete supplier \'{{ $supplier->name }}\'? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Delete Supplier" 
                                                class="text-red-600 hover:text-red-900 transition duration-150 ease-in-out p-1 rounded-md hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    {{-- PAGINATION LINKS --}}
    <div class="mt-4">
        {{ $suppliers->links() }}
    </div>
@endif

@endsection