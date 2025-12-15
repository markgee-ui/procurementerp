@extends('layouts.app') 

@section('title', 'PRs Awaiting QS Approval')

@section('content')

<div class="container mx-auto p-4 md:p-8">

    @if (session('success'))
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">{{ session('error') }}</div>
    @endif

    {{-- Filter/Search Section (Optional, but good practice) --}}
    <form method="GET" class="mb-6 p-4 bg-gray-50 rounded-lg shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="boq_id" class="block text-sm font-medium text-gray-700">Filter by Project</label>
                <select name="boq_id" id="boq_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">All Projects</option>
                    @foreach ($boqs as $boq)
                        <option value="{{ $boq->id }}" {{ request('boq_id') == $boq->id ? 'selected' : '' }}>
                            {{ $boq->project_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                {{-- Placeholder for a Search Field --}}
            </div>
            <div class="md:col-span-1 flex items-end">
                <button type="submit" class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Filter
                </button>
            </div>
        </div>
    </form>


    @if ($requisitions->isEmpty())
        <div class="text-center p-10 border-2 border-dashed border-gray-300 rounded-lg bg-white">
            <p class="text-xl text-gray-600"> No Purchase Requisitions require your approval at this time (Stage 1).</p>
        </div>
    @else
        <div class="overflow-x-auto shadow-md sm:rounded-lg border border-gray-200"> {{-- Added border here --}}
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100"> {{-- Slightly darker background for header --}}
                    <tr>
                        {{-- Added border-r to most columns --}}
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase border-r border-gray-200">PR #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase border-r border-gray-200">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase border-r border-gray-200">Initiator</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase border-r border-gray-200">Est. Cost</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase border-r border-gray-200">Required By</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">Action</th> {{-- No border-r on the last column --}}
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($requisitions as $requisition)
                        <tr class="{{ $loop->odd ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100 transition duration-150"> {{-- Stripe rows for better visibility --}}
                            {{-- Added border-r to cells --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-indigo-600 border-r border-gray-200">
                                #{{ $requisition->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 border-r border-gray-200">
                                {{ $requisition->project->project_name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 border-r border-gray-200">
                                {{ $requisition->initiator->name ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 font-medium border-r border-gray-200">
                                KES {{ number_format($requisition->cost_estimate, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r border-gray-200">
                                {{ $requisition->required_by_date ? $requisition->required_by_date->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <a href="{{ route('qs.requisitions.show', $requisition) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                    Review & Act
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $requisitions->links() }}
        </div>
    @endif
</div>
@endsection