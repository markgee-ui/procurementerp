@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
    <header class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Edit Product: {{ $product->item }}</h1>
    </header>

    <div class="bg-white rounded-xl shadow-lg p-6 max-w-5xl mx-auto">
        
        {{-- Display Validation Errors --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Whoops!</strong>
                <span class="block sm:inline">There were some problems with your input.</span>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form to Update Product --}}
        <form action="{{ route('procurement.product.update', $product->id) }}" method="POST">
            @csrf
            @method('PUT') {{-- Required for update actions --}}

            <div class="space-y-6">
                
                {{-- Item Name --}}
                <div>
                    <label for="item" class="block text-sm font-medium text-gray-700">Item Name</label>
                    <input type="text" name="item" id="item" value="{{ old('item', $product->item) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Item Code</label>
                    <textarea name="description" id="description" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('description', $product->description) }}</textarea>
                </div>

                {{-- Unit Price --}}
                <div>
                    <label for="unit_price" class="block text-sm font-medium text-gray-700">Unit Price (Kes)</label>
                    <input type="number" name="unit_price" id="unit_price" value="{{ old('unit_price', $product->unit_price) }}" step="0.01" min="0" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                
                {{-- Supplier --}}
                <div>
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700">Supplier</label>
                    <select name="supplier_id" id="supplier_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" 
                                {{ old('supplier_id', $product->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-5 border-t border-gray-200 flex justify-end space-x-3">
                    <a href="{{ route('procurement.product.index') }}" class="py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Update Product
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection