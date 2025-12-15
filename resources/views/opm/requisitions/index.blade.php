@extends('layouts.app') 

@section('title', 'PRs Awaiting OPM Approval')

@section('content')

<div class="container mx-auto p-4 md:p-8">

    <h1 class="text-3xl font-bold text-gray-800 mb-6 flex items-center">
        Purchase Requisitions Awaiting Office PM Review (Stage 2)
    </h1>

    @if (session('success'))
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">{{ session('error') }}</div>
    @endif

    {{-- Filter/Search Section --}}
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
            {{-- Placeholder for a Search Field (if needed) --}}
            <div></div> 
            <div class="md:col-span-1 flex items-end">
                <button type="submit" class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Filter
                </button>
            </div>
        </div>
    </form>


    @if ($requisitions->isEmpty())
        <div class="text-center p-10 border-2 border-dashed border-gray-300 rounded-lg bg-white">
            <p class="text-xl text-gray-600">No Purchase Requisitions require your approval at this time (Stage 2).</p>
        </div>
    @else
        <div class="overflow-x-auto shadow-md sm:rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase border-r border-gray-200">PR #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase border-r border-gray-200">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase border-r border-gray-200">Initiator</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase border-r border-gray-200">Est. Cost</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase border-r border-gray-200">Required By</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">Status</th> 
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($requisitions as $requisition)
                        <tr class="{{ $loop->odd ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-yellow-700 border-r border-gray-200">
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center border-r border-gray-200">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Awaiting OPM
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <a href="{{ route('opm.requisitions.show', $requisition) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">
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