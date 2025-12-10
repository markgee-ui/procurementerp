@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Project Manager Dashboard</h1>

    @if (session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded mb-4">{{ session('success') }}</div>
    @endif

    {{-- Pending Approvals Card --}}
    <div class="mb-8">
        <div class="p-6 bg-white rounded-lg shadow-lg">
            <h3 class="text-xl font-semibold text-gray-700">PRs Awaiting Approval</h3>
            <p class="text-4xl font-bold text-red-600 mt-2">{{ $pendingApprovals }}</p>
            <p class="text-sm text-gray-500">Submitted by other Site Managers, awaiting your review.</p>
        </div>
    </div>

    {{-- Projects List --}}
    <h2 class="text-2xl font-semibold mb-4 border-b pb-2">Active Projects</h2>
    
    <div class="overflow-x-auto bg-white shadow-md rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activities</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($projects as $project)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $project->project_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $project->activities_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('pm.requisitions.create', ['project' => $project->id]) }}" 
                               class="text-indigo-600 hover:text-indigo-900 font-medium">
                                + Create Requisition
                            </a>
                            <span class="text-gray-400 mx-2">|</span>
                            <a href="#" class="text-blue-600 hover:text-blue-900 font-medium">View BoQ</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection