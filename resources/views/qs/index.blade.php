@extends('layouts.app') 
{{-- NOTE: Assumes you have a main layout file at resources/views/layouts/app.blade.php --}}

@section('content')
<div class="p-6 bg-white rounded-lg shadow-xl">
    <div class="flex items-center justify-between border-b pb-4 mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            <span class="text-indigo-600">📊</span> QS Module Dashboard
        </h1>
        
        {{-- Link to the BoQ Creation Form --}}
        <a href="{{ route('qs.boq.create') }}" 
           class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            + New Bill of Quantities (BoQ)
        </a>
    </div>

    <p class="text-gray-600 mb-6">
        Welcome to the Quantity Surveyor Module. Use the navigation to manage project planning and material specifications.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- Card 1: BoQ Management Summary --}}
        <div class="bg-gray-100 p-4 rounded-lg border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700">Project BoQs</h2>
            <p class="text-3xl font-bold text-indigo-600 mt-2">15</p>
            <p class="text-sm text-gray-500">Total BoQs created</p>
        </div>
        
        {{-- Card 2: Status Summary --}}
        <div class="bg-gray-100 p-4 rounded-lg border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700">Awaiting Procurement</h2>
            <p class="text-3xl font-bold text-yellow-600 mt-2">3</p>
            <p class="text-sm text-gray-500">Projects pending material ordering</p>
        </div>
        
        {{-- Card 3: Pending Approvals --}}
        <div class="bg-gray-100 p-4 rounded-lg border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700">Pending Approvals</h2>
            <p class="text-3xl font-bold text-red-600 mt-2">1</p>
            <p class="text-sm text-gray-500">BoQs requiring review</p>
        </div>
    </div>
</div>
@endsection