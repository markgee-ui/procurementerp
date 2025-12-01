@extends('layouts.app')

@section('title', 'All Products')

@section('content')
    
    {{-- MODIFIED HEADER SECTION TO INCLUDE THE BUTTON --}}
    <div class="flex justify-between items-center mb-8">

        {{-- Quick Add Button, links to the procurement creation page --}}
        <a href="{{ route('procurement.create') }}" 
           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
            {{-- Plus Icon --}}
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New Product
        </a>
    </div>

    {{-- NEW: Filters Section --}}
    <div class="bg-white p-6 rounded-xl shadow-lg mb-6">
        <h3 class="text-xl font-semibold text-gray-700 mb-4">Filter Products</h3>
        <form method="GET" action="{{ route('procurement.product.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            
            {{-- Item Name Search --}}
            <div class="col-span-1">
                <label for="item_name" class="block text-sm font-medium text-gray-700">Item Name</label>
                <input type="text" name="item_name" id="item_name" 
                       value="{{ request('item_name') }}"
                       placeholder="Search by product name"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            {{-- Supplier Filter Dropdown (Requires $suppliers from Controller) --}}
            <div class="col-span-1">
                <label for="supplier_id" class="block text-sm font-medium text-gray-700">Filter by Supplier</label>
                <select id="supplier_id" name="supplier_id" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">-- All Suppliers --</option>
                    {{-- Loop through suppliers passed from the controller --}}
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" 
                                {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
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
                @if (request()->hasAny(['item_name', 'supplier_id']))
                    <a href="{{ route('procurement.product.index') }}" 
                       class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 w-full sm:w-auto">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    @if ($products->isEmpty())
        <div class="text-center p-10 bg-white rounded-xl shadow-lg">
            {{-- Custom message if filtered result is empty --}}
            @if (request()->hasAny(['item_name', 'supplier_id']))
                <p class="text-xl text-gray-500 font-semibold">No products match your current filter criteria.</p>
            @else
                <p class="text-xl text-gray-500 font-semibold">No Products have been recorded yet.</p>
            @endif
        </div>
    @else
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 divide-x divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Item Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Item Code</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Unit Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Supplier</th>
                            {{-- NEW: Actions Header --}}
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($products as $product)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-200">{{ $product->item }}</td>
                                <td class="px-6 py-4 whitespace-normal text-sm text-gray-500 max-w-xs border-r border-gray-200">{{ $product->description ?: 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-900 border-r border-gray-200">Kes {{ number_format($product->unit_price, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 border-r border-gray-200">{{ $product->supplier->name ?? 'N/A' }}</td>
                                {{-- NEW: Actions Cell --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                    {{-- Edit Link (Route needs to be defined) --}}
                                    <a href="{{ route('procurement.product.edit', $product->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">
                                        Edit
                                    </a>
                                    
                                    {{-- Delete Form (Requires a form for POST/DELETE method) --}}
                                    <form action="{{ route('procurement.product.destroy', $product->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{-- PAGINATION LINKS --}}
        <div class="mt-4">
            {{ $products->links() }}
        </div>
    @endif
@endsection