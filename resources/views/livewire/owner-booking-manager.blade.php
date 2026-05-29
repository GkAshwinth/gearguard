<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
            Booking Management
            @if($pendingCount > 0)
                <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-0.5 rounded-full">{{ $pendingCount }} Pending</span>
            @endif
        </h2>
        
        <div class="flex bg-gray-100 p-1 rounded-lg self-start">
            <button wire:click="setFilter('all')" class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $filter === 'all' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">All</button>
            <button wire:click="setFilter('pending')" class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $filter === 'pending' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">Pending</button>
            <button wire:click="setFilter('approved')" class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $filter === 'approved' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">Approved</button>
            <button wire:click="setFilter('rejected')" class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $filter === 'rejected' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">Rejected</button>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden relative min-h-[400px]">
        <div wire:loading class="absolute inset-0 bg-white/70 backdrop-blur-sm z-10 flex items-center justify-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-sky-600"></div>
        </div>
        
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                <tr>
                    <th class="px-4 py-3 text-left">Equipment</th>
                    <th class="px-4 py-3 text-left">Customer</th>
                    <th class="px-4 py-3 text-left">Dates</th>
                    <th class="px-4 py-3 text-right">Total</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($bookings as $booking)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $booking->equipment->image_url }}" class="w-10 h-10 object-cover rounded-lg" alt="">
                            <div>
                                <p class="font-medium text-gray-900">{{ $booking->equipment->name }}</p>
                                <p class="text-xs text-gray-500">Booking #{{ $booking->id }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-500">
                        <p class="font-medium text-gray-900">{{ $booking->user->name }}</p>
                        <p class="text-xs">{{ $booking->user->email }}</p>
                    </td>
                    <td class="px-4 py-3 text-gray-500">
                        {{ $booking->start_date->format('M d') }} → {{ $booking->end_date->format('M d, Y') }}
                    </td>
                    <td class="px-4 py-3 text-right font-medium text-gray-900">
                        LKR {{ number_format($booking->total_cost) }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="bg-{{ $booking->status_color }}-100 text-{{ $booking->status_color }}-800 text-xs font-semibold px-2 py-1 rounded-full capitalize">
                            {{ $booking->status }}
                        </span>
                        @if($booking->status === 'rejected' && $booking->cancellation_reason)
                            <p class="text-xs text-red-500 mt-1 max-w-[150px] truncate mx-auto" title="{{ $booking->cancellation_reason }}">
                                {{ $booking->cancellation_reason }}
                            </p>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        @if($booking->status === 'pending')
                            <div class="flex justify-end gap-2">
                                <button wire:click="approve({{ $booking->id }})" class="bg-green-600 text-white text-xs px-3 py-1.5 rounded hover:bg-green-700 transition font-medium">
                                    Approve
                                </button>
                                <button wire:click="initiateReject({{ $booking->id }})" class="bg-red-50 text-red-600 border border-red-200 text-xs px-3 py-1.5 rounded hover:bg-red-100 transition font-medium">
                                    Reject
                                </button>
                            </div>
                        @elseif($booking->status === 'approved')
                            <button wire:click="markCompleted({{ $booking->id }})" class="text-xs text-blue-600 hover:underline">Mark Completed</button>
                        @else
                            <span class="text-gray-300 text-xs">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                        No bookings found for the selected filter.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $bookings->links() }}
    </div>

    <!-- Rejection Modal -->
    <div x-data="{ open: @entangle('rejectingBookingId').live }" 
         x-show="open" 
         x-on:close-modal.window="open = false"
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                 aria-hidden="true" 
                 @click="open = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="open" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Reject Booking
                            </h3>
                            <div class="mt-4">
                                <label for="cancellationReason" class="block text-sm font-medium text-gray-700">Reason for rejection (sent to customer)</label>
                                <textarea wire:model="cancellationReason" id="cancellationReason" rows="3" class="shadow-sm focus:ring-sky-500 focus:border-sky-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md" placeholder="e.g., Equipment is undergoing maintenance..."></textarea>
                                @error('cancellationReason') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="confirmReject" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Confirm Rejection
                    </button>
                    <button wire:click="cancelReject" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
