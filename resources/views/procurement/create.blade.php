@extends('layouts.app')

@section('title', 'Add New Supplier & Products')

@push('styles')
<style>
    /* ... (Your existing custom styles) ... */
    :root {
        --primary-color: #3b82f6; /* Tailwind blue-500 */
    }
    .form-card {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .btn-primary {
        transition: background-color 0.3s, transform 0.1s;
        background-color: var(--primary-color);
    }
    .btn-primary:hover {
        background-color: #2563eb; /* Blue-600 */
    }
    .input-field {
        border-color: #d1d5db; /* Gray-300 */
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .input-field:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
        outline: none;
    }
    .product-row-exit {
        transition: opacity 0.3s ease-out, max-height 0.5s ease-out;
        max-height: 500px;
        overflow: hidden;
    }
</style>
@endpush

@section('content')
    <header class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Add New Supplier & Products</h1>
    </header>

    <div id="messageBox" class="hidden p-4 mb-6 rounded-lg font-medium text-white bg-red-500 shadow-md">
        </div>

    <form id="procurementForm" action="{{ route('procurement.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf 

        <section class="bg-white p-6 sm:p-8 rounded-xl form-card">
            <h2 class="text-xl font-semibold mb-4 text-gray-700 border-b pb-2">1. Supplier & Contact Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Supplier Name --}}
                <div class="col-span-1">
                    <label for="supplierName" class="block text-sm font-medium text-gray-700 mb-1">Supplier Name <span class="text-red-500">*</span></label>
                    <input type="text" id="supplierName" name="supplier_name" required
                           value="{{ old('supplier_name') }}"
                           class="input-field w-full p-3 border rounded-lg text-gray-900 @error('supplier_name') border-red-500 @enderror"
                           >
                    @error('supplier_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror 
                </div>

                {{-- KRA Pin --}}
                <div class="col-span-1">
                    <label for="kraPin" class="block text-sm font-medium text-gray-700 mb-1">KRA PIN (TIN)</label>
                    <input type="text" id="kraPin" name="kra_pin"
                           value="{{ old('kra_pin') }}"
                           class="input-field w-full p-3 border rounded-lg text-gray-900 @error('kra_pin') border-red-500 @enderror"
                         >
                    @error('kra_pin') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Location --}}
                <div class="col-span-1">
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location (City/Country) <span class="text-red-500">*</span></label>
                    <input type="text" id="location" name="location" required
                           value="{{ old('location') }}"
                           class="input-field w-full p-3 border rounded-lg text-gray-900 @error('location') border-red-500 @enderror"
                           >
                    @error('location') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror 
                </div>

                {{-- Contact (Email/Phone) --}}
                <div class="col-span-1">
                    <label for="contact" class="block text-sm font-medium text-gray-700 mb-1">Supplier Contact (Phone or Email) <span class="text-red-500">*</span></label>
                    <input type="text" id="contact" name="contact" required
                           value="{{ old('contact') }}"
                           class="input-field w-full p-3 border rounded-lg text-gray-900 @error('contact') border-red-500 @enderror"
                           >
                    @error('contact') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Sales Person Contact --}}
                <div class="col-span-1">
                    <label for="salesPersonContact" class="block text-sm font-medium text-gray-700 mb-1">Sales Person Name/Contact</label>
                    <input type="text" id="salesPersonContact" name="sales_person_contact"
                           value="{{ old('sales_person_contact') }}"
                           class="input-field w-full p-3 border rounded-lg text-gray-900 @error('sales_person_contact') border-red-500 @enderror"
                           >
                    @error('sales_person_contact') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                {{-- Shop Photo UPLOAD --}}
                <div class="col-span-1">
                    <label for="shop_photo" class="block text-sm font-medium text-gray-700 mb-1">
                        Upload Shop/Hardware Photo
                    </label>
                    
                    <input 
                        type="file" 
                        id="shop_photo" 
                        name="shop_photo"
                        accept="image/*"
                        class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 
                                 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 
                                 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 
                                 hover:file:bg-indigo-100 @error('shop_photo') border-red-500 @enderror"
                    >
                    
                    @error('shop_photo') 
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                    @enderror
                    
                    <p class="text-xs text-gray-500 mt-1">
                        Upload the image file directly (Max 2MB).
                    </p>
                </div>

                {{-- Address --}}
                <div class="col-span-full">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Physical Address <span class="text-red-500">*</span></label>
                    <input type="text" id="address" name="address" required
                           value="{{ old('address') }}"
                           class="input-field w-full p-3 border rounded-lg text-gray-900 @error('address') border-red-500 @enderror"
                           >
                    @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <section class="bg-white p-6 sm:p-8 rounded-xl form-card">
            <h2 class="text-xl font-semibold mb-4 text-gray-700 border-b pb-2">2. Payment & Bank Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Bank Account Number --}}
                <div class="col-span-1">
                    <label for="accountNumber" class="block text-sm font-medium text-gray-700 mb-1">Bank Account Number</label>
                    <input type="text" id="accountNumber" name="account_number"
                           value="{{ old('account_number') }}"
                           class="input-field w-full p-3 border rounded-lg text-gray-900 @error('account_number') border-red-500 @enderror"
                           >
                    @error('account_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                {{-- Bank Name --}}
                <div class="col-span-1">
                    <label for="bankName" class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                    <input type="text" id="bankName" name="bank_name"
                           value="{{ old('bank_name') }}"
                           class="input-field w-full p-3 border rounded-lg text-gray-900 @error('bank_name') border-red-500 @enderror"
                           >
                    @error('bank_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                {{-- M-Pesa Paybill Number --}}
                <div class="col-span-1">
                    <label for="paybillNumber" class="block text-sm font-medium text-gray-700 mb-1">M-Pesa Paybill Number</label>
                    <input type="text" id="paybillNumber" name="paybill_number"
                           value="{{ old('paybill_number') }}"
                           class="input-field w-full p-3 border rounded-lg text-gray-900 @error('paybill_number') border-red-500 @enderror"
                           >
                    @error('paybill_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                {{-- M-Pesa Till Number --}}
                <div class="col-span-1">
                    <label for="tillNumber" class="block text-sm font-medium text-gray-700 mb-1">M-Pesa Till Number</label>
                    <input type="text" id="tillNumber" name="till_number"
                           value="{{ old('till_number') }}"
                           class="input-field w-full p-3 border rounded-lg text-gray-900 @error('till_number') border-red-500 @enderror"
                           >
                    @error('till_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
            </div>
        </section>

        <section class="bg-white p-6 sm:p-8 rounded-xl form-card">
            <h2 class="text-xl font-semibold mb-4 text-gray-700 border-b pb-2">3. Product Details</h2>
            <input type="hidden" name="products_data" id="productsDataInput">
            
            <div id="productsContainer" class="space-y-4 mb-6">
                <div class="product-row p-4 border border-gray-200 rounded-lg bg-gray-50 product-row-exit">
                    <div class="grid grid-cols-12 gap-3">
                        
                        {{-- CRITICAL CHANGE: BOQ Material Link (REQUIRED REMOVED) --}}
                        <div class="col-span-12 sm:col-span-4">
                            {{-- REMOVED <span class="text-red-500">*</span> --}}
                            <label for="boqMaterialId-0" class="block text-xs font-medium text-gray-600 mb-1">Internal Material Ref.</label>
                            {{-- REMOVED required --}}
                            <select id="boqMaterialId-0" name="boqMaterialId" 
                                     class="input-field w-full p-2 border rounded-md text-sm text-gray-900">
                                <option value="">-- Select BOQ Item (Optional) --</option>
                                {{-- @foreach loop populates the initial dropdown --}}
                                @isset($boqMaterials)
                                    @foreach ($boqMaterials as $material)
                                        <option value="{{ $material->id }}">
                                            {{ $material->item }} ({{ $material->specs }})
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>

                        {{-- Supplier Item Name --}}
                        <div class="col-span-12 sm:col-span-3">
                            <label for="item-0" class="block text-xs font-medium text-gray-600 mb-1">Supplier Item Name <span class="text-red-500">*</span></label>
                            <input type="text" id="item-0" name="item" required
                                     class="input-field w-full p-2 border rounded-md text-sm text-gray-900" placeholder="Product Name">
                        </div>

                        {{-- Item Code (Description) --}}
                        <div class="col-span-12 sm:col-span-2">
                            <label for="description-0" class="block text-xs font-medium text-gray-600 mb-1">Item Code</label>
                            <input type="text" id="description-0" name="description"
                                     class="input-field w-full p-2 border rounded-md text-sm text-gray-900" placeholder="SKU/Code">
                        </div>
                        
                        {{-- Unit Price --}}
                        <div class="col-span-6 sm:col-span-2">
                            <label for="unitPrice-0" class="block text-xs font-medium text-gray-600 mb-1">Unit Price <span class="text-red-500">*</span></label>
                            <input type="number" id="unitPrice-0" name="unitPrice" required step="0.01" min="0.01"
                                     class="input-field w-full p-2 border rounded-md text-sm text-gray-900" placeholder="0.00">
                        </div>
                        
                        {{-- Unit --}}
                        <div class="col-span-4 sm:col-span-1">
                            <label for="unit-0" class="block text-xs font-medium text-gray-600 mb-1">Unit</label>
                            <input type="text" id="unit-0" name="unit"
                                     class="input-field w-full p-2 border rounded-md text-sm text-gray-900" placeholder="pcs/kg/l">
                        </div>

                        {{-- Remove Button Placeholder/Area --}}
                        <div class="col-span-2 sm:col-span-12 sm:text-right flex items-center justify-end sm:justify-end mt-2">
                            <button type="button" onclick="removeProductRow(this)"
                                     class="hidden text-red-500 hover:text-red-700 text-sm font-medium p-1 rounded-md transition-colors">
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" id="addProductBtn"
                    class="mt-4 flex items-center px-4 py-2 text-sm font-medium rounded-full text-blue-700 bg-blue-100 hover:bg-blue-200 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Another Product
            </button>
        </section>

        <div class="pt-4">
            <button type="submit"
                    class="btn-primary w-full md:w-auto px-8 py-3 rounded-xl text-white font-semibold text-lg hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-blue-300">
                Save Supplier & Products
            </button>
        </div>
    </form>

@endsection

@push('scripts')
<script>
    // Global counter for product rows
    let productRowCounter = 0;
    const productsContainer = document.getElementById('productsContainer');
    const form = document.getElementById('procurementForm');
    const messageBox = document.getElementById('messageBox');
    const addProductBtn = document.getElementById('addProductBtn');
    const productsDataInput = document.getElementById('productsDataInput');
    
    // Cache the options HTML for the BOQ dropdown from the first row
    let boqOptions = '';

    /**
     * Shows a message in the notification box.
     */
    function showMessage(message, type) {
        messageBox.textContent = message;
        messageBox.className = `p-4 mb-6 rounded-lg font-medium text-white shadow-md ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
        messageBox.classList.remove('hidden');
        setTimeout(() => {
            messageBox.classList.add('hidden');
        }, 5000);
    }

    /**
     * Creates a new, dynamic product input row HTML string.
     */
    function createProductRowHTML() {
        productRowCounter++;
        // Use the cached options for new rows
        const optionsHtml = boqOptions || document.getElementById('boqMaterialId-0').innerHTML;

        return `
            <div class="product-row p-4 border border-gray-200 rounded-lg bg-gray-50 product-row-exit">
                <div class="grid grid-cols-12 gap-3">
                    
                    {{-- BOQ Material Link (REQUIRED REMOVED) --}}
                    <div class="col-span-12 sm:col-span-4">
                        {{-- REMOVED <span class="text-red-500">*</span> from label in dynamic script --}}
                        <label for="boqMaterialId-${productRowCounter}" class="block text-xs font-medium text-gray-600 mb-1">Internal Material Ref.</label>
                        {{-- REMOVED required --}}
                        <select id="boqMaterialId-${productRowCounter}" name="boqMaterialId" 
                                 class="input-field w-full p-2 border rounded-md text-sm text-gray-900">
                            ${optionsHtml}
                        </select>
                    </div>

                    {{-- Supplier Item Name --}}
                    <div class="col-span-12 sm:col-span-3">
                        <label for="item-${productRowCounter}" class="block text-xs font-medium text-gray-600 mb-1">Supplier Item Name <span class="text-red-500">*</span></label>
                        <input type="text" id="item-${productRowCounter}" name="item" required
                                 class="input-field w-full p-2 border rounded-md text-sm text-gray-900" placeholder="Product Name">
                    </div>

                    {{-- Item Code (Description) --}}
                    <div class="col-span-12 sm:col-span-2">
                        <label for="description-${productRowCounter}" class="block text-xs font-medium text-gray-600 mb-1">Item Code</label>
                        <input type="text" id="description-${productRowCounter}" name="description"
                                 class="input-field w-full p-2 border rounded-md text-sm text-gray-900" placeholder="SKU/Code">
                    </div>

                    {{-- Unit Price --}}
                    <div class="col-span-6 sm:col-span-2">
                        <label for="unitPrice-${productRowCounter}" class="block text-xs font-medium text-gray-600 mb-1">Unit Price <span class="text-red-500">*</span></label>
                        <input type="number" id="unitPrice-${productRowCounter}" name="unitPrice" required step="0.01" min="0.01"
                                 class="input-field w-full p-2 border rounded-md text-sm text-gray-900" placeholder="0.00">
                    </div>
                    
                    {{-- Unit --}}
                    <div class="col-span-4 sm:col-span-1">
                        <label for="unit-${productRowCounter}" class="block text-xs font-medium text-gray-600 mb-1">Unit</label>
                        <input type="text" id="unit-${productRowCounter}" name="unit" 
                                 class="input-field w-full p-2 border rounded-md text-sm text-gray-900" placeholder="pcs/kg/l">
                    </div>

                    {{-- Remove Button Placeholder/Area --}}
                    <div class="col-span-2 sm:col-span-12 sm:text-right flex items-center justify-end sm:justify-end mt-2">
                        <button type="button" onclick="removeProductRow(this)"
                                 class="text-red-500 hover:text-red-700 text-sm font-medium p-1 rounded-md transition-colors">
                            Remove
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    addProductBtn.addEventListener('click', () => {
        productsContainer.insertAdjacentHTML('beforeend', createProductRowHTML());
    });

    window.removeProductRow = function(button) {
        const row = button.closest('.product-row');
        if (row) {
            row.style.opacity = 0;
            row.style.maxHeight = '0px';
            row.style.paddingTop = '0px';
            row.style.paddingBottom = '0px';
            row.style.marginTop = '0px';
            row.style.marginBottom = '0px';
            setTimeout(() => { row.remove(); }, 500);
        }
    };

    /**
     * Client-side Validation and Data Collection (for Laravel submission)
     */
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        messageBox.classList.add('hidden'); 

        const products = [];
        const productRows = productsContainer.querySelectorAll('.product-row');
        let hasError = false;

        productRows.forEach((row, index) => {
            const boqIdInput = row.querySelector('[name="boqMaterialId"]'); // <-- NEW
            const itemInput = row.querySelector('[name="item"]');
            const priceInput = row.querySelector('[name="unitPrice"]');
            const descInput = row.querySelector('[name="description"]');
            const unitInput = row.querySelector('[name="unit"]'); 

            const item = itemInput.value.trim();
            const unitPrice = parseFloat(priceInput.value);

            // Reset error borders
            boqIdInput.classList.remove('border-red-500'); // <-- NEW
            itemInput.classList.remove('border-red-500');
            priceInput.classList.remove('border-red-500');

            // 1. Validate BOQ Material ID (OPTIONAL NOW - REMOVED REQUIRED CHECK)
            // if (!boqIdInput.value) {
            //     boqIdInput.classList.add('border-red-500');
            //     hasError = true;
            // }

            // 2. Validate product fields
            if (!item) {
                itemInput.classList.add('border-red-500');
                hasError = true;
            }
            if (isNaN(unitPrice) || unitPrice <= 0) {
                priceInput.classList.add('border-red-500');
                hasError = true;
            }

            // Collect product data (CRITICAL CHANGE)
            // If boqIdInput.value is empty string, parseInt converts it to NaN, which is not JSON-safe.
            // Using a ternary to ensure it's null or a number.
            const boqMaterialId = boqIdInput.value ? parseInt(boqIdInput.value) : null;

            products.push({
                boq_material_id: boqMaterialId, // <-- NOW NULL IF NOT SELECTED
                item: item,
                description: descInput.value.trim(),
                unit: unitInput ? unitInput.value.trim() : null,
                unit_price: unitPrice.toFixed(2)
            });
        });


        // Basic check for required supplier fields 
        const requiredSupplierFields = [
            'supplierName', 'location', 'address', 'contact'
        ];
        
        requiredSupplierFields.forEach(id => {
            const input = document.getElementById(id);
            if (input && !input.value.trim()) {
                input.classList.add('border-red-500');
                hasError = true;
            } else if (input) {
                input.classList.remove('border-red-500');
            }
        });


        if (hasError) {
            showMessage("Error: Please fill out all required fields marked with * and ensure product prices are positive numbers.", 'error');
            return;
        }
        if (products.length === 0) {
            showMessage("Error: At least one product is required for submission.", 'error');
            return;
        }

        // Serialize products data and submit
        productsDataInput.value = JSON.stringify(products);
        this.submit();
    });

    // Initialize: Store options and hide the initial remove button
    document.addEventListener('DOMContentLoaded', () => {
        const initialRow = productsContainer.querySelector('.product-row');
        if (initialRow) {
            // Cache the options HTML
            boqOptions = document.getElementById('boqMaterialId-0').innerHTML;
            
            // Hide the remove button on the mandatory first row
            const removeBtn = initialRow.querySelector('button');
            if (removeBtn) {
                 removeBtn.classList.add('hidden');
            }
        }
        
        @if ($errors->any() || session('error'))
            showMessage("Please correct the errors indicated below.", 'error');
        @endif
        @if (session('success'))
            showMessage("{{ session('success') }}", 'success');
        @endif
    });
</script>
@endpush