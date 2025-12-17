@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">PRs Ready for Procurement</h2>

    {{-- Filter Form Here --}}

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        {{-- Add 'divide-x divide-gray-200' to the table element to apply vertical borders --}}
        <table class="min-w-full divide-y divide-gray-200 divide-x divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    {{-- Add border-r to all headers except the last one (Action) --}}
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Project</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Submitted By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Status</th>
                    {{-- Last column header, no border-r --}}
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            {{-- Note: The 'divide-y divide-gray-200' on tbody handles row division (horizontal) --}}
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($requisitions as $requisition)
                <tr>
                    {{-- Apply border-r to all data cells except the last one --}}
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 border-r border-gray-200">PR-{{ $requisition->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">{{ $requisition->project->project_name?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">{{ $requisition->initiator?->name ?? 'User Missing' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r border-gray-200">{{ $requisition->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                        {{-- Status Pill --}}
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Approved (Stage 2)
                        </span>
                    </td>
                    {{-- Last column cell, no border-r --}}
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('procurement.requisition.action', $requisition) }}" class="text-indigo-600 hover:text-indigo-900">
                            Action / Create PO
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No requisitions currently awaiting procurement action.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $requisitions->links() }}
    </div>
</div>
@endsection 