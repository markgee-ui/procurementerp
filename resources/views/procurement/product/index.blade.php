@extends('layouts.app')

@section('title', 'All Products')

@section('content')
    
    {{-- MODIFIED HEADER SECTION TO INCLUDE THE BUTTON --}}
    <div class="flex justify-between items-center mb-8">
        <header>
            <h1 class="text-3xl font-bold text-gray-800">Product Master List</h1>
            <p class="text-gray-600 mt-1">View all products recorded in the system, sourced from various suppliers.</p>
        </header>

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

    @if ($products->isEmpty())
        <div class="text-center p-10 bg-white rounded-xl shadow-lg">
            <p class="text-xl text-gray-500 font-semibold">No Products have been recorded yet.</p>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 divide-x divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Item Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Description</th>
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
                                            {{-- NOTE: Removed `onclick="return confirm(...)` - implement a custom modal for confirmation. --}}
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