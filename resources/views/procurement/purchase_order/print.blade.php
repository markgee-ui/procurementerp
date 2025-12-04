<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            table th, table td {
                padding: 6px 10px;
            }
        }

        .po-container {
            max-width: 8.5in;
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

<div class="po-container border border-gray-400 p-8">

    <!-- TOP SECTION: Company (Left) | PO TITLE (Center) | Supplier (Right) -->
    <div class="grid grid-cols-3 items-start mb-6 pb-2 border-b border-gray-400">

        <!-- Company Details -->
        <div class="text-left">
            <div class="mb-1">
                <img src="{{ asset('hms-logo.png') }}" alt="Logo" class="h-16 inline-block">
            </div>
            <h2 class="text-lg font-bold">Taison Group Limited</h2>
            <p class="text-sm">Mombasa Rd, Plaza 2000</p>
            <p class="text-sm">Nairobi, Kenya</p>
            <p class="text-sm">Mobile : +254 700929007</p>
            <p class="text-sm">KRA PIN : P052110441E</p>
        </div>

        <!-- CENTER: PURCHASE ORDER TITLE -->
        <div class="flex flex-col items-center justify-start pt-4">
            <h1 class="text-sm font-extrabold text-gray-800 inline-block px-4  py-1">PURCHASE ORDER</h1>
            <!-- <p class="text-xs font-semibold mt-2">PO NO: TGL{{ $purchaseOrder->order_number ?? $purchaseOrder->id }}</p>
            <p class="text-xs text-gray-600">Date Issued: {{ $purchaseOrder->order_date->format('d/m/Y') }}</p>
            @if ($purchaseOrder->required_by_date)
            <p class="text-xs text-gray-600">Required By: {{ $purchaseOrder->required_by_date->format('d/m/Y') }}</p>
            @endif -->
        </div>

        <!-- Supplier Details -->
        <div class="text-right">
            <p class="text-xs font-semibold mt-2">PO NO: {{ $purchaseOrder->order_number ?? $purchaseOrder->id }}</p>
            <p class="text-xs text-gray-600">Date Issued: {{ $purchaseOrder->order_date->format('d/m/Y') }}</p>
            @if ($purchaseOrder->required_by_date)
            <p class="text-xs text-gray-600">Required By: {{ $purchaseOrder->required_by_date->format('d/m/Y') }}</p>
            @endif
            <!-- <h3 class="text-sm font-bold uppercase tracking-wider text-gray-600 mb-1 border-b">Supplier (Bill To)</h3> -->
            <p class="text-1xl font-semibold text-gray-800 mt-4">{{ $purchaseOrder->supplier->name }}</p>
            <p class="text-sm">{{ $purchaseOrder->supplier->address }}</p>
            <p class="text-sm">{{ $purchaseOrder->supplier->contact }}</p>
            <p class="text-sm">{{ $purchaseOrder->supplier->kra_pin }}</p>
        </div>

    </div>

    <!-- TERMS MESSAGE -->
    <p class="text-xs font-semibold mb-4 p-2 bg-gray-50 border-l-4 border-red-500 text-gray-700">
        Please supply the following material(s) subject to the terms and conditions stipulated.
    </p>

    <!-- LINE ITEMS TABLE -->
    <div class="mb-10 border border-gray-400">
        <table class="min-w-full divide-y divide-gray-400">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-700  w-1/12 border-r border-gray-400">S.No</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-700  w-4/12 border-r border-gray-400">Item </th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-700  w-1/12 border-r border-gray-400">Unit</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-700  w-1/12 border-r border-gray-400">Qty</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-700  w-1/12 border-r border-gray-400">Price</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-700  w-1/12 border-r border-gray-400">Disc (%)</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-700  w-2/12">Amount (KSH)</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-300">
                @php $subTotal = 0; @endphp
                @foreach ($purchaseOrder->items as $index => $item)
                @php $subTotal += ($item->quantity * $item->unit_price); @endphp
                <tr>
                    <td class="px-3 py-2 text-sm text-gray-500 border-r border-gray-300">{{ $index + 1 }}</td>
                    <td class="px-3 py-2 text-sm text-gray-900 border-r border-gray-300">
                        {{ $item->product->item }}
                    </td>
                    <td class="px-3 py-2 text-sm text-gray-500 border-r border-gray-300">{{ $item->product->unit ?? 'N/A' }}</td>
                    <td class="px-3 py-2 text-sm text-right text-gray-700 border-r border-gray-300">{{ $item->quantity }}</td>
                    <td class="px-3 py-2 text-sm text-right text-gray-700 border-r border-gray-300">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="px-3 py-2 text-sm text-right text-gray-700 border-r border-gray-300">{{ number_format($item->discount, 1) }}%</td>
                    <td class="px-3 py-2 text-sm font-bold text-right text-gray-900">{{ number_format($item->line_total, 2) }}</td>
                </tr>
                @endforeach
                <tr><td colspan="7" class="h-4"></td></tr>
            </tbody>
        </table>
    </div>
@php
    // Toggle values from request (defaults to ON)
    $showVat      = request('include_vat', '1') == '1';
    $showWht      = request('include_wht', '1') == '1';
    $showPayable  = request('show_payable', '1') == '1';

    // Base Net amount
    $netAmount = $purchaseOrder->total_amount;

    // VAT calculation
    $vatAmount = $showVat ? ($netAmount * 0.16) : 0;

    // Grand Total (VAT inclusive)
    $grandTotal = $netAmount + $vatAmount;

    // Default values
    $payableAmount = $netAmount;
    $withholdingAmount = 0;

    // Apply Payable formula only if VAT or WHT enabled
    if ($showVat || $showWht) {
        // PAYABLE = Grand Total × 1.14 / 1.16
        $payableAmount = ($grandTotal * 1.14) / 1.16;
    }

    // WHT = 2% of Grand Total
    if ($showWht) {
        // EXACT rule: 2% of Grand Total
        $withholdingAmount = ($grandTotal * 1.14 / 1.16 - $grandTotal) * -1;
    }
@endphp



<div class="flex justify-between">
    <div class="w-1/2 pr-4">
        <h3 class="text-sm font-bold tracking-wider text-gray-600 mb-2 border-b">
            Project Name
        </h3>
        <p class="text-gray-700 italic text-sm">
            {{ $purchaseOrder->project_name ?? 'N/A' }}
        </p>
    </div>

    <div class="w-1/3">
        <div class="space-y-1 text-sm">

            {{-- NET AMOUNT --}}
            <div class="flex justify-between font-medium border-b pb-1">
                <span>NET AMOUNT:</span>
                <span class="font-bold">
                    KSH {{ number_format($netAmount, 2) }}
                </span>
            </div>

            {{-- VAT 16% --}}
            @if($showVat)
            <div class="flex justify-between font-medium text-red-600 mt-1">
                <span>VAT TAX (16%):</span>
                <span class="font-bold">
                    KSH {{ number_format($vatAmount, 2) }}
                </span>
            </div>
            @endif

            {{-- WHT 2% --}}
            @if($showWht)
            <div class="flex justify-between font-medium text-red-600">
                <span>Withholding Tax (2%):</span>
                <span class="font-bold">
                    KSH {{ number_format($withholdingAmount, 2) }}
                </span>
            </div>
            @endif

            {{-- PAYABLE AMOUNT --}}
            @if($showVat || $showWht || $showPayable)
            <div class="flex justify-between font-medium mt-2 pt-1 border-t border-gray-300">
                <span>PAYABLE AMOUNT:</span>
                <span class="font-bold">
                    KSH {{ number_format($payableAmount, 2) }}
                </span>
            </div>
            @endif

            {{-- GRAND TOTAL --}}
            <div class="flex justify-between font-medium border-t border-gray-700 pt-2 mt-2">
                <span>GRAND TOTAL:</span>
                <span class="font-bold">
                    KSH {{ number_format($grandTotal, 2) }}
                </span>
            </div>

        </div>
    </div>
</div>


    <!-- SIGNATURES -->
    <div class="mt-16 pt-6 border-t border-gray-400 flex justify-around text-center text-xs">
        <div>
            <span class="block border-b border-gray-600 w-40 mb-1"></span>
            <p>Prepared By (Procurement)</p>
        </div>
        <div>
            <span class="block border-b border-gray-600 w-40 mb-1"></span>
            <p>Authorized By (Management)</p>
        </div>
    </div>

    <!-- PRINT NOTE -->
    <div class="no-print mt-12 text-center text-sm text-gray-500">
        <p>Use Ctrl+P or Cmd+P to print this document.</p>
        <button onclick="window.close()" class="mt-4 p-2 border rounded-md hover:bg-gray-100">Close Print View</button>
    </div>

</div>

<script>
    window.onload = function() {
        window.print();
    }
</script>

</body>
</html>