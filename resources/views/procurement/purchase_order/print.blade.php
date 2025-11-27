<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order #{{ $purchaseOrder->order_number ?? $purchaseOrder->id }} - Print</title>
    
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <style>
        /* CSS to ensure clean printing */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
        }
        .po-container {
            max-width: 8.5in; /* Standard paper width */
            margin: 0 auto;
            padding: 0.5in;
            font-family: Arial, sans-serif;
            font-size: 10pt;
        }
        table {
            border-collapse: collapse;
        }
    </style>
</head>
<body>

<div class="po-container">

    {{-- Company Logo and Header Section --}}
    <div class="flex justify-between items-start mb-10 border-b pb-4">
        
        {{-- Company Details (Right side of the PO header) --}}
        <div class="text-right">
            {{-- Company Logo Placeholder --}}
            <div class="mb-2">
    <img src="{{ asset('hms-logo.png') }}" alt="Taison Group Logo" class="h-10 inline-block">
</div>
            <h2 class="text-lg font-bold">Taison Group Limited</h2>
            <p class="text-sm">Mombasa Rd, Plaza 2000</p>
            <p class="text-sm">Nairobi, Kenya</p>
            <p class="text-sm">Email: procurement@taisongroup.co.ke</p>
        </div>

        {{-- PO Title and Dates (Left side of the PO header) --}}
        <div class="text-left">
            <h1 class="text-3xl font-extrabold text-gray-800">PURCHASE ORDER</h1>
            <p class="text-xl text-indigo-700 mt-1">PO NO: TGL{{ $purchaseOrder->order_number ?? $purchaseOrder->id }}</p>
            {{-- Date format changed to DD-Mon-YYYY --}}
            <p class="text-sm text-gray-600 mt-1">Date Issued: {{ $purchaseOrder->order_date->format('d-M-Y') }}</p>
        </div>
    </div>

    {{-- Supplier & Shipping Details --}}
    <div class="flex justify-between mb-8">
        {{-- Supplier Details (Left) --}}
        <div class="w-1/2 pr-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-600 mb-2 border-b">Supplier </h3>
            <p class="text-lg font-semibold text-gray-800">{{ $purchaseOrder->supplier->name }}</p>
            <p>{{ $purchaseOrder->supplier->address }}</p>
            <p>Contact: {{ $purchaseOrder->supplier->phone ?? $purchaseOrder->supplier->email }}</p>
        </div>
        
        {{-- Order Status and Required Date (Right) --}}
        <div class="w-1/2 pl-4 text-right">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-600 mb-2 border-b">Order Status / Delivery Date</h3>
            <span class="text-lg font-bold @if($purchaseOrder->status == 'Draft') text-yellow-600 @else text-green-600 @endif">
                {{ strtoupper($purchaseOrder->status) }}
            </span>
            @if ($purchaseOrder->required_by_date)
            <p class="text-sm mt-2">Required By: <span class="font-semibold">{{ $purchaseOrder->required_by_date->format('d-M-Y') }}</span></p>
            @endif
        </div>
    </div>
    
    {{-- Tagline/Terms Statement --}}
    <p class="text-sm font-semibold italic mb-4 p-2 bg-gray-50 border-l-4 border-indigo-500 text-gray-700">
        Please supply the following materials subject to the terms and conditions outlined in our standard Purchase Agreement.
    </p>

    {{-- Line Items Table --}}
    <div class="mb-10 shadow overflow-hidden border border-gray-200 sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-1/12">S.No</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-4/12">Item / Description</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-1/12">Unit</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-1/12">Qty</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-1/12">Price</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-1/12">Disc (%)</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-2/12">Line Total (KSH)</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php
                    $subTotal = 0;
                @endphp
                @foreach ($purchaseOrder->items as $index => $item)
                @php
                    $subTotal += ($item->quantity * $item->unit_price);
                @endphp
                <tr>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                    <td class="px-3 py-2 text-sm text-gray-900">
                        {{ $item->product->item }}
                        <div class="text-xs text-gray-500">{{ $item->product->description }}</div>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ $item->product->unit ?? 'N/A' }}</td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-right text-gray-700">{{ $item->quantity }}</td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-right text-gray-700">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-right text-gray-700">{{ number_format($item->discount, 1) }}%</td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm font-bold text-right text-gray-900">{{ number_format($item->line_total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    @php
        // Calculate the taxable amount based on line totals (VAT is 16%)
        // Assuming the $purchaseOrder->total_amount is the Net Amount (Subtotal - Discount)
        $netAmount = $purchaseOrder->total_amount;
        $vatRate = 0.16;
        $vatAmount = $netAmount * $vatRate;
        $grandTotal = $netAmount + $vatAmount;
    @endphp

    {{-- Totals and Notes Footer --}}
    <div class="flex justify-between">
        <div class="w-1/2 pr-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-600 mb-2 border-b">Notes</h3>
            <p class="text-gray-700 italic text-sm">{{ $purchaseOrder->notes ?? 'N/A' }}</p>
        </div>
        <div class="w-1/3">
            <div class="space-y-2">
                
                {{-- Net Total (Line Totals Sum) --}}
                <div class="flex justify-between font-medium border-b pb-1">
                    <span>NET AMOUNT:</span>
                    <span>{{ number_format($netAmount, 2) }} KSH</span>
                </div>
                
                {{-- VAT Tax (16%) --}}
                <div class="flex justify-between font-medium">
                    <span>VAT TAX (16%):</span>
                    <span class="text-red-600">{{ number_format($vatAmount, 2) }} KSH</span>
                </div>

                {{-- Grand Total --}}
                <div class="flex justify-between font-bold border-t pt-2">
                    <span class="text-lg font-bold">FINAL GRAND TOTAL:</span>
                    <span class="text-lg font-bold text-indigo-700">{{ number_format($grandTotal, 2) }} KSH</span>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Authorization Signatures (Good practice for POs) --}}
    <div class="mt-16 pt-6 border-t border-gray-300 flex justify-around text-center text-xs">
        <div>
            <span class="block border-b w-40 mb-1"></span>
            <p>Prepared By (Procurement)</p>
        </div>
        <div>
            <span class="block border-b w-40 mb-1"></span>
            <p>Authorized By (Management)</p>
        </div>
    </div>

    {{-- Instructions for printing --}}
    <div class="no-print mt-12 text-center text-sm text-gray-500">
        <p>This is a dedicated print view. Use your browser's print dialogue (Ctrl+P or Cmd+P) to save or print this document.</p>
        <button onclick="window.close()" class="mt-4 p-2 border rounded-md hover:bg-gray-100">Close Print View</button>
    </div>

</div>

<script>
    // Automatically trigger the print dialog when the page loads
    window.onload = function() {
        window.print();
    }
</script>

</body>
</html>