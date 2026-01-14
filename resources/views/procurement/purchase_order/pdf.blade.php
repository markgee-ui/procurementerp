<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 11px;
        padding: 20px;
    }
    .container {
        width: 100%;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }
    th, td {
        border: 1px solid #999;
        padding: 6px;
    }
    .no-border td, .no-border th {
        border: none !important;
    }
    .section-title {
        font-weight: bold;
        font-size: 12px;
        border-bottom: 1px solid #999;
        margin-bottom: 5px;
        padding-bottom: 3px;
    }
    .totals td {
        font-weight: bold;
    }
    .signature-line {
        border-bottom: 1px solid #000;
        width: 200px;
        margin-bottom: 5px;
        height: 20px;
    }
</style>
</head>

<body>

<div class="container">

    <!-- HEADER -->
    <table class="no-border">
        <tr>
            <td width="33%">
                <img src="{{ public_path('hms-logo.png') }}" alt="Logo" height="60"><br>
                <strong>Taison Group Limited</strong><br>
                Mombasa Rd, Plaza 2000<br>
                Nairobi, Kenya<br>
                Mobile: +254 700929007<br>
                KRA PIN: P052110441E<br>
            </td>

            <td width="33%" text-align="center">
                <h2>PURCHASE ORDER</h2>
            </td>

            <td width="33%" text-align="right">
                <strong>PO NO:</strong> {{ $purchaseOrder->order_number }}<br>
                <strong>Date Issued:</strong> {{ $purchaseOrder->order_date->format('d/m/Y') }}<br>
                @if($purchaseOrder->required_by_date)
                <strong>Required By:</strong> {{ $purchaseOrder->required_by_date->format('d/m/Y') }}<br>
                @endif
                <br>
                <strong>{{ $purchaseOrder->supplier->name }}</strong><br>
                {{ $purchaseOrder->supplier->address }}<br>
                {{ $purchaseOrder->supplier->contact }}<br>
                KRA PIN: {{ $purchaseOrder->supplier->kra_pin }}<br>
            </td>
        </tr>
    </table>

    <!-- TERMS -->
    <p style="
        background:#f5f5f5;
        border-left:4px solid red;
        padding:8px;
        font-size:11px;
    ">
        Please supply the following material(s) subject to the terms and conditions stipulated.
    </p>

    <!-- ITEMS -->
    <table>
        <thead style="background:#eee;">
            <tr>
                <th width="5%">S.No</th>
                <th width="35%">Item</th>
                <th width="10%">Unit</th>
                <th width="10%">Qty</th>
                <th width="10%">Price</th>
                <th width="10%">Disc (%)</th>
                <th width="20%">Amount (KSH)</th>
            </tr>
        </thead>
        <tbody>
            @php $subTotal = 0; @endphp
            @foreach ($purchaseOrder->items as $index => $item)
                @php
                    $subTotal += $item->line_total;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product->item }}</td>
                    <td>{{ $item->product->unit ?? 'N/A' }}</td>
                    <td text-align="right">{{ $item->quantity }}</td>
                    
                    <td text-align="right">{{ number_format($item->unit_price, 2) }}</td>
                    <td text-align="right">{{ number_format($item->discount, 1) }}%</td>
                    <td text-align="right"><strong>{{ number_format($item->line_total, 2) }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- TOTALS -->
<table class="no-border">
        <tr>
            <td width="60%">
                <div class="section-title">Project Name</div>
                <em>{{ $purchaseOrder->project_name ?? 'N/A' }}</em>
            </td>

           <td width="40%">
@php
    $showVat      = request('include_vat', '1') == '1';
    $showWht      = request('include_wht', '1') == '1';
    $showPayable  = request('show_payable', '1') == '1';

    $netAmount = $purchaseOrder->total_amount;

    // VAT = 16%
    $vatAmount = $showVat ? ($netAmount * 0.16) : 0;

    // Grand = Net + VAT
    $grandTotal = $netAmount + $vatAmount;

    // Default values
    $payableAmount = $netAmount;
    $withholdingAmount = 0;

    // PAYABLE using formula
    if ($showVat || $showWht) {
        $payableAmount = ($grandTotal * 1.14) / 1.16;
    }

    // WHT = Grand − Payable
    if ($showWht) {
        $withholdingAmount = $grandTotal - $payableAmount;
    }
@endphp



<table>
    <tr>
        <td><strong>Net Amount:</strong></td>
        <td text-align="right"><strong>KSH {{ number_format($netAmount, 2) }}</strong></td>
    </tr>

    {{-- VAT: show if selected --}}
    @if($showVat)
    <tr>
        <td>VAT (16%):</td>
        <td text-align="right" style="color:red;">
            <strong>KSH {{ number_format($vatAmount, 2) }}</strong>
        </td>
    </tr>
    @endif

    {{-- Withholding Tax: calculated from Grand Total --}}
    @if($showWht)
    <tr>
        <td>Withholding Tax (2%):</td>
        <td text-align="right" style="color:red;">
            <strong>KSH {{ number_format($withholdingAmount, 2) }}</strong>
        </td>
    </tr>
    @endif

    {{-- Payable amount --}}
    @if($showVat || $showWht || $showPayable)
    <tr>
        <td><strong>PAYABLE AMOUNT:</strong></td>
        <td text-align="right">
            <strong>KSH {{ number_format($payableAmount, 2) }}</strong>
        </td>
    </tr>
    @endif

    <tr>
        <td><strong>GRAND TOTAL:</strong></td>
        <td text-align="right"><strong>KSH {{ number_format($grandTotal, 2) }}</strong></td>
    </tr>
</table>

        </tr>
    </table>
    <!-- SIGNATURES -->
    <br><br><br>
    <table class="no-border" text-align="center">
        <tr>
            <td text-align="center">
                <div class="signature-line"></div>
                Prepared By (Procurement)
            </td>

            <td text-align="center">
                <div class="signature-line"></div>
                Authorized By (Management)
            </td>
        </tr>
    </table>

</div>

</body>
</html>
