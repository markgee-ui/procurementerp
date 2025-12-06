@extends('layouts.app') 

@section('content')
<div class="p-6 bg-white rounded-lg shadow-xl" id="boq-document"> {{-- Added ID for potential print handling --}}
    <div class="flex justify-between items-center mb-6 border-b pb-2 print:hidden"> {{-- Hiding buttons on print --}}
        <h1 class="text-3xl font-bold text-gray-800">
            BoQ Details: {{ $boq->project_name }} 
        </h1>
        <div class="space-x-2">
            
            {{-- 1. Download as PDF Button --}}
            <a href="{{ route('qs.boq.download', $boq) }}" 
               class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                <i class="fas fa-file-pdf mr-1"></i> Download PDF
            </a>
            
            {{-- 2. Print Button (JavaScript) --}}
            <button onclick="window.print()"
                    class="px-4 py-2 text-sm font-medium text-white bg-gray-600 rounded-md hover:bg-gray-700">
                <i class="fas fa-print mr-1"></i> Print
            </button>
            
            {{-- Edit and Back Buttons --}}
            <a href="{{ route('qs.boq.edit', $boq) }}" 
               class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                Edit BoQ
            </a>
            <a href="{{ route('qs.boq.index') }}" 
               class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                Back to List
            </a>
        </div>
    </div>

    {{-- Project Header Details --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8 p-4 bg-gray-50 rounded-lg border">
        <div>
            <p class="text-sm font-medium text-gray-700">Project Name:</p>
            <p class="text-lg font-semibold text-gray-900">{{ $boq->project_name }}</p>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-700">Total Budget:</p>
            <p class="text-lg font-semibold text-green-600">KSH {{ number_format($boq->project_budget, 2) }}</p>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-700">Date Created:</p>
            <p class="text-lg text-gray-900">{{ $boq->created_at->format('Y-m-d H:i') }}</p>
        </div>
    </div>

    <h2 class="text-xl font-bold text-gray-800 mt-8 mb-4">Project Activities Breakdown</h2>

    <div class="space-y-6">
        @forelse($boq->activities as $activity)
        <div class="activity-section bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
            <div class="flex justify-between items-center bg-gray-100 p-3 rounded-md mb-3">
                <h3 class="text-lg font-semibold text-gray-800">
                    Activity: {{ $activity->name }}
                </h3>
                <span class="text-lg font-bold text-blue-600">
                    Budget: KSH {{ number_format($activity->budget, 2) }}
                </span>
            </div>

            <h4 class="text-md font-semibold text-gray-700 mt-4 border-b pb-1">Materials Breakdown:</h4>

            {{-- Materials Table --}}
            <div class="overflow-x-auto mt-2">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Item</th>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Specifications</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Unit</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Qty</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Rate (KSH)</th> 
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">Total Cost</th>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($activity->materials as $material)
                        <tr>
                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-200">{{ $material->item }}</td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 border-r border-gray-200">{{ $material->specs }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 border-r border-gray-200">{{ $material->unit }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 border-r border-gray-200">{{ number_format($material->qty) }}</td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 border-r border-gray-200">
                                {{ number_format($material->rate, 2) }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm font-semibold text-gray-800 border-r border-gray-200">
                                {{ number_format($material->qty * $material->rate, 2) }}
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ $material->remarks }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @empty
        <p class="text-center text-gray-500">No activities found for this Bill of Quantities.</p>
        @endforelse
    </div>
</div>
@endsection