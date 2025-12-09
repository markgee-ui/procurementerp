@extends('layouts.app') 
{{-- Assuming you have a main layout file --}}

@section('content')
<div class="p-6 bg-white rounded-lg shadow-xl">
    <div class="flex justify-between items-center mb-6 border-b pb-2">
        <h1 class="text-3xl font-bold text-gray-800">
            Bill of Quantities (BoQ) List
        </h1>
        
        {{-- Button for adding a new BoQ --}}
        <a href="{{ route('qs.boq.create') }}" 
           class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            + Create New BoQ
        </a>
    </div>

    {{-- Filter Section --}}
    <div class="mb-6 p-4 bg-gray-50 rounded-lg shadow-inner">
        <form action="{{ route('qs.boq.index') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:space-x-4">
            <input type="text" name="search" placeholder="Search by Project Name" 
                    value="{{ request('search') }}"
                    class="w-full md:w-1/3 p-2 border border-gray-300 rounded-md">
                    
            <input type="number" name="min_budget" placeholder="Min Budget (KSH)" 
                    value="{{ request('min_budget') }}" step="0.01"
                    class="w-full md:w-1/4 p-2 border border-gray-300 rounded-md">
                    
            <button type="submit" class="w-full md:w-auto px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600">
                Apply Filters
            </button>
            
            @if(request()->hasAny(['search', 'min_budget']))
                <a href="{{ route('qs.boq.index') }}" class="w-full md:w-auto px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 text-center">
                    Clear Filters
                </a>
            @endif
        </form>
    </div>

    {{-- BoQ Table --}}
    <div class="overflow-x-auto shadow-md sm:rounded-lg border border-gray-200">
        @if($boqs->isEmpty())
            <p class="p-4 text-center text-gray-500">No Bills of Quantities found matching your criteria.</p>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Project Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Total Budget</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Date Created</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($boqs as $boq)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-200">
                            {{ $boq->project_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r border-gray-200">
                            KSH {{ number_format($boq->project_budget, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r border-gray-200">
                            {{ $boq->created_at->format('Y-m-d') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            {{-- Action Section --}}
                            <div class="flex justify-center space-x-3">
                                
                                {{-- View Button (Link to qs.boq.show) --}}
                                <a href="{{ route('qs.boq.show', $boq) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="View">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
                                </a>

                                {{-- Edit Button (Link to qs.boq.edit) --}}
                                <a href="{{ route('qs.boq.edit', $boq) }}" 
                                   class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-7-7l-2 2m5-5l2 2m-2-2l-3-3m3 3l-3 3"></path></svg>
                                </a>

                                {{-- Delete Button (Link to qs.boq.destroy) --}}
                                <form action="{{ route('qs.boq.destroy', $boq) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this BoQ?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" /></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            {{-- Pagination Links --}}
            <div class="p-4 bg-white border-t">
                {{ $boqs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection