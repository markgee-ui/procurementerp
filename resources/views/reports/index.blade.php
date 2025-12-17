@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="p-6 bg-white rounded-lg shadow-md border border-gray-100">
        <div class="flex items-center mb-6">
            <svg class="w-6 h-6 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h2 class="text-xl font-bold text-gray-800">Export {{ ucfirst($role) }} Reports</h2>
        </div>
        
        <form action="{{ route('reports.export', 'requisitions') }}" method="GET" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Start Date --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                {{-- End Date --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                    <select name="status" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                        <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft</option>

                    </select>
                </div>
                <div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Project Name</label>
    <select name="project_name" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
        <option value="all">All Projects</option>
        @foreach($projects as $project)
            <option value="{{ $project->project_name }}" {{ request('project_name') == $project->project_name ? 'selected' : '' }}>
                {{ $project->project_name }}
            </option>
        @endforeach
    </select>
</div>
            </div>

            <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-100">
                {{-- Export Requisitions --}}
                <button type="submit" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2.5 rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export Requisitions CSV
                </button>
                
                {{-- Export Orders (Visible only to non-PM roles) --}}
                @if(Auth::user()->role !== 'pm')
                <button type="submit" formaction="{{ route('reports.export', 'orders') }}" 
                    class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-medium px-5 py-2.5 rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export Purchase Orders CSV
                </button>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection