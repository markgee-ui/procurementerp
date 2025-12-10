@extends('layouts.app') 
{{-- Adjust 'layouts.app' to your actual master layout --}}

@section('title', 'Requisition #' . $requisition->id)

@section('content')

<div class="container mx-auto p-4 md:p-8">

    {{-- Header and Back Button --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            Purchase Requisition #{{ $requisition->id }}
        </h1>
        <a href="{{ route('pm.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg transition duration-150">
            &larr; Back to Dashboard
        </a>
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
        <div class="text-sm">Approval Stage: {{ $requisition->current_stage }}</div>
    </div>


    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Column 1: Requisition Details --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-lg border border-gray-100">
            <h2 class="text-xl font-semibold text-gray-700 border-b pb-3 mb-4">Material Details</h2>
            
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 text-gray-700">
                <div class="border-b pb-2">
                    <dt class="text-sm font-medium text-gray-500">Item Name</dt>
                    <dd class="mt-1 text-lg font-semibold">{{ $requisition->item_name }}</dd>
                </div>
                <div class="border-b pb-2">
                    <dt class="text-sm font-medium text-gray-500">Unit</dt>
                    <dd class="mt-1 text-lg">{{ $requisition->unit }}</dd>
                </div>
                <div class="border-b pb-2">
                    <dt class="text-sm font-medium text-gray-500">Quantity Requested</dt>
                    <dd class="mt-1 text-2xl font-bold text-indigo-600">{{ number_format($requisition->qty_requested, 2) }}</dd>
                </div>
                <div class="border-b pb-2">
                    <dt class="text-sm font-medium text-gray-500">Estimated Cost</dt>
                    <dd class="mt-1 text-2xl font-bold text-green-600">${{ number_format($requisition->cost_estimate, 2) }}</dd>
                </div>
                <div class="md:col-span-2 border-b pb-2">
                    <dt class="text-sm font-medium text-gray-500">Required By Date</dt>
                    <dd class="mt-1 text-lg">{{ $requisition->required_by_date ? $requisition->required_by_date->format('d M, Y') : 'N/A' }}</dd>
                </div>
                <div class="md:col-span-2 border-b pb-2">
                    <dt class="text-sm font-medium text-gray-500">Justification</dt>
                    <dd class="mt-1 text-base italic bg-gray-50 p-3 rounded">{{ $requisition->justification }}</dd>
                </div>
            </dl>
        </div>

        {{-- Column 2: Metadata and Actions --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Metadata Card --}}
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                <h2 class="text-xl font-semibold text-gray-700 border-b pb-3 mb-4">Metadata</h2>
                <dl class="space-y-3 text-gray-700">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Submitted By</dt>
                        <dd class="text-base">{{ $requisition->user->name ?? 'System' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Submission Date</dt>
                        <dd class="text-base">{{ $requisition->created_at->format('d M, Y h:i A') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Linked BoQ/Project</dt>
                        <dd class="text-base">#{{ $requisition->boq->id }} - {{ $requisition->boq->project_name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">BoQ Material Line</dt>
                        <dd class="text-base">ID: #{{ $requisition->boq_material_id }}</dd>
                    </div>
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
                                This requisition is currently awaiting approval from the **{{ $requisition->currentApproverRole() }}**.
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
        <h2 class="text-xl font-semibold text-gray-700 border-b pb-3 mb-4">Approval History</h2>
        {{-- You would loop through a related 'Approvals' table here if you had one --}}
        <p class="text-gray-500">No detailed history available in this view yet. See approval notes: {{ $requisition->approval_notes ?? 'N/A' }}</p>
    </div>

</div>

@endsection