@extends('layouts.app') 

@section('content')
<style>
    /* ======================================
    EXCEL-STYLE PRINT OPTIMIZATION 
    ====================================== */
    @media print {
        /* 1. HIDE NON-ESSENTIAL ELEMENTS (Buttons, Nav, Shadows) */
        header, footer, nav, .print\:hidden, .no-print {
            display: none !important;
        }

        /* 2. PAGE STRUCTURE - Remove all box styling */
        .p-6 {
            padding: 0 !important;
        }
        .bg-white, .shadow-xl, .rounded-lg, .rounded-md {
            background: none !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }
        body {
            color: #000;
            font-family: Arial, sans-serif; /* Excel uses system fonts */
            font-size: 10pt; /* Slightly smaller for compactness */
        }
        
        /* 3. TABLE STYLING - Create the Excel Grid Look */
        table {
            border-collapse: collapse !important;
            page-break-inside: auto !important;
            width: 100% !important;
        }
        /* Apply thin border to ALL table elements */
        table, th, td {
            border: 1px solid #000 !important; /* Solid black border for Excel look */
        }
        tr {
            page-break-inside: avoid !important;
            page-break-after: auto !important;
        }
        thead {
            display: table-header-group !important; /* Repeat table headers on new pages */
        }
        
        /* 4. HEADER/SECTION STYLING - Simple and Flat */
        h1, h2, h3, h4 {
            font-size: 14pt !important;
            margin-top: 5px;
            margin-bottom: 5px;
            padding-bottom: 0;
            border-bottom: none;
        }
        /* Style Project Header like a merged Excel cell */
        .project-header-cell {
            border: 1px solid #000 !important;
            padding: 8px;
            background-color: #f5f5f5 !important;
        }
        /* Style Activity Header like a gray header row */
        .activity-header-row {
            background-color: #E0E0E0 !important; /* Light gray fill */
        }
    }
</style>

<div class="p-6 bg-white rounded-lg shadow-xl">
    
    {{-- ACTION BUTTONS (Hidden on Print) --}}
    <div class="flex justify-between items-center mb-6 border-b pb-2 print:hidden">
        <h1 class="text-3xl font-bold text-gray-800">
            BoQ Details: {{ $boq->project_name }} 
        </h1>
        <div class="space-x-2">
            {{-- Download as PDF Button --}}
            <a href="{{ route('qs.boq.download', $boq) }}" 
               class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                <i class="fas fa-file-pdf mr-1"></i> Download PDF
            </a>
            
            {{-- Print Button (Uses window.print()) --}}
            <button onclick="window.print()"
                    class="px-4 py-2 text-sm font-medium text-white bg-gray-600 rounded-md hover:bg-gray-700">
                <i class="fas fa-print mr-1"></i> Print
            </button>
            
            {{-- Edit and Back Buttons --}}
            <a href="{{ route('qs.boq.edit', $boq) }}" 
               class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                Edit BoQ
            </a>
            <a href="{{ route('qs.boq.index') }}" 
               class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                Back to List
            </a>
        </div>
    </div>

    <h1 class="text-xl font-bold text-gray-800 mb-4 print:text-center print:mt-4 print:mb-4">
        BILL OF QUANTITIES (NET COST): {{ strtoupper($boq->project_name) }}
    </h1>

    {{-- Project Header Details - Using a table structure for Excel-like layout --}}
    <table class="w-full mb-8">
        <thead>
             <tr class="bg-gray-200">
                <th class="px-3 py-2 text-sm font-bold text-gray-700" colspan="2">PROJECT SUMMARY</th>
             </tr>
        </thead>
        <tbody>
            <tr>
                <td class="px-3 py-2 font-medium w-1/4">Project Name:</td>
                <td class="px-3 py-2 font-semibold">{{ $boq->project_name }}</td>
            </tr>
            <tr>
                <td class="px-3 py-2 font-medium w-1/4">Total Budget (Estimate):</td>
                <td class="px-3 py-2 font-semibold text-green-700">KSH {{ number_format($boq->project_budget, 2) }}</td>
            </tr>
            <tr>
                <td class="px-3 py-2 font-medium w-1/4">Date Generated:</td>
                <td class="px-3 py-2">{{ $boq->created_at->format('Y-m-d H:i') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="space-y-4"> {{-- Reduced spacing --}}
        @php $totalNetCost = 0; @endphp

        @forelse($boq->activities as $activity)
        <div class="activity-section border border-gray-400"> {{-- Excel look: single large border container --}}
            
            {{-- Activity Header - Styled like a merged cell/row --}}
            <div class="activity-header-row p-2 font-bold text-gray-800 flex justify-between print:block">
                <span>ACTIVITY SECTION: {{ strtoupper($activity->name) }}</span>
                <span class="ml-auto print:hidden">SECTION BUDGET: KSH {{ number_format($activity->budget, 2) }}</span>
            </div>

            {{-- Materials Table --}}
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100"> {{-- Lighter header for items --}}
                        <tr>
                            <th class="px-3 py-2 text-center text-xs font-bold text-gray-700 uppercase tracking-wider w-[3%]">#</th>
                            <th class="px-3 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-[25%]">Item</th>
                            <th class="px-6 py-2 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-[25%]">Specifications</th>
                            <th class="px-3 py-2 text-center text-xs font-bold text-gray-700 uppercase tracking-wider w-[8%]">Unit</th>
                            <th class="px-3 py-2 text-right text-xs font-bold text-gray-700 uppercase tracking-wider w-[10%]">Qty</th>
                            <th class="px-3 py-2 text-right text-xs font-bold text-gray-700 uppercase tracking-wider w-[17%]">Rate (KSH)</th> 
                            <th class="px-3 py-2 text-right text-xs font-bold text-gray-700 uppercase tracking-wider w-[12%]">Total Cost</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @php $activityTotal = 0; @endphp
                        @foreach ($activity->materials as $index => $material)
                        @php
                            $lineTotal = $material->qty * $material->rate;
                            $activityTotal += $lineTotal;
                            $totalNetCost += $lineTotal;
                        @endphp
                        <tr>
                            <td class="px-3 py-1 text-center text-sm">{{ $index + 1 }}</td>
                            <td class="px-3 py-1 text-sm">{{ $material->item }}</td>
                            <td class="px-6 py-1 text-sm">{{ $material->specs }}</td>
                            <td class="px-3 py-1 text-center text-sm">{{ $material->unit }}</td>
                            <td class="px-3 py-1 text-right text-sm">{{ number_format($material->qty) }}</td>
                            <td class="px-3 py-1 text-right text-sm">{{ number_format($material->rate, 2) }}</td>
                            <td class="px-3 py-1 text-right text-sm font-semibold">{{ number_format($lineTotal, 2) }}</td>
                        </tr>
                        @endforeach
                        {{-- Activity Sub-Total Row --}}
                        <tr class="bg-gray-200 font-bold"> {{-- Darker background for totals --}}
                            <td colspan="6" class="px-6 py-1 text-right text-sm">Activity Sub-Total:</td>
                            <td class="px-3 py-1 text-right text-sm text-gray-800">KSH {{ number_format($activityTotal, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @empty
        <p class="text-center text-gray-500">No activities found for this Bill of Quantities.</p>
        @endforelse
    </div>

    {{-- Final Summary Totals - ONLY NET COST REMAINS --}}
    <div class="mt-8 border border-gray-400 max-w-sm ml-auto">
        <table class="w-full">
            <tbody class="text-sm">
                {{-- Only the Grand Total (Net Cost) row remains --}}
                <tr class="bg-gray-200 font-bold">
                    <td class="px-3 py-2 w-1/2">GRAND TOTAL (NET COST):</td>
                    <td class="px-3 py-2 text-green-700 text-right w-1/2">KSH {{ number_format($totalNetCost, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    {{-- Optional Note for Print View --}}
    <div class="mt-4 text-xs text-gray-600 print:block hidden max-w-sm ml-auto text-right">
        *Note: This report shows Net Costs and excludes VAT.*
    </div>
</div>
@endsection