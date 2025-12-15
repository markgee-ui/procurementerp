@extends('layouts.app')

@section('title', 'Review PR #{{ $requisition->id }}')

@section('content')
<div class="container mx-auto p-4 md:p-8">

    <h1 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">
        Review Purchase Requisition #{{ $requisition->id }} 
        <span class="text-lg text-yellow-600 ml-2">(Stage 2: Awaiting OPM Approval)</span>
    </h1>

    @if (session('error'))
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">{{ session('error') }}</div>
    @endif

    {{-- PR DETAILS CARD --}}
    <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8 p-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Requisition Overview</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-y-4 gap-x-6 text-sm">
            <div>
                <p class="text-gray-500">Project:</p>
                <p class="font-medium text-gray-900">{{ $requisition->project->project_name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-gray-500">Initiated By:</p>
                <p class="font-medium text-gray-900">{{ $requisition->initiator->name ?? 'Unknown' }}</p>
            </div>
            <div>
                <p class="text-gray-500">Date Submitted:</p>
                <p class="font-medium text-gray-900">{{ $requisition->created_at->format('M d, Y H:i') }}</p>
            </div>
            <div>
                <p class="text-gray-500">Required By:</p>
                <p class="font-medium text-gray-900">{{ $requisition->required_by_date ? $requisition->required_by_date->format('M d, Y') : 'N/A' }}</p>
            </div>
            <div>
                <p class="text-gray-500">Justification:</p>
                <p class="font-medium text-gray-900">{{ $requisition->justification ?? 'N/A' }}</p>
            </div>
            <div class="md:col-span-3">
                <p class="text-gray-500 font-bold text-lg">Total Estimated Cost:</p>
                <p class="text-2xl font-extrabold text-red-600">KES {{ number_format($requisition->cost_estimate, 2) }}</p>
            </div>
        </div>
    </div>
    
    {{-- REQUISITION ITEMS TABLE --}}
    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Requested Items</h2>
    <div class="overflow-x-auto shadow-md sm:rounded-lg mb-8 border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase border-r border-gray-200">#</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase border-r border-gray-200">Item Name (or BOQ Material)</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase border-r border-gray-200">Activity</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-600 uppercase border-r border-gray-200">Unit</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase border-r border-gray-200">Qty</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase border-r border-gray-200">Unit Cost (Est.)</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase">Cost Estimate</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($requisition->items as $index => $item)
                    <tr class="{{ $loop->odd ? 'bg-white' : 'bg-gray-50' }}">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 border-r border-gray-200">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200">
                            {{ $item->item_name }}
                            @if ($item->boqMaterial)
                                <span class="block text-xs text-indigo-500">({{ $item->boqMaterial->name }})</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 border-r border-gray-200">
                            {{ $item->boqActivity->name ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-center text-gray-500 border-r border-gray-200">{{ $item->unit }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-medium text-gray-700 border-r border-gray-200">{{ number_format($item->qty_requested, 2) }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-700 border-r border-gray-200">KES {{ number_format($item->unit_cost, 2) }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-semibold text-gray-900">KES {{ number_format($item->cost_estimate, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- APPROVAL HISTORY --}}
    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Approval History</h2>
    <div class="bg-white shadow-md rounded-lg p-4 mb-8">
        @forelse ($requisition->approvals->sortBy('stage') as $approval)
            <div class="mb-4 pb-2 border-b last:border-b-0">
                <p class="text-sm font-semibold text-indigo-700">Stage {{ $approval->stage }}: {{ ucfirst($approval->status) }}</p>
                <p class="text-xs text-gray-600">By: {{ $approval->user->name ?? 'System' }} on {{ $approval->created_at->format('M d, Y H:i') }}</p>
                <p class="mt-1 text-sm italic @if($approval->status == 'rejected') text-red-600 @endif">Notes: {{ $approval->notes ?? 'N/A' }}</p>
            </div>
        @empty
            <p class="text-sm text-gray-500">No approval actions recorded yet.</p>
        @endforelse
    </div>

    {{-- OPM ACTION BUTTONS --}}
    @if ($requisition->current_stage == 2)
        <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Take Action</h2>
        <div class="flex space-x-4">
            
            {{-- Approve Button (Triggers form submission) --}}
            <form action="{{ route('opm.requisitions.approve', $requisition) }}" method="POST">
                @csrf
                <button type="submit" onclick="return confirm('Are you sure you want to approve PR #{{ $requisition->id }}? This will forward it to the next stage.')" 
                        class="px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 flex items-center">
                    <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Approve and Forward (Stage 3)
                </button>
            </form>

            {{-- Reject Button (Triggers modal/input for notes) --}}
            <button type="button" 
                    class="px-6 py-3 border border-red-600 text-base font-medium rounded-md shadow-sm text-red-600 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 flex items-center"
                    onclick="document.getElementById('rejection-form-container').classList.toggle('hidden')">
                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
                Reject
            </button>
        </div>

        {{-- Rejection Form (Hidden by default) --}}
        <div id="rejection-form-container" class="hidden mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <form action="{{ route('opm.requisitions.reject', $requisition) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="rejection_notes" class="block text-sm font-medium text-red-700">Rejection Notes (Required)</label>
                    <textarea name="rejection_notes" id="rejection_notes" rows="3" required 
                              class="mt-1 block w-full border-red-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 @error('rejection_notes') border-red-500 @enderror" placeholder="State the reason(s) for rejection clearly..."></textarea>
                    @error('rejection_notes')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700">
                    Confirm Rejection
                </button>
            </form>
        </div>
    @else
        <div class="mt-6 p-4 bg-gray-100 rounded-lg">
            <p class="text-gray-700 font-medium">This requisition is currently in **Stage {{ $requisition->current_stage }}** and cannot be actioned by the Office PM at this time.</p>
        </div>
    @endif
    
    <div class="mt-8">
        <a href="{{ route('opm.requisitions.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">&larr; Back to PR List</a>
    </div>

</div>
@endsection