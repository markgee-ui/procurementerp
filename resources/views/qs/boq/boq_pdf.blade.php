<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Bill of Quantities - {{ $boq->project_name }} (Net Costs)</title>
<style>
    /* Global Excel-Like Reset */
    body {
        font-family: Arial, sans-serif;
        font-size: 10px;
        padding: 10px;
    }
    .container {
        width: 100%;
    }
    
    /* Core Table Styling */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 5px;
        table-layout: fixed;
    }
    th, td {
        border: 1px solid #000;
        padding: 4px 6px;
        text-align: left; 
        line-height: 1.2;
    }
    
    /* Remove Borders for Header/Footer Sections */
    .no-border td, .no-border th {
        border: none !important;
        padding: 2px 0;
    }

    /* Section Headers */
    .section-header {
        font-weight: bold;
        background: #D9D9D9;
        border: 1px solid #000;
        font-size: 11px;
    }
    .activity-header th {
        background: #F0F0F0;
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
        margin-left: auto;
        margin-right: auto;
    }
</style>
</head>

<body>

<div class="container">

    <table class="no-border">
        <tr>
            <td width="35%" style="vertical-align: top;">
                <img src="{{ public_path('hms-logo.png') }}" alt="Logo" height="50" style="margin-bottom: 5px;"><br>
                <div style="font-size: 9px; line-height: 1.4;">
                    <strong>Taison Group Limited</strong><br>
                    Mombasa Rd, Plaza 2000<br>
                    Nairobi, Kenya<br>
                    Mobile: +254 700929007<br>
                    KRA PIN: P052110441E
                </div>
            </td>

            <td width="30%" class="text-center" style="vertical-align: middle;">
                <h2 style="font-size: 14px; margin: 0; text-decoration: underline;">BILL OF QUANTITIES</h2>
                <p style="font-size: 10px; margin: 5px 0 0 0;">(NET COST BASIS)</p>
            </td>

            <td width="35%" class="text-right" style="vertical-align: top; font-size: 9px; line-height: 1.4;">
                <div style="margin-top: 10px;">
                    <strong>Project:</strong> {{ $boq->project_name }}<br>
                    <strong>Ref No:</strong> BOQ-{{ str_pad($boq->id, 4, '0', STR_PAD_LEFT) }}<br>
                    <strong>Date:</strong> {{ $boq->created_at->format('d/m/Y') }}<br>
                    <strong>Status:</strong> FINAL
                </div>
            </td>
        </tr>
    </table>

    <div style="background:#FFFFE0; border: 1px solid #FFD700; padding: 6px; font-size: 9px; margin-bottom: 10px; border-left: 4px solid #FFD700;">
        <strong>Important Note:</strong> This document represents a detailed breakdown of materials, quantities, and <strong>net market costs</strong>. These figures exclude VAT and overhead markups unless otherwise specified.
    </div>

    @php
        $totalNetCost = 0;
    @endphp

    @forelse ($boq->activities as $activity)
        <table style="margin-top: 8px;">
            <thead>
                <tr>
                    <th colspan="7" class="section-header">
                        Activity Section: {{ $activity->name }}
                        <span style="float:right;">Activity Budget: KSH {{ number_format($activity->budget, 2) }}</span>
                    </th>
                </tr>
                <tr class="activity-header">
                    <th width="4%" class="text-center">#</th>
                    <th width="31%">Item / Description</th>
                    <th width="19%">Specifications</th>
                    <th width="8%" class="text-center">Unit</th>
                    <th width="10%" class="text-right">Qty</th>
                    <th width="13%" class="text-right">Rate (KSH)</th>
                    <th width="15%" class="text-right">Total (KSH)</th>
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

    <div style="margin-top: 30px;">
        <h3 style="font-size: 11px; text-decoration: underline; margin-bottom: 5px;">SUMMARY OF ACTIVITIES</h3>
        <table>
            <thead>
                <tr class="activity-header">
                    <th width="10%" class="text-center">Ref</th>
                    <th width="65%">Activity Description</th>
                    <th width="25%" class="text-right">Amount (KSH)</th>
                </tr>
            </thead>
            <tbody>
                @php $summaryTotal = 0; @endphp
                @foreach ($boq->activities as $index => $activity)
                    @php 
                        // Calculate total for this specific activity
                        $actTotal = $activity->materials->sum(function($m) {
                            return $m->qty * $m->rate;
                        });
                        $summaryTotal += $actTotal;
                    @endphp
                    <tr>
                        <td class="text-center">{{ chr(65 + $index) }}</td> {{-- Labels sections as A, B, C... --}}
                        <td>{{ $activity->name }}</td>
                        <td class="text-right">{{ number_format($actTotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="section-header">
                    <td colspan="2" class="text-right">NET PROJECT TOTAL:</td>
                    <td class="text-right" style="font-size: 12px;">KSH {{ number_format($summaryTotal, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <table style="width: 50%; margin-left: auto; margin-top: 10px; border: 2px solid #000;">
        <tr>
            <td width="55%" class="text-bold text-center" style="background: #333; color: #fff; padding: 10px;">
                FINAL BOQ TOTAL (EXCL. VAT):
            </td>
            <td width="45%" class="text-right text-bold" style="background: #fff; font-size: 13px; padding: 10px;">
                KSH {{ number_format($summaryTotal, 2) }}
            </td>
        </tr>
    </table>

    <table class="no-border" style="margin-top: 60px;">
        <tr>
            <td width="50%" class="text-center" style="font-size: 9px;">
                <div class="signature-line"></div>
                <strong>{{ Auth::user()->name ?? '____________________' }}</strong><br>
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
