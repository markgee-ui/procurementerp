@extends('layouts.app') 
{{-- Adjust 'layouts.app' to your actual master layout --}}

@section('title', 'Requisition #' . $requisition->id)

@section('content')

<div class="container mx-auto p-4 md:p-8">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            Purchase Requisition #{{ $requisition->id }}
        </h1>
        <div class="space-x-2 flex">
            
            {{-- 1. Download PDF Button --}}
            <a href="{{ route('pm.requisitions.pdf', $requisition) }}" 
               class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-150">
                Download PDF
            </a>

            {{-- 2. Print Button (JavaScript) --}}
            <button onclick="window.print()"
                    class="bg-gray-700 hover:bg-gray-800 text-white font-semibold py-2 px-4 rounded-lg transition duration-150 print:hidden">
                Print
            </button>

            {{-- 3. Back Button (Adjust route as needed) --}}
            <a href="{{ route('pm.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg transition duration-150">
                &larr; Back to Dashboard
            </a>
        </div>
    </div>

    {{-- Status Banner --}}
    @php
        $statusClasses = [
            'Pending' => 'bg-yellow-100 text-yellow-800 border-yellow-400',
            'Approved' => 'bg-green-100 text-green-800 border-green-400',
            'Rejected' => 'bg-red-100 text-red-800 border-red-400',
            'Procurement' => 'bg-blue-100 text-blue-800 border-blue-400',
        ];
        $currentStatusClass = $statusClasses[$requisition->status] ?? 'bg-gray-100 text-gray-800 border-gray-400';
    @endphp

    <div class="p-4 mb-6 rounded-lg border-l-4 {{ $currentStatusClass }} shadow-md">
        <div class="font-bold text-lg">Current Status: {{ $requisition->status }}</div>
        <div class="text-sm">
            Approval Stage: {{ $requisition->current_stage }} 
            @if ($requisition->status == 'Pending')
                (Awaiting: {{ $requisition->currentApproverRole() }})
            @endif
        </div>
    </div>


    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Column 1: LINE ITEM TABLE (Replaces single Material Details) --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-lg border border-gray-100">
            <h2 class="text-xl font-semibold text-gray-700 border-b pb-3 mb-4">Requisition Line Items ({{ $requisition->items->count() }})</h2>
            
            @if ($requisition->items->isEmpty())
                <p class="text-gray-500 italic">This requisition has no line items.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Cost</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Est. Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($requisition->items as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $item->boqActivity->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $item->item_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->unit }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ number_format($item->qty_requested, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-600">KES{{ number_format($item->unit_cost, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-right text-indigo-600">KES{{ number_format($item->cost_estimate, 2) }}</td>
                                </tr>
                            @endforeach
                            {{-- TOTAL ROW --}}
                            <tr class="bg-gray-50 font-bold border-t-2 border-gray-300">
                                <td colspan="5" class="px-6 py-3 text-right text-md text-gray-700">GRAND TOTAL ESTIMATE:</td>
                                <td class="px-6 py-3 text-right text-xl text-green-700">
                                    KES{{ number_format($requisition->cost_estimate ?? 0, 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Global Details (Required Date, Justification) --}}
            <div class="mt-6 border-t pt-4">
                 <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 text-gray-700">
                    <div class="border-b pb-2">
                        <dt class="text-sm font-medium text-gray-500">Required By Date</dt>
                        <dd class="mt-1 text-lg">{{ $requisition->required_by_date ? $requisition->required_by_date->format('d M, Y') : 'N/A' }}</dd>
                    </div>
                    <div class="md:col-span-2 border-b pb-2">
                        <dt class="text-sm font-medium text-gray-500">Overall Justification</dt>
                        <dd class="mt-1 text-base italic bg-gray-50 p-3 rounded">{{ $requisition->justification }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Column 2: Metadata and Actions (No change needed here for multi-item structure) --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Metadata Card --}}
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                <h2 class="text-xl font-semibold text-gray-700 border-b pb-3 mb-4">Metadata</h2>
                <dl class="space-y-3 text-gray-700">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Submitted By</dt>
                        <dd class="text-base">{{ $requisition->initiator->name ?? 'System' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Submission Date</dt>
                        <dd class="text-base">{{ $requisition->created_at->format('d M, Y h:i A') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Linked BoQ/Project</dt>
                        <dd class="text-base">#{{ $requisition->project->id }} - {{ $requisition->project->project_name ?? 'N/A' }}</dd>
                    </div>
                     {{-- Removed: BoQ Material Line ID since it's now an item list --}}
                </dl>
            </div>

            {{-- Action Items Card (The core workflow logic) --}}
            <div class="bg-indigo-50 p-6 rounded-xl shadow-lg border border-indigo-200">
                <h2 class="text-xl font-semibold text-indigo-800 border-b border-indigo-300 pb-3 mb-4">Action Items</h2>
                
                {{-- Check 1: Must be Pending to be acted upon --}}
                @if ($requisition->status == 'Pending')
                    
                    {{-- Check 2: Check Authorization Policy/Gate --}}
                    @can('approve', $requisition)
                        <p class="text-sm text-indigo-700 mb-4">
                            You are authorized to act on this requisition (Stage {{ $requisition->current_stage }}).
                        </p>
                        <div class="flex flex-col space-y-3">
                            {{-- Approve Button Form --}}
                            <form action="{{ route('requisitions.approve', $requisition) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg transition duration-150">
                                    APPROVE & Advance (Stage {{ $requisition->current_stage + 1 }})
                                </button>
                            </form>

                            {{-- Reject Button Form --}}
                            <form action="{{ route('requisitions.reject', $requisition) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg transition duration-150"
                                        onclick="return confirm('Are you sure you want to reject this requisition? A note will be required.')">
                                    REJECT
                                </button>
                            </form>
                        </div>
                    @else
                        {{-- User is NOT authorized for the current stage --}}
                        <div class="text-center p-4 bg-white rounded-lg border border-gray-300">
                            <p class="text-gray-600 font-semibold">Awaiting Approval</p>
                            <p class="text-sm text-gray-500 mt-1">
                                This requisition is currently awaiting approval from the {{ $requisition->currentApproverRole() }}.
                            </p>
                        </div>
                    @endcan

                @elseif ($requisition->status == 'Approved')
                    <div class="text-center p-4 bg-green-50 rounded-lg border border-green-300">
                        <p class="text-green-700 font-semibold">Final Approval Granted.</p>
                        <p class="text-sm text-green-600 mt-1">Ready for Procurement processing.</p>
                    </div>
                @elseif ($requisition->status == 'Rejected')
                    <div class="text-center p-4 bg-red-50 rounded-lg border border-red-300">
                        <p class="text-red-700 font-semibold">Requisition Rejected.</p>
                        <p class="text-sm text-red-600 mt-1">Notes: {{ $requisition->approval_notes ?? 'N/A' }}</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
    
    {{-- Optional: Approval History/Notes --}}
    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 mt-6">
        <h2 class="text-xl font-semibold text-gray-700 border-b pb-3 mb-4">Approval History / Notes</h2>
        <p class="text-gray-500">
            Current Approval Notes: 
            <span class="font-mono text-gray-800">{{ $requisition->approval_notes ?? 'N/A' }}</span>
        </p>
    </div>

</div>

@endsection