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
                Mobile: +254 700 929007<br>
                KRA PIN: P052110441E<br>
            </td>

            <td width="33%" align="center">
                <h2>SERVICE ORDER</h2>
            </td>

            <td width="33%" align="right">
                <strong>SO NO:</strong> {{ $serviceOrder->order_number }}<br>
                <strong>Date Issued:</strong> {{ $serviceOrder->order_date->format('d/m/Y') }}<br>
                <br>
                <strong>{{ $serviceOrder->supplier->name }}</strong><br>
                {{ $serviceOrder->supplier->location }}<br>
                {{ $serviceOrder->supplier->contact }}<br>
            </td>
        </tr>
    </table>

    <!-- TERMS -->
    <p style="
        background:#f5f5f5;
        border-left:4px solid #4f46e5;
        padding:8px;
        font-size:11px;
    ">
        Please provide the following service(s) subject to the terms and conditions stipulated.
    </p>

    <!-- SERVICE ITEMS -->
    <table>
        <thead style="background:#eee;">
            <tr>
                <th width="5%">S.No</th>
                <th width="65%">Service Description</th>
                <th width="30%">Amount (KES)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="center">1</td>
                <td>
                    {{ $serviceOrder->service_description }}
                </td>
                <td align="right">
                    <strong>{{ number_format($serviceOrder->total_amount, 2) }}</strong>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- TOTALS -->
    <table class="no-border">
        <tr>
            <td width="60%">
                <div class="section-title">Project Name</div>
                <em>{{ $serviceOrder->project_name ?? 'N/A' }}</em>
            </td>

            <td width="40%">

@php
    $showVat = request('include_vat', '1') == '1';
    $showWht = request('include_wht', '1') == '1';

    $netAmount = $serviceOrder->total_amount;

    // VAT (16%)
    $vatAmount = $showVat ? ($netAmount * 0.16) : 0;

    // Grand Total
    $grandTotal = $netAmount + $vatAmount;

    // Payable formula (same logic as PO)
    $payableAmount = ($showVat || $showWht)
        ? ($grandTotal * 1.14) / 1.16
        : $netAmount;

    // Withholding Tax (2%)
    $withholdingAmount = $showWht
        ? $grandTotal - $payableAmount
        : 0;
@endphp

<table>
    <tr>
        <td><strong>Net Amount:</strong></td>
        <td align="right"><strong>KSH {{ number_format($netAmount, 2) }}</strong></td>
    </tr>

    @if($showVat)
    <tr>
        <td>VAT (16%):</td>
        <td align="right" style="color:red;">
            <strong>KSH {{ number_format($vatAmount, 2) }}</strong>
        </td>
    </tr>
    @endif

    @if($showWht)
    <tr>
        <td>Withholding Tax (2%):</td>
        <td align="right" style="color:red;">
            <strong>KSH {{ number_format($withholdingAmount, 2) }}</strong>
        </td>
    </tr>
    @endif

    <tr>
        <td><strong>PAYABLE AMOUNT:</strong></td>
        <td align="right">
            <strong>KSH {{ number_format($payableAmount, 2) }}</strong>
        </td>
    </tr>

    <tr>
        <td><strong>GRAND TOTAL:</strong></td>
        <td align="right">
            <strong>KSH {{ number_format($grandTotal, 2) }}</strong>
        </td>
    </tr>
</table>

            </td>
        </tr>
    </table>

    <!-- SIGNATURES -->
    <br><br><br>
    <table class="no-border" align="center">
        <tr>
            <td align="center">
                <div class="signature-line"></div>
                Prepared By (Procurement)
            </td>

            <td align="center">
                <div class="signature-line"></div>
                Authorized By (Management)
            </td>
        </tr>
    </table>

</div>

</body>
</html>
