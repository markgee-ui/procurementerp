<!DOCTYPE html>
<html>
<head>
    <title>Purchase Requisition #{{ $requisition->id }}</title>
    <style>
        /* General Setup */
        body { font-family: Arial, sans-serif; margin: 0; padding: 30px; font-size: 10pt; }

        /* --- HEADER CSS --- */
        .header-container { 
            width: 100%; 
            border-bottom: 2px solid #333; 
            padding-bottom: 10px; 
            margin-bottom: 20px; 
            overflow: auto; 
        }
        .logo-left { 
            max-width: 100px; 
            height: auto; 
            float: left; 
            margin-right: 20px;
        }
        .header-title { 
            text-align: center;
            margin-left: 120px; 
            font-size: 14pt; 
            color: #1f2937; 
            margin-top: 5px; 
        }
        h1 { 
            font-size: 14pt; 
            color: #1f2937; 
            margin: 0; 
            padding: 0;  
        }

        /* Metadata Block */
        .metadata-block { 
            width: 100%; 
            border: 1px solid #ccc; 
            margin-bottom: 20px; 
            padding: 10px;
            display: table;
            table-layout: fixed;
        }
        .meta-item { 
            font-size: 9pt; 
            line-height: 1.6; 
            display: table-cell; 
            width: 25%; /* Adjusted width for 4 items per row */
            padding-right: 10px;
        }
        .meta-label { font-weight: bold; color: #555; display: block; }
        .meta-value { color: #000; font-weight: bold; font-size: 10pt; }
        
        /* Justification Block */
        .justification-block { 
            width: 100%; 
            border: 1px solid #ccc; 
            margin-bottom: 20px; 
            padding: 10px;
            font-size: 9pt;
        }
        .justification-block strong { font-size: 10pt; color: #333; display: block; margin-bottom: 5px; }

        /* Item Table */
        h2 { font-size: 12pt; color: #333; margin-top: 25px; margin-bottom: 10px; }
        .excel-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        
        .excel-table th, .excel-table td {
            border: 1px solid #333; 
            padding: 5px 8px; 
            text-align: left;
            font-size: 9pt;
        }
        .excel-table th {
            background-color: #e5e7eb; 
            color: #333;
            font-weight: bold;
            text-transform: uppercase;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Footer/Signatures */
        .footer { margin-top: 40px; width: 100%; display: table; table-layout: fixed; }
        .signature-box { display: table-cell; width: 33.33%; padding: 10px; text-align: center; }
        .signature-line { border-top: 1px solid #000; margin-top: 40px; padding-top: 5px; font-size: 8pt; }
        
    </style>
</head>
<body>

    {{-- 1. HEADER SECTION --}}
    <div class="header-container">
        <img src="{{ public_path('hms-logo.png') }}" alt="Company Logo" class="logo-left">
        <div class="header-title">
            <h1>PURCHASE REQUISITION</h1>
            <p style="font-size: 10pt; margin-top: 3px; color: #555;">Document ID: PR-{{ $requisition->id }}</p>
        </div>
    </div>

    {{-- 2. METADATA BLOCK: PR No., Project, Date --}}
    <div class="metadata-block">

        <div class="meta-item" style="width: 40%;">
            <span class="meta-label">Project:</span>
            <span class="meta-value">#{{ $requisition->project->id ?? 'N/A' }} - {{ $requisition->project->project_name ?? 'N/A' }}</span>
        </div>
        <div class="meta-item" style="width: 15%;">
            <span class="meta-label">Submission Date:</span>
            <span class="meta-value">{{ $requisition->created_at->format('d M, Y') }}</span>
        </div>
    </div>
    
    {{-- 3. JUSTIFICATION --}}
    <div class="justification-block">
        <strong>Overall Justification:</strong>
        <p style="margin: 0; padding: 0;">{{ $requisition->justification }}</p>
    </div>

    {{-- 4. EXCEL-STYLE ITEM TABLE (Multi-Item) --}}
    <h2>Items Requested</h2>
    <table class="excel-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No.</th>
                <th style="width: 45%;">Item Name / Description</th>
                <th style="width: 10%;" class="text-center">Unit</th>
                <th style="width: 10%;" class="text-right">Qty Requested</th>
                <th style="width: 30%;">Remarks (Activity/Notes)</th>
            </tr>
        </thead>
        <tbody>
            @php $item_count = 0; @endphp
            @foreach ($requisition->items as $item)
                @php $item_count++; @endphp
                <tr>
                    <td class="text-center">{{ $item_count }}</td>
                    <td>{{ $item->item_name }}</td>
                    <td class="text-center">{{ $item->unit }}</td>
                    <td class="text-right">{{ number_format($item->qty_requested, 2) }}</td>
                    {{-- Using the Activity name or a fallback as the per-item remark --}}
                    <td>{{ $item->boqActivity->name ?? 'N/A' }}</td>
                </tr>
            @endforeach
            
            {{-- Add empty rows for the Excel 'look' and reserve space --}}
            @for ($i = $item_count; $i < ($item_count < 5 ? 5 : $item_count + 1); $i++)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
        </tbody>
    </table>

    {{-- 5. FOOTER: Signatures --}}
    <div class="footer">
        <div class="signature-box">
            <div style="font-weight: bold; margin-bottom: 5px;">Requested By:</div>
            <div style="font-size: 8pt; margin-bottom: 20px;">{{ $requisition->initiator->name ?? 'N/A' }}</div>
            <div class="signature-line">Signature / Date</div>
        </div>
        <div class="signature-box">
            <div style="font-weight: bold; margin-bottom: 5px;">Approved By (Stage 1):</div>
            <div style="font-size: 8pt; margin-bottom: 20px;">{{ $requisition->approval_notes ?? 'Awaiting' }}</div>
            <div class="signature-line">Signature / Date</div>
        </div>
        <div class="signature-box">
            <div style="font-weight: bold; margin-bottom: 5px;">Authorized By (Stage 2):</div>
            <div style="font-size: 8pt; margin-bottom: 20px;">{{ $requisition->approval_notes ?? 'Awaiting' }}</div>
            <div class="signature-line">Signature / Date</div>
        </div>
    </div>

</body>
</html>