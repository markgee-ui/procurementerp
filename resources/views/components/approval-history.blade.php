<div class="space-y-4">
    @forelse ($approvals as $approval)
        <div class="flex items-start space-x-4 p-3 rounded-lg 
             @if ($approval->status === 'approved') 
                bg-green-50 border border-green-200
             @elseif ($approval->status === 'rejected')
                bg-red-50 border border-red-200
             @else
                bg-gray-50 border border-gray-200
             @endif">

            {{-- Icon based on status --}}
            <div class="flex-shrink-0">
                @if ($approval->status === 'approved')
                    <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @elseif ($approval->status === 'rejected')
                    <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @else
                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @endif
            </div>

            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-gray-900">
                    {{-- Display the status and the stage --}}
                    <span class="font-bold uppercase 
                        @if ($approval->status === 'approved') text-green-600 @elseif ($approval->status === 'rejected') text-red-600 @else text-gray-600 @endif">
                        {{ $approval->status }}
                    </span> 
                    for Stage {{ $approval->stage }}
                    ({{ $approval->user?->name ?? 'System/Unknown User' }})
                </p>
                <p class="text-xs text-gray-500 mt-1">
                    @if ($approval->notes)
                        Notes: <span class="text-gray-700 italic">"{{ $approval->notes }}"</span>
                    @else
                        No notes provided.
                    @endif
                </p>
                <p class="text-xs text-gray-400 mt-1">
                    Date: {{ $approval->created_at->format('M d, Y H:i A') }}
                </p>
            </div>
        </div>
    @empty
        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-700">
            No formal approval records found yet. Requisition might be newly submitted or manually approved.
        </div>
    @endforelse
</div>