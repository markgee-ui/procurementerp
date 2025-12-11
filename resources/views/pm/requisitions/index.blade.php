@extends('layouts.app') 
{{-- Assuming you have a main layout file --}}

@section('content')
<div class="p-6 bg-white rounded-lg shadow-xl">
    <div class="flex justify-between items-center mb-6 border-b pb-2">
        <h1 class="text-3xl font-bold text-gray-800">
            Purchase Requisitions List
        </h1>
        
        {{-- Button for adding a new Requisition (Assuming this route is available) --}}
        
    </div>

    {{-- Filter Section --}}
    <div class="mb-6 p-4 bg-gray-50 rounded-lg shadow-inner">
        <form action="{{ route('pm.requisitions.index') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:space-x-4 items-center">
            
            {{-- Search by Item Name (Controller must search through the 'items' relationship) --}}
            <input type="text" name="item_search" placeholder="Search by Item Name" 
                    value="{{ request('item_search') }}"
                    class="w-full md:w-1/3 p-2 border border-gray-300 rounded-md">
            
            {{-- Filter by Status --}}
            <select name="status" class="w-full md:w-1/4 p-2 border border-gray-300 rounded-md">
                <option value="">Filter by Status</option>
                @foreach (['Pending', 'Approved', 'Rejected', 'Procurement', 'Completed'] as $status)
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                        {{ $status }}
                    </option>
                @endforeach
            </select>
            
            {{-- Optional: Filter by Project/BoQ --}}
            {{-- Leaving this commented out as no $boqs variable is provided --}}
            
            <button type="submit" class="w-full md:w-auto px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600">
                Apply Filters
            </button>
            
            @if(request()->hasAny(['item_search', 'status', 'boq_id']))
                <a href="{{ route('pm.requisitions.index') }}" class="w-full md:w-auto px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 text-center">
                    Clear Filters
                </a>
            @endif
        </form>
    </div>

    {{-- Purchase Requisitions Table --}}
    <div class="overflow-x-auto shadow-md sm:rounded-lg border border-gray-200">
        @if($requisitions->isEmpty())
            <p class="p-4 text-center text-gray-500">No Purchase Requisitions found matching your criteria.</p>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">ID / Project</th>
                        {{-- UPDATED: Show consolidated Item Count and Total Cost --}}
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Line Items / Est. Cost</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Initiator</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Status / Stage</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Date Submitted</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($requisitions as $requisition)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-200">
                            #{{ $requisition->id }} <br>
                            <span class="text-xs text-gray-500">{{ $requisition->project->project_name ?? 'N/A' }}</span>
                        </td>
                        
                        {{-- UPDATED CELL: Displaying item count and total cost --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 border-r border-gray-200">
                            <span class="font-semibold text-indigo-600">{{ $requisition->items->count() }}</span> Items<br>
                            <span class="text-xs text-green-600">KES{{ number_format($requisition->cost_estimate ?? 0, 2) }} (Est.)</span>
                        </td>
                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 border-r border-gray-200">
                            {{ $requisition->initiator->name ?? 'System' }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm border-r border-gray-200">
                            @php
                                $statusColor = match ($requisition->status) {
                                    'Approved' => 'bg-green-100 text-green-800',
                                    'Pending' => 'bg-yellow-100 text-yellow-800',
                                    'Rejected' => 'bg-red-100 text-red-800',
                                    'Procurement' => 'bg-blue-100 text-blue-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                {{ $requisition->status }}
                            </span>
                            <span class="text-xs text-gray-500 block mt-1">Stage {{ $requisition->current_stage }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r border-gray-200">
                            {{ $requisition->created_at->format('Y-m-d') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            {{-- Action Section --}}
                            <div class="flex justify-center space-x-3">
                                
                                {{-- View Button (Link to show PR details) --}}
                                <a href="{{ route('pm.requisitions.show', $requisition) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="View Details">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
                                </a>

                                {{-- Edit Button (Only for Pending PRs, assuming policy) --}}
                                @if ($requisition->status == 'Pending')
                                <a href="{{ route('pm.requisitions.edit', $requisition) }}" 
                                   class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-7-7l-2 2m5-5l2 2m-2-2l-3-3m3 3l-3 3"></path></svg>
                                </a>

                                {{-- Delete Button (Only for Pending PRs, assuming policy) --}}
                                <form action="{{ route('pm.requisitions.destroy', $requisition) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel and delete this Purchase Requisition?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete/Cancel">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" /></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            {{-- Pagination Links --}}
            <div class="p-4 bg-white border-t">
                {{ $requisitions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection