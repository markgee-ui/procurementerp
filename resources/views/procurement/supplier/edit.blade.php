@extends('layouts.app')

@section('title', 'Edit Supplier: ' . $supplier->name)

@section('content')

<div class="max-w-7xl mx-auto">
    <header class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Edit Supplier Details</h1>
            <p class="text-gray-600 mt-1">
                Updating master information for <span class="font-semibold text-indigo-600">{{ $supplier->name }}</span>.
            </p>
        </div>
        {{-- Top Back Button --}}
        <a href="{{ route('procurement.supplier.index') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
            <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to List
        </a>
    </header>

    {{-- Session Message (Success/Error) --}}
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md mb-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-8 rounded-xl shadow-lg">
          
        {{-- Form action points to the updateSupplier method using PUT method --}}
        <form action="{{ route('procurement.supplier.update', $supplier->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                
                <h2 class="text-xl font-semibold text-gray-700 border-b pb-2 mb-4">General Information</h2>

                {{-- Row 1: Name and Location --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Supplier Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" required
                               value="{{ old('name', $supplier->name) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700">Location / City <span class="text-red-500">*</span></label>
                        <input type="text" name="location" id="location" required
                               value="{{ old('location', $supplier->location) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                {{-- Row 2: Main Contact and KRA Pin --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="contact" class="block text-sm font-medium text-gray-700">Main Contact Number <span class="text-red-500">*</span></label>
                        <input type="text" name="contact" id="contact" required
                               value="{{ old('contact', $supplier->contact) }}"
                               placeholder="+254..."
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="kra_pin" class="block text-sm font-medium text-gray-700">KRA Pin</label>
                        <input type="text" name="kra_pin" id="kra_pin"
                               value="{{ old('kra_pin', $supplier->kra_pin) }}"
                               placeholder="e.g., A123456789Z"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <h2 class="text-xl font-semibold text-gray-700 border-b pb-2 mb-4 pt-4">Sales & Logistics Contact</h2>

                {{-- Row 3: Sales Contact and Email --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="sales_person_contact" class="block text-sm font-medium text-gray-700">Sales Person Contact</label>
                        <input type="text" name="sales_person_contact" id="sales_person_contact"
                               value="{{ old('sales_person_contact', $supplier->sales_person_contact) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" name="email" id="email"
                               value="{{ old('email', $supplier->email) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <h2 class="text-xl font-semibold text-gray-700 border-b pb-2 mb-4 pt-4">Payment Information</h2>
                <p class="text-sm text-gray-500">Fill in EITHER the Bank Details OR the Mobile Money Details.</p>

                {{-- Row 4: Bank Details --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 border p-4 rounded-md bg-gray-50">
                    <div class="md:col-span-1">
                        <label for="bank_name" class="block text-sm font-medium text-gray-700">Bank Name</label>
                        <input type="text" name="bank_name" id="bank_name"
                               value="{{ old('bank_name', $supplier->bank_name) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="md:col-span-2">
                        <label for="account_number" class="block text-sm font-medium text-gray-700">Account Number</label>
                        <input type="text" name="account_number" id="account_number"
                               value="{{ old('account_number', $supplier->account_number) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                {{-- Row 5: Mobile Money (M-Pesa) Details --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border p-4 rounded-md bg-gray-50">
                    <div>
                        <label for="paybill_number" class="block text-sm font-medium text-gray-700">M-Pesa Paybill Number</label>
                        <input type="text" name="paybill_number" id="paybill_number"
                               value="{{ old('paybill_number', $supplier->paybill_number) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="till_number" class="block text-sm font-medium text-gray-700">M-Pesa Till Number (Buy Goods)</label>
                        <input type="text" name="till_number" id="till_number"
                               value="{{ old('till_number', $supplier->till_number) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

            </div>

            <h2 class="text-xl font-semibold text-gray-700 border-b pb-2 mb-4 pt-4">Associated Products</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="products-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Item Name *</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Unit Price *</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Unit *</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="product-rows">
                        @foreach($supplier->products as $index => $product)
                        <tr>
                            <td class="px-2 py-2">
                                <input type="hidden" name="products[{{ $index }}][id]" value="{{ $product->id }}">
                                <input type="text" name="products[{{ $index }}][item]" value="{{ $product->item }}" required class="block w-full rounded-md border-gray-300 shadow-sm text-sm">
                            </td>
                            <td class="px-2 py-2">
                                <input type="number" step="0.01" name="products[{{ $index }}][unit_price]" value="{{ $product->unit_price }}" required class="block w-full rounded-md border-gray-300 shadow-sm text-sm">
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" name="products[{{ $index }}][unit]" value="{{ $product->unit }}" required class="block w-full rounded-md border-gray-300 shadow-sm text-sm" placeholder="pcs, kg, etc">
                            </td>
                            <td class="px-2 py-2">
                                <input type="text" name="products[{{ $index }}][description]" value="{{ $product->description }}" class="block w-full rounded-md border-gray-300 shadow-sm text-sm">
                            </td>
                            <td class="px-2 py-2 text-center"></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button type="button" id="add-product-row" class="mt-4 inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none">
                + Add New Product Row
            </button>

            {{-- Action Buttons --}}
            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('procurement.supplier.index') }}" 
                   class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition duration-150 ease-in-out">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let productIndex = {{ $supplier->products->count() }};
    
    document.getElementById('add-product-row').addEventListener('click', function() {
        const tbody = document.getElementById('product-rows');
        const row = `
            <tr class="bg-yellow-50">
                <td class="px-2 py-2">
                    <input type="text" name="products[${productIndex}][item]" required class="block w-full rounded-md border-gray-300 shadow-sm text-sm">
                </td>
                <td class="px-2 py-2">
                    <input type="number" step="0.01" name="products[${productIndex}][unit_price]" required class="block w-full rounded-md border-gray-300 shadow-sm text-sm">
                </td>
                <td class="px-2 py-2">
                    <input type="text" name="products[${productIndex}][unit]" required class="block w-full rounded-md border-gray-300 shadow-sm text-sm" placeholder="e.g. pcs">
                </td>
                <td class="px-2 py-2">
                    <input type="text" name="products[${productIndex}][description]" class="block w-full rounded-md border-gray-300 shadow-sm text-sm">
                </td>
                <td class="px-2 py-2 text-center">
                    <button type="button" onclick="this.closest('tr').remove()" class="text-red-600 hover:text-red-900 font-bold">×</button>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);
        productIndex++;
    });
</script>
@endsection