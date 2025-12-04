{{-- resources/views/qs/boq/material_row.blade.php --}}

<tr data-row-id="{{ $i ?? 1 }}">
    <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $i ?? 1 }}</td>
    <td class="px-6 py-4 whitespace-nowrap"><input type="text" name="materials[{{ $i ?? 1 }}][item]" class="w-full text-sm border-gray-300 rounded-md p-1" required></td>
    <td class="px-6 py-4 whitespace-nowrap"><input type="text" name="materials[{{ $i ?? 1 }}][specs]" class="w-full text-sm border-gray-300 rounded-md p-1"></td>
    <td class="px-3 py-4 whitespace-nowrap"><input type="text" name="materials[{{ $i ?? 1 }}][unit]" class="w-full text-sm border-gray-300 rounded-md p-1" placeholder="e.g., Bag, Ton"></td>
    <td class="px-3 py-4 whitespace-nowrap"><input type="number" name="materials[{{ $i ?? 1 }}][qty]" class="w-full text-sm border-gray-300 rounded-md p-1" required min="1"></td>
    <td class="px-6 py-4 whitespace-nowrap"><input type="text" name="materials[{{ $i ?? 1 }}][remarks]" class="w-full text-sm border-gray-300 rounded-md p-1"></td>
    <td class="px-2 py-4 whitespace-nowrap">
        <button type="button" class="text-red-600 hover:text-red-900 remove-row-btn" data-row="{{ $i ?? 1 }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </td>
</tr>