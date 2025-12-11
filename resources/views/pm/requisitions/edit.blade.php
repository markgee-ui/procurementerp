@extends('layouts.app') 
{{-- Adjust 'layouts.app' to your actual master layout --}}

@section('title', 'Edit Requisition #' . $requisition->id)

@section('content')

<div class="container mx-auto p-4 md:p-8">

    {{-- Header and Back Button --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
             Edit Purchase Requisition #{{ $requisition->id }}
        </h1>
        <a href="{{ route('pm.requisitions.show', $requisition) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg transition duration-150">
            &larr; Back to Requisition
        </a>
    </div>

    {{-- Global Error Handling (for fields like justification, date, etc.) --}}
    @if ($errors->any() && !$errors->has('items.*'))
        <div class="p-4 mb-6 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
            <p class="font-bold">Please correct the following errors:</p>
            <ul class="mt-1.5 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    {{-- Only display errors not related to line items here --}}
                    @if (!str_contains($error, 'items.'))
                        <li>{{ $error }}</li>
                    @endif
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Section --}}
    <form action="{{ route('pm.requisitions.update', $requisition) }}" method="POST" class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
        @csrf
        @method('PATCH') {{-- Use PATCH method for updates --}}

        <div class="space-y-8">
            
            {{-- 1. HEADER FIELDS (Project, Date) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 border-b pb-4">
                {{-- Project Details (Read-only) --}}
                <div class="md:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Project / BoQ</dt>
                    <dd class="text-base font-semibold text-gray-800">
                        {{-- Include hidden field for boq_id if required by validation --}}
                        <input type="hidden" name="boq_id" value="{{ $requisition->boq_id }}">
                        #{{ $requisition->project->id ?? 'N/A' }} - {{ $requisition->project->project_name ?? 'Project Not Found' }}
                    </dd>
                </div>
                
                {{-- Required By Date --}}
                <div class="md:col-span-1">
                    <label for="required_by_date" class="block text-sm font-medium text-gray-700">Required By Date</label>
                    <input type="date" name="required_by_date" id="required_by_date"
                           value="{{ old('required_by_date', $requisition->required_by_date ? $requisition->required_by_date->format('Y-m-d') : '') }}"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('required_by_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                {{-- Status --}}
                <div class="md:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Current Status</dt>
                    <dd class="text-base font-bold text-orange-600">{{ $requisition->status }}</dd>
                </div>
            </div>

            {{-- 2. EDITABLE LINE ITEMS TABLE --}}
            <div>
                <h2 class="text-xl font-semibold text-gray-700 border-b pb-3 mb-4">Items to Purchase ({{ $requisition->items->count() }} items)</h2>

                <div class="overflow-x-auto shadow-md sm:rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Item Name / Description</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-20">Unit</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-24">Qty Req.</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Activity / Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="item-list">
                            @foreach ($requisition->items as $index => $item)
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                
                                {{-- Item Name/Description (Input Field) --}}
                                <td class="px-3 py-2">
                                    {{-- Hidden fields to satisfy controller validation for existing items --}}
                                    <input type="hidden" name="items[{{ $item->id }}][boq_material_id]" value="{{ $item->boq_material_id }}">
                                    <input type="hidden" name="items[{{ $item->id }}][boq_activity_id]" value="{{ $item->boq_activity_id }}">
                                    
                                    <input type="text" name="items[{{ $item->id }}][item_name]" 
                                           value="{{ old('items.' . $item->id . '.item_name', $item->item_name) }}"
                                           required
                                           class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-1.5">
                                    
                                    @error('items.' . $item->id . '.item_name') 
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                                    @enderror
                                </td>
                                
                                {{-- Unit (Read-only) --}}
                                <td class="px-3 py-2 text-center text-sm font-semibold text-gray-700">
                                    <input type="hidden" name="items[{{ $item->id }}][unit]" value="{{ $item->unit }}">
                                    {{ $item->unit }}
                                </td>

                                {{-- Quantity Requested (Input Field) --}}
                                <td class="px-3 py-2">
                                    <input type="number" step="0.01" min="0" name="items[{{ $item->id }}][qty_requested]"
                                           value="{{ old('items.' . $item->id . '.qty_requested', $item->qty_requested) }}"
                                           required
                                           class="w-full text-sm text-right border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-1.5">
                                    
                                    {{-- FIX: Correctly display the validation error for this specific item quantity --}}
                                    @error('items.' . $item->id . '.qty_requested') 
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                                    @enderror
                                </td>
                                
                                {{-- Activity / Remarks (Input Field) --}}
                                <td class="px-3 py-2">
                                    <input type="text" name="items[{{ $item->id }}][remarks]"
                                           value="{{ old('items.' . $item->id . '.remarks', $item->boqActivity->name ?? '') }}"
                                           placeholder="Linked Activity/Justification"
                                           class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-1.5">
                                    @error('items.' . $item->id . '.remarks') 
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                                    @enderror
                                </td>
                            </tr>
                            @endforeach
                            
                        </tbody>
                    </table>
                </div>
                
                <p class="mt-4 text-sm text-gray-500 italic">Note: Editing the item list requires updating the quantity, name, and justification/remarks for existing items. Adding or removing items usually requires a more dynamic tool (like Livewire/Vue/React).</p>
            </div>

            {{-- 3. OVERALL JUSTIFICATION --}}
            <div class="pt-4 border-t border-gray-200">
                <label for="justification" class="block text-sm font-medium text-gray-700">Overall Justification</label>
                <textarea name="justification" id="justification" rows="4"
                          required
                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('justification', $requisition->justification) }}</textarea>
                @error('justification') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- 4. Submit Button --}}
            <div class="mt-8 pt-6 border-t border-gray-200">
                <button type="submit" class="w-full inline-flex justify-center py-3 px-6 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Update Purchase Requisition
                </button>
            </div>
        </div>
    </form>
</div>

@endsection