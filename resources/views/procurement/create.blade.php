@extends('layouts.app')

@section('title', 'Add New Procurement Entry')

@push('styles')
<style>
    /* These styles are now page-specific */
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
        <p class="text-gray-600 mt-1">Input essential supplier and product details for the ERP system.</p>
    </header>

    <!-- Notification/Message Box -->
    <div id="messageBox" class="hidden p-4 mb-6 rounded-lg font-medium text-white bg-red-500 shadow-md">
        <!-- Messages will be inserted here -->
    </div>

    <form id="procurementForm" action="{{ route('procurement.store') }}" method="POST" class="space-y-6">
        @csrf 

        <!-- SUPPLIER DETAILS SECTION -->
        <section class="bg-white p-6 sm:p-8 rounded-xl form-card">
            <h2 class="text-xl font-semibold mb-4 text-gray-700 border-b pb-2">1. Supplier Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div class="col-span-1">
                    <label for="supplierName" class="block text-sm font-medium text-gray-700 mb-1">Supplier Name <span class="text-red-500">*</span></label>
                    <input type="text" id="supplierName" name="supplier_name" required
                           value="{{ old('supplier_name') }}"
                           class="input-field w-full p-3 border rounded-lg text-gray-900 @error('supplier_name') border-red-500 @enderror"
                           placeholder="e.g., Simba">
                    @error('supplier_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror 
                </div>

                <div class="col-span-1">
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location (City/Country) <span class="text-red-500">*</span></label>
                    <input type="text" id="location" name="location" required
                           value="{{ old('location') }}"
                           class="input-field w-full p-3 border rounded-lg text-gray-900 @error('location') border-red-500 @enderror"
                           placeholder="e.g., Nairobi, Kenya">
                    @error('location') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror 
                </div>

                <div class="col-span-full">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
                    <input type="text" id="address" name="address" required
                           value="{{ old('address') }}"
                           class="input-field w-full p-3 border rounded-lg text-gray-900 @error('address') border-red-500 @enderror"
                           placeholder="e.g., P.O. Box 456">
                    @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="col-span-full">
                    <label for="contact" class="block text-sm font-medium text-gray-700 mb-1">Phone or Email <span class="text-red-500">*</span></label>
                    <input type="text" id="contact" name="contact" required
                           value="{{ old('contact') }}"
                           class="input-field w-full p-3 border rounded-lg text-gray-900 @error('contact') border-red-500 @enderror"
                           placeholder="e.g., +254712345678 ">
                    @error('contact') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <!-- PRODUCT DETAILS SECTION (DYNAMIC) -->
        <section class="bg-white p-6 sm:p-8 rounded-xl form-card">
            <h2 class="text-xl font-semibold mb-4 text-gray-700 border-b pb-2">2. Product Details</h2>
            <input type="hidden" name="products_data" id="productsDataInput">
            <div id="productsContainer" class="space-y-4 mb-6">
                <!-- Initial product row -->
                <div class="product-row p-4 border border-gray-200 rounded-lg bg-gray-50 product-row-exit">
                    <div class="grid grid-cols-6 gap-3">
                        <div class="col-span-6 sm:col-span-2">
                            <label for="item-0" class="block text-xs font-medium text-gray-600 mb-1">Item <span class="text-red-500">*</span></label>
                            <input type="text" id="item-0" name="item" required
                                   class="input-field w-full p-2 border rounded-md text-sm text-gray-900" placeholder="Product Name">
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label for="description-0" class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                            <input type="text" id="description-0" name="description"
                                   class="input-field w-full p-2 border rounded-md text-sm text-gray-900" placeholder="Specifications/Model">
                        </div>
                        <div class="col-span-4 sm:col-span-1">
                            <label for="unitPrice-0" class="block text-xs font-medium text-gray-600 mb-1">Unit Price <span class="text-red-500">*</span></label>
                            <input type="number" id="unitPrice-0" name="unitPrice" required step="0.01" min="0.01"
                                   class="input-field w-full p-2 border rounded-md text-sm text-gray-900" placeholder="0.00">
                        </div>
                        <div class="col-span-2 sm:col-span-6 sm:text-right flex items-center justify-end sm:justify-end">
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

        <!-- SUBMIT BUTTON -->
        <div class="pt-4">
            <button type="submit"
                    class="btn-primary w-full md:w-auto px-8 py-3 rounded-xl text-white font-semibold text-lg hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-blue-300">
                Save Procurement Data
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
        return `
            <div class="product-row p-4 border border-gray-200 rounded-lg bg-gray-50 product-row-exit">
                <div class="grid grid-cols-6 gap-3">
                    <div class="col-span-6 sm:col-span-2">
                        <label for="item-${productRowCounter}" class="block text-xs font-medium text-gray-600 mb-1">Item <span class="text-red-500">*</span></label>
                        <input type="text" id="item-${productRowCounter}" name="item" required
                               class="input-field w-full p-2 border rounded-md text-sm text-gray-900" placeholder="Product Name">
                    </div>
                    <div class="col-span-6 sm:col-span-3">
                        <label for="description-${productRowCounter}" class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                        <input type="text" id="description-${productRowCounter}" name="description"
                               class="input-field w-full p-2 border rounded-md text-sm text-gray-900" placeholder="Specifications/Model">
                    </div>
                    <div class="col-span-4 sm:col-span-1">
                        <label for="unitPrice-${productRowCounter}" class="block text-xs font-medium text-gray-600 mb-1">Unit Price <span class="text-red-500">*</span></label>
                        <input type="number" id="unitPrice-${productRowCounter}" name="unitPrice" required step="0.01" min="0.01"
                               class="input-field w-full p-2 border rounded-md text-sm text-gray-900" placeholder="0.00">
                    </div>
                    <div class="col-span-2 sm:col-span-6 sm:text-right flex items-center justify-end sm:justify-end">
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
            const itemInput = row.querySelector('[name="item"]');
            const priceInput = row.querySelector('[name="unitPrice"]');
            const descInput = row.querySelector('[name="description"]');
            const item = itemInput.value.trim();
            const unitPrice = parseFloat(priceInput.value);

            itemInput.classList.remove('border-red-500');
            priceInput.classList.remove('border-red-500');

            if (!item) {
                itemInput.classList.add('border-red-500');
                hasError = true;
            }
            if (isNaN(unitPrice) || unitPrice <= 0) {
                priceInput.classList.add('border-red-500');
                hasError = true;
            }

            products.push({
                item: item,
                description: descInput.value.trim(),
                unit_price: unitPrice.toFixed(2)
            });
        });

        if (hasError) {
            showMessage("Error: Please fill out all required fields and ensure Unit Price is a positive number for all products.", 'error');
            return;
        }
        if (products.length === 0) {
             showMessage("Error: At least one product is required for submission.", 'error');
             return;
        }

        productsDataInput.value = JSON.stringify(products);
        this.submit();
    });

    // Initialize: Ensure the initial row's remove button is hidden
    document.addEventListener('DOMContentLoaded', () => {
        const initialRow = productsContainer.querySelector('.product-row');
        if (initialRow) {
            initialRow.querySelector('button').classList.add('hidden');
        }
    });
</script>
@endpush