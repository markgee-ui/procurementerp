@php
    // QS is responsible for Stage 1 approval
    $isReadyForQSApproval = $requisition->status === 'Pending' && $requisition->current_stage === 1;
@endphp

@if ($isReadyForQSApproval)
<div class="p-6 bg-yellow-50 border-2 border-yellow-300 rounded-lg shadow-md mt-6">
    <h3 class="text-xl font-bold text-yellow-800 mb-4">
        Action Required: Quantity Surveyor Approval (Stage 1)
    </h3>
    
    <div class="flex flex-col md:flex-row space-y-3 md:space-y-0 md:space-x-4">
        
        {{-- 1. Approve Button --}}
        <form action="{{ route('qs.requisitions.approve', $requisition) }}" method="POST" class="w-full md:w-1/2">
            @csrf
            <button type="submit" 
                    onclick="return confirm('Confirm approval? The PR will now be sent to the Office Project Manager (Stage 2).');"
                    class="w-full px-6 py-3 text-white bg-green-600 rounded-lg font-semibold hover:bg-green-700 transition duration-150 shadow-lg">
                Approve & Send to PM
            </button>
        </form>

        {{-- 2. Reject Button (Triggers Modal for Notes) --}}
        <button type="button" 
                class="w-full md:w-1/2 px-6 py-3 text-white bg-red-600 rounded-lg font-semibold hover:bg-red-700 transition duration-150 shadow-lg"
                onclick="document.getElementById('qs-reject-modal').classList.remove('hidden')">
            Reject Requisition
        </button>
    </div>
</div>

{{-- Rejection Modal --}}
<div id="qs-reject-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all max-w-lg w-full p-6 z-50">
            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                Reject Purchase Requisition #{{ $requisition->id }}
            </h3>
            <p class="text-sm text-gray-500 mt-2">You must provide notes explaining the reason for rejection.</p>
            
            <form action="{{ route('qs.requisitions.reject', $requisition) }}" method="POST" class="mt-4">
                @csrf
                
                <div class="mb-4">
                    <label for="rejection_notes" class="block text-sm font-medium text-gray-700">Rejection Notes</label>
                    <textarea name="rejection_notes" id="rejection_notes" rows="4" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-red-500 focus:border-red-500 text-sm"
                        placeholder="State the specific reasons for rejecting this request..."></textarea>
                    
                    @error('rejection_notes') 
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                    @enderror
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="document.getElementById('qs-reject-modal').classList.add('hidden')"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                        Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endif