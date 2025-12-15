@extends('layouts.app') 

@section('title', 'Office PM Dashboard')

@section('content')

<div class="container mx-auto p-4 md:p-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 flex items-center">
        Office Project Manager Dashboard
    </h1>

    @if (session('success'))
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- Card: Pending PRs --}}
        <div class="bg-white p-6 rounded-lg shadow-md ">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500">PRs Awaiting Your Approval (Stage 2)</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($pendingCount) }}</p>
                </div>
            </div>
            @if ($pendingCount > 0)
                <div class="mt-4">
                    <a href="{{ route('opm.requisitions.index') }}" class="text-sm font-medium text-yellow-600 hover:text-yellow-700">
                        Review Now &rarr;
                    </a>
                </div>
            @endif
        </div>

        {{-- Placeholder Card --}}
        <div class="bg-white p-6 rounded-lg shadow-md ">
            <p class="text-sm font-medium text-gray-500">Total Projects Managed</p>
            <p class="text-2xl font-bold text-gray-900">N/A</p>
        </div>
        
        {{-- Placeholder Card --}}
        <div class="bg-white p-6 rounded-lg shadow-md ">
            <p class="text-sm font-medium text-gray-500">Total Value Approved (Month)</p>
            <p class="text-2xl font-bold text-gray-900">N/A</p>
        </div>

    </div>
    
    <h2 class="text-xl font-semibold text-gray-700 mt-10 mb-4">Quick Actions</h2>
    <div class="bg-white p-6 rounded-lg shadow-md">
        <a href="{{ route('opm.requisitions.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 7.5V6.5a1 1 0 0 1 1-1h5.5a1 1 0 0 1 1 1V7.5m-9 0v9a1 1 0 0 0 1 1h7.5a1 1 0 0 0 1-1v-9m-10.5 0h10.5m-10.5 0a1 1 0 0 0-1 1v9a1 1 0 0 0 1 1h10.5a1 1 0 0 0 1-1v-9a1 1 0 0 0-1-1h-10.5m-3 0h16.5" />
            </svg>
            Review Purchase Requisitions
        </a>
    </div>

</div>
@endsection