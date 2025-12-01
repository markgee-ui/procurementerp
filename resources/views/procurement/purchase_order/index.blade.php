@extends('layouts.app')

@section('title', 'All Purchase Orders')

@section('content')

<div class="flex justify-between items-center mb-8">
    

    {{-- Button to create a new Purchase Order --}}
    <a href="{{ route('procurement.order.create_select_supplier') }}" 
       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
        {{-- Plus Icon --}}
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Create New PO
    </a>
</div>

<div class="bg-white p-6 rounded-xl shadow-lg mb-6">
    <h3 class="text-xl font-semibold text-gray-700 mb-4">Filter Purchase Orders</h3>
    <form method="GET" action="{{ route('procurement.order.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">

        {{-- PO Number/Project Search --}}
        <div class="col-span-1">
            <label for="search" class="block text-sm font-medium text-gray-700">PO/Project Search</label>
            <input type="text" name="search" id="search" 
                    value="{{ request('search') }}"
                    placeholder="Search PO # or Project Name"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>

        {{-- Status Filter Dropdown (Draft, Issued, Received, Canceled) --}}
        <div class="col-span-1">
            <label for="status_filter" class="block text-sm font-medium text-gray-700">Filter by Status</label>
            <select id="status_filter" name="status_filter" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">-- All Statuses --</option>
                @foreach (['Draft', 'Issued', 'Received', 'Canceled'] as $status)
                    <option value="{{ $status }}" 
                            {{ request('status_filter') == $status ? 'selected' : '' }}>
                        {{ $status }}
                    </option>
                @endforeach
            </select>
        </div>
        
        {{-- Project Filter Dropdown (Requires $projects from Controller) --}}
        <div class="col-span-1">
            <label for="project_filter" class="block text-sm font-medium text-gray-700">Filter by Project</label>
            <select id="project_filter" name="project_filter" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">-- All Projects --</option>
                {{-- Loop through distinct project names passed from the controller --}}
                @foreach ($projects as $project)
                    <option value="{{ $project }}" 
                            {{ request('project_filter') == $project ? 'selected' : '' }}>
                        {{ $project }}
                    </option>
                @endforeach
            </select>
        </div>


        {{-- Action Buttons --}}
        <div class="col-span-2 flex space-x-3">
            <button type="submit" 
                    class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 w-full sm:w-auto">
                {{-- Filter Icon --}}
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707v3.586a1 1 0 01-1.555.832l-2.5-1A1 1 0 018 17.586V15a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                Apply Filters
            </button>
            
            {{-- Reset Button (Only shows if filters are currently active) --}}
            @if (request()->hasAny(['search', 'status_filter', 'project_filter']))
                <a href="{{ route('procurement.order.index') }}" 
                   class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 w-full sm:w-auto">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

@if ($purchaseOrders->isEmpty())
    {{-- Display message if no POs are found --}}
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-yellow-800">No Purchase Orders match your current filters or none have been created yet.</p>
            </div>
        </div>
    </div>
@else
    <div class="overflow-x-auto shadow-lg rounded-xl">
        <div class="inline-block min-w-full align-middle">
            {{-- ADDED: border border-gray-300 to show outer border --}}
            <table class="min-w-full divide-y divide-gray-200 border border-gray-300"> 
                <thead class="bg-gray-50">
                    <tr>
                        {{-- ADDED: border-r border-gray-200 to all but the last column --}}
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">PO #</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Project Name</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Supplier</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Date</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Total Amount</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Status</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        {{-- The last column (Actions) does not need border-r --}}
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($purchaseOrders as $po)
                        <tr class="hover:bg-gray-50">
                            {{-- Data Cells: ADDED border-r border-gray-200 to all but the last cell --}}
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-indigo-600 border-r border-gray-200">
                                {{ $po->order_number ?? 'TGL' . $po->id }}
                            </td>

                            {{-- Project Name --}}
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 border-r border-gray-200">
                                {{ $po->project_name ?? 'N/A' }}
                            </td>
                            
                            {{-- Supplier Name --}}
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 border-r border-gray-200">
                                {{ $po->supplier->name ?? 'N/A' }}
                            </td>

                            {{-- Date --}}
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 border-r border-gray-200">
                                {{ $po->order_date->format('d M Y') }}
                            </td>

                            {{-- Total Amount --}}
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-right text-gray-900 border-r border-gray-200">
                                KES {{ number_format($po->total_amount, 2) }}
                            </td>
                            
                            {{-- Status Badge --}}
                            <td class="px-4 py-3 whitespace-nowrap text-center border-r border-gray-200">
                                @php
                                    $badgeClass = [
                                        'Draft' => 'bg-gray-100 text-gray-800',
                                        'Issued' => 'bg-blue-100 text-blue-800',
                                        'Received' => 'bg-green-100 text-green-800',
                                        'Canceled' => 'bg-red-100 text-red-800',
                                    ][$po->status] ?? 'bg-yellow-100 text-yellow-800';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeClass }}">
                                    {{ $po->status }}
                                </span>
                            </td>

                            {{-- Actions Cell --}}
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    {{-- View Details --}}
                                    <a href="{{ route('procurement.order.show', $po->id) }}" 
                                       title="View Purchase Order"
                                       class="text-teal-600 hover:text-teal-900 transition duration-150 ease-in-out p-1 rounded-md hover:bg-teal-100">
                                        {{-- Eye Icon --}}
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    
                                    {{-- Edit/Modify (e.g., if status is Draft) --}}
                                    @if ($po->status === 'Draft')
                                        <a href="{{ route('procurement.order.edit', $po->id) }}" 
                                           title="Edit Draft Order"
                                           class="text-indigo-600 hover:text-indigo-900 transition duration-150 ease-in-out p-1 rounded-md hover:bg-indigo-100">
                                            {{-- Pencil Icon --}}
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-7-7l-2 2m5-5l2 2m-2-2l-3-3m3 3l-3 3"></path></svg>
                                        </a>
                                    @endif

                                    {{-- Delete Form/Button (Only for Drafts) --}}
                                    @if ($po->status === 'Draft')
                                        <form action="{{ route('procurement.order.destroy', $po->id) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete Purchase Order #{{ $po->order_number ?? $po->id }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Delete Order" 
                                                    class="text-red-600 hover:text-red-900 transition duration-150 ease-in-out p-1 rounded-md hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                                {{-- Trash Can Icon --}}
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    {{-- PAGINATION LINKS --}}
    <div class="mt-4">
        {{ $purchaseOrders->links() }}
    </div>
@endif

@endsection