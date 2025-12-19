@extends('layouts.app')

@section('title', 'All Service Orders')

@section('content')

<div class="flex justify-between items-center mb-8">
    <h2 class="text-2xl font-bold text-gray-800">Service Orders</h2>
    {{-- Button to create a new Service Order --}}
    <a href="{{ route('procurement.supplier.index') }}" 
       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Create New SO
    </a>
</div>

<div class="bg-white p-6 rounded-xl shadow-lg mb-6">
    <h3 class="text-xl font-semibold text-gray-700 mb-4">Filter Service Orders</h3>
    <form method="GET" action="{{ route('procurement.service-order.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">

        {{-- SO Number/Project Search --}}
        <div class="col-span-1">
            <label for="search" class="block text-sm font-medium text-gray-700">SO/Project Search</label>
            <input type="text" name="search" id="search" 
                    value="{{ request('search') }}"
                    placeholder="Search SO # or Project"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>

        {{-- Status Filter --}}
        <div class="col-span-1">
            <label for="status_filter" class="block text-sm font-medium text-gray-700">Filter by Status</label>
            <select id="status_filter" name="status_filter" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">-- All Statuses --</option>
                @foreach (['Draft', 'Pending', 'Approved', 'Canceled'] as $status)
                    <option value="{{ $status }}" {{ request('status_filter') == $status ? 'selected' : '' }}>
                        {{ $status }}
                    </option>
                @endforeach
            </select>
        </div>
        
        {{-- Action Buttons --}}
        <div class="col-span-3 flex space-x-3">
            <button type="submit" 
                    class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 w-full sm:w-auto">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707v3.586a1 1 0 01-1.555.832l-2.5-1A1 1 0 018 17.586V15a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                Apply Filters
            </button>
            
            @if (request()->hasAny(['search', 'status_filter']))
                <a href="{{ route('procurement.service-order.index') }}" 
                   class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 w-full sm:w-auto">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

@if ($serviceOrders->isEmpty())
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md">
        <div class="flex items-center">
            <p class="text-sm font-medium text-yellow-800">No Service Orders match your current filters or none have been created yet.</p>
        </div>
    </div>
@else
    <div class="overflow-x-auto shadow-lg rounded-xl">
        <div class="inline-block min-w-full align-middle">
            <table class="min-w-full divide-y divide-gray-200 border border-gray-300"> 
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase border-r border-gray-200">SO #</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase border-r border-gray-200">Project Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase border-r border-gray-200">Supplier</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase border-r border-gray-200">Order Date</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase border-r border-gray-200">Total Amount</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase border-r border-gray-200">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($serviceOrders as $so)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-indigo-600 border-r border-gray-200">
                                {{ $so->order_number }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">
                                {{ $so->project_name }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 border-r border-gray-200">
                                {{ $so->supplier->name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 border-r border-gray-200">
                                {{ $so->order_date ? $so->order_date->format('d M Y') : 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-sm font-bold text-right text-gray-900 border-r border-gray-200">
                                KES {{ number_format($so->total_amount, 2) }}
                            </td>
                            <td class="px-4 py-3 text-center border-r border-gray-200">
                                @php
                                    $badgeClass = [
                                        'Draft' => 'bg-gray-100 text-gray-800',
                                        'Pending' => 'bg-yellow-100 text-yellow-800',
                                        'Approved' => 'bg-green-100 text-green-800',
                                        'Canceled' => 'bg-red-100 text-red-800',
                                    ][$so->status] ?? 'bg-blue-100 text-blue-800';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeClass }}">
                                    {{ $so->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('procurement.service-order.show', $so->id) }}" 
                                       class="text-teal-600 hover:text-teal-900 p-1 rounded-md hover:bg-teal-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    
                                    @if ($so->status === 'Draft')
                                        <a href="{{ route('procurement.service-order.edit', $so->id) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 p-1 rounded-md hover:bg-indigo-100">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-7-7l-2 2m5-5l2 2m-2-2l-3-3m3 3l-3 3"></path></svg>
                                        </a>

                                        <form action="{{ route('procurement.service-order.destroy', $so->id) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 p-1 rounded-md hover:bg-red-100">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
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
    <div class="mt-4">
        {{ $serviceOrders->links() }}
    </div>
@endif

@endsection