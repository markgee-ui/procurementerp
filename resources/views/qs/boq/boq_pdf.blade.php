<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Bill of Quantities - {{ $boq->project_name }} (Net Costs)</title>
<style>
    /* Global Excel-Like Reset */
    body {
        font-family: Arial, sans-serif;
        font-size: 10px; /* Smaller font size for high data density */
        padding: 10px;
    }
    .container {
        width: 100%;
    }
    
    /* Core Table Styling (The Grid) */
    table {
        width: 100%;
        border-collapse: collapse; /* Essential for grid lines */
        margin-bottom: 5px;
        table-layout: fixed; /* Helps maintain column width consistency */
    }
    th, td {
        border: 1px solid #000; /* Solid black/dark border for grid lines */
        padding: 4px 6px; /* Reduced padding */
        text-align: left; 
        line-height: 1.2;
    }
    
    /* Remove Borders for Header/Footer Sections */
    .no-border td, .no-border th {
        border: none !important;
        padding: 2px 0; /* Tighten up non-table spacing */
    }

    /* Section Headers (Shaded Rows like merged Excel cells) */
    .section-header {
        font-weight: bold;
        background: #D9D9D9; /* Light gray fill */
        border: 1px solid #000;
        font-size: 11px;
    }
    .activity-header th {
        background: #F0F0F0; /* Very light gray for column headers */
        font-size: 10px;
        font-weight: bold;
        text-transform: uppercase;
    }
    
    /* Alignment Classes */
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .text-bold { font-weight: bold; }
    
    /* Signature Line */
    .signature-line {
        border-bottom: 1px solid #000;
        width: 150px;
        margin-bottom: 3px;
        height: 15px;
    }
</style>
</head>

<body>

<div class="container">

    <table class="no-border">
        <tr>
            <td width="33%" style="font-size: 9px;">
                <strong>Taison Group Limited</strong><br>
                Mombasa Rd, Plaza 2000<br>
                Nairobi, Kenya<br>
                Mobile: +254 700929007<br>
                KRA PIN: P052110441E<br>
            </td>

            <td width="34%" class="text-center" style="padding-top: 10px;">
                <h2 style="font-size: 14px; margin: 0;">BILL OF QUANTITIES (NET COST)</h2>
            </td>

            <td width="33%" class="text-right" style="font-size: 9px;">
                <strong>Project:</strong> {{ $boq->project_name }}<br>
                <strong>Date:</strong> {{ $boq->created_at->format('d/m/Y') }}<br>
                <br>
            </td>
        </tr>
    </table>

    <div style="background:#FFFFE0; border: 1px solid #FFD700; padding: 4px; font-size: 9px; margin-bottom: 10px;">
        **Note:** Detailed breakdown of materials, quantities, and **net costs**. Excludes VAT.
    </div>

    @php
        $totalNetCost = 0;
    @endphp

    @forelse ($boq->activities as $activity)
        <table style="margin-top: 5px;">
            {{-- ACTIVITY HEADER (Merged Cell Look) --}}
            <thead>
                <tr>
                    <th colspan="7" class="section-header">
                        Activity Section: {{ $activity->name }}
                        <span style="float:right;">Activity Budget: KSH {{ number_format($activity->budget, 2) }}</span>
                    </th>
                </tr>
            </thead>
            {{-- ITEM COLUMN HEADERS --}}
            <thead>
                <tr class="activity-header">
                    <th width="3%" class="text-center">#</th>
                    <th width="30%">Item / Description</th>
                    <th width="20%">Specs</th>
                    <th width="8%" class="text-center">Unit</th>
                    <th width="10%" class="text-right">Qty</th>
                    <th width="14%" class="text-right">Rate (KSH)</th>
                    <th width="15%" class="text-right">Total Cost (KSH)</th>
                </tr>
            </thead>
            <tbody>
                @php $activityTotal = 0; @endphp
                @forelse ($activity->materials as $index => $material)
                    @php
                        $lineTotal = $material->qty * $material->rate;
                        $activityTotal += $lineTotal;
                        $totalNetCost += $lineTotal;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $material->item }}</td>
                        <td>{{ $material->specs }}</td>
                        <td class="text-center">{{ $material->unit }}</td>
                        <td class="text-right">{{ number_format($material->qty, 2) }}</td>
                        <td class="text-right">{{ number_format($material->rate, 2) }}</td>
                        <td class="text-right text-bold">{{ number_format($lineTotal, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No materials listed for this activity.</td>
                    </tr>
                @endforelse
            </tbody>
            {{-- ACTIVITY SUBTOTAL ROW (Shaded Row) --}}
            <tfoot>
                <tr>
                    <td colspan="6" class="text-right section-header">Activity Sub-Total:</td>
                    <td class="text-right text-bold section-header">KSH {{ number_format($activityTotal, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    @empty
        <p class="text-center">No activities found for this Bill of Quantities.</p>
    @endforelse

    <table style="width: 40%; margin-left: auto; margin-top: 15px;">
        <tr>
            <td width="60%" class="text-bold section-header">GRAND TOTAL (NET COST):</td>
            <td width="40%" class="text-right text-bold section-header">KSH {{ number_format($totalNetCost, 2) }}</td>
        </tr>
    </table>

    <table class="no-border" style="margin-top: 50px;">
        <tr>
            <td width="50%" class="text-center" style="font-size: 9px;">
                <div class="signature-line"></div>
                Prepared By (Quantity Surveyor)
            </td>

            <td width="50%" class="text-center" style="font-size: 9px;">
                <div class="signature-line"></div>
                Acknowledged By (Client/Management)
            </td>
        </tr>
    </table>

</div>

</body>
</html>