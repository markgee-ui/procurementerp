@extends('layouts.app')

@section('title', 'All Suppliers')

@section('content')
    
    {{-- MODIFIED HEADER SECTION TO INCLUDE THE BUTTON --}}
    <div class="flex justify-between items-center mb-8">
        <header>
            <h1 class="text-3xl font-bold text-gray-800">Supplier Master List</h1>
            <p class="text-gray-600 mt-1">Manage and view all registered suppliers in the system.</p>
        </header>

        {{-- Quick Add Button --}}
        <a href="{{ route('procurement.create') }}" 
           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
            {{-- Plus Icon --}}
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New Supplier
        </a>
    </div>

    @if ($suppliers->isEmpty())
        <div class="text-center p-10 bg-white rounded-xl shadow-lg">
            <p class="text-xl text-gray-500 font-semibold">No Suppliers Registered.</p>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                {{-- Added divide-x to the table for better border structure --}}
                <table class="min-w-full divide-y divide-gray-200 divide-x divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            {{-- Added border-r to th for vertical column lines --}}
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($suppliers as $supplier)
                            <tr>
                                {{-- Added border-r to td elements for vertical column lines --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-200">{{ $supplier->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r border-gray-200">{{ $supplier->location }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r border-gray-200">{{ $supplier->contact }}</td>
                                <td class="px-6 py-4 whitespace-normal text-sm text-gray-500 max-w-xs">{{ $supplier->address }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{-- PAGINATION LINKS --}}
        <div class="mt-4">
            {{ $suppliers->links() }}
        </div>
    @endif
@endsection