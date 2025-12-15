@extends('layouts.app') 

@section('title', 'Review PR #' . $requisition->id)

@section('content')

<div class="container mx-auto p-4 md:p-8">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            Review Purchase Requisition #{{ $requisition->id }}
        </h1>
        <a href="{{ route('qs.requisitions.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg transition duration-150">
            &larr; Back to Approvals
        </a>
    </div>

    {{-- Status and Workflow --}}
    <div class="bg-white p-6 rounded-xl shadow-lg mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
            <div class="border-r pr-4">
                <dt class="font-medium text-gray-500">Current Status</dt>
                <dd class="text-xl font-bold {{ $requisition->status === 'Pending' ? 'text-yellow-600' : 'text-green-600' }}">{{ $requisition->status }}</dd>
            </div>
            <div class="border-r pr-4">
                <dt class="font-medium text-gray-500">Approval Stage</dt>
                <dd class="text-xl font-bold text-gray-800">Stage {{ $requisition->current_stage }} of 3</dd>
            </div>
            <div class="md:col-span-2">
                <dt class="font-medium text-gray-500">Awaiting Action From</dt>
                <dd class="text-xl font-bold text-indigo-600">
                    {{ $requisition->currentApproverRole() }}
                </dd>
            </div>
        </div>
    </div>

    {{-- Conditional Approval/Rejection Actions --}}
    @include('qs.requisitions.approval_actions', ['requisition' => $requisition])

    {{-- Main Details --}}
    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 mt-6">
        <h2 class="text-xl font-semibold text-gray-700 border-b pb-3 mb-4">Requisition Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <dt class="font-medium text-gray-500">Project / BoQ</dt>
                <dd class="text-gray-900">{{ $requisition->project->project_name ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="font-medium text-gray-500">Initiated By</dt>
                <dd class="text-gray-900">{{ $requisition->initiator->name ?? 'Unknown' }} ({{ $requisition->created_at->format('M d, Y') }})</dd>
            </div>
            <div>
                <dt class="font-medium text-gray-500">Required By Date</dt>
                <dd class="text-gray-900">{{ $requisition->required_by_date ? $requisition->required_by_date->format('M d, Y') : 'N/A' }}</dd>
            </div>
            <div>
                <dt class="font-medium text-gray-500">Estimated Total Cost</dt>
                <dd class="text-2xl font-bold text-red-600">KES {{ number_format($requisition->cost_estimate, 2) }}</dd>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t">
            <dt class="font-medium text-gray-500">Overall Justification</dt>
            <dd class="text-gray-700 whitespace-pre-wrap">{{ $requisition->justification }}</dd>
        </div>
        
        @if ($requisition->approval_notes)
        <div class="mt-6 pt-4 border-t">
            <dt class="font-medium text-gray-500">Rejection/Approval Notes</dt>
            <dd class="text-red-700 font-semibold">{{ $requisition->approval_notes }}</dd>
        </div>
        @endif
    </div>

    {{-- Line Items Table --}}
    <div class="mt-8">
        <h2 class="text-xl font-semibold text-gray-700 border-b pb-3 mb-4">Requested Items</h2>
        <div class="overflow-x-auto shadow-md sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Item Name</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Unit</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Qty Req.</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Unit Cost (BoQ)</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Line Est. Cost</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($requisition->items as $item)
                        <tr>
                            <td class="px-4 py-2 whitespace-normal text-sm text-gray-900">{{ $item->item_name }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-center text-gray-500">{{ $item->unit }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-center text-gray-700 font-medium">{{ $item->qty_requested }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-right text-gray-500">KES {{ number_format($item->unit_cost, 2) }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-right font-semibold text-gray-800">KES {{ number_format($item->cost_estimate, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-100">
                        <td colspan="4" class="px-4 py-3 text-right text-base font-bold text-gray-800">GRAND TOTAL:</td>
                        <td class="px-4 py-3 text-right text-base font-bold text-red-600">KES {{ number_format($requisition->cost_estimate, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection