<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Inventory</h1>
            <p class="text-gray-500 text-sm">Manage all your rental equipment</p>
        </div>
        <button wire:click="openCreateModal" class="bg-sky-600 text-white px-5 py-2.5 rounded-xl font-medium hover:bg-sky-700 transition text-sm flex items-center gap-2">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Equipment
        </button>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden relative min-h-[400px]">
        <div wire:loading.delay class="absolute inset-0 bg-white/70 backdrop-blur-sm z-10 flex items-center justify-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-sky-600"></div>
        </div>

        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                <tr>
                    <th class="px-4 py-3 text-left">Item</th>
                    <th class="px-4 py-3 text-left">Category</th>
                    <th class="px-4 py-3 text-right">Daily Rate</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Bookings</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($equipmentList as $item)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $item->image_url }}" class="w-10 h-10 object-cover rounded-lg" alt="">
                            <span class="font-medium text-gray-900">{{ $item->name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $item->category }}</td>
                    <td class="px-4 py-3 text-right font-medium">LKR {{ number_format($item->getRawOriginal('daily_rate')) }}</td>
                    <td class="px-4 py-3 text-center">
                        @php
                            $sc = ['available'=>'green','rented'=>'yellow','maintenance'=>'red'];
                            $s = $sc[$item->status] ?? 'gray';
                        @endphp
                        <span class="bg-{{ $s }}-100 text-{{ $s }}-800 text-xs font-semibold px-2 py-1 rounded-full capitalize">
                            {{ $item->status }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center text-gray-500">{{ $item->bookings_count }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end gap-2">
                            <button wire:click="openEditModal({{ $item->id }})" class="text-xs text-sky-600 border border-sky-200 px-3 py-1 rounded-lg hover:bg-sky-50 transition">
                                Edit
                            </button>
                            <button wire:click="delete({{ $item->id }})" wire:confirm="Are you sure you want to delete {{ $item->name }}? This will soft delete the item and it cannot be undone." class="text-xs text-red-500 border border-red-200 px-3 py-1 rounded-lg hover:bg-red-50 transition">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                        No equipment found in inventory.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $equipmentList->links() }}
    </div>

    <!-- Create/Edit Modal -->
    <div x-data="{ open: @entangle('isModalOpen').live }" 
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
                 class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                
                <form wire:submit="save">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-xl leading-6 font-bold text-gray-900 mb-6" id="modal-title">
                            {{ $isEditing ? 'Edit Equipment' : 'Add New Equipment' }}
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Left Column -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Equipment Name</label>
                                    <input type="text" wire:model="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
                                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Category</label>
                                    <select wire:model="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
                                        <option value="">Select Category</option>
                                        <option value="Cameras">Cameras</option>
                                        <option value="Lenses">Lenses</option>
                                        <option value="Lighting">Lighting</option>
                                        <option value="Audio">Audio</option>
                                        <option value="Accessories">Accessories</option>
                                    </select>
                                    @error('category') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Daily Rate (LKR)</label>
                                    <input type="number" step="0.01" wire:model="daily_rate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
                                    @error('daily_rate') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <select wire:model="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
                                        <option value="available">Available</option>
                                        <option value="rented">Rented</option>
                                        <option value="maintenance">Maintenance</option>
                                    </select>
                                    @error('status') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            
                            <!-- Right Column -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Description</label>
                                    <textarea wire:model="description" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm"></textarea>
                                    @error('description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Equipment Image</label>
                                    
                                    @if ($image)
                                        <div class="mb-3">
                                            <p class="text-xs text-gray-500 mb-1">New image preview:</p>
                                            <img src="{{ $image->temporaryUrl() }}" class="h-32 w-full object-cover rounded-lg border border-gray-200">
                                        </div>
                                    @elseif ($isEditing && $existing_image_url)
                                        <div class="mb-3">
                                            <p class="text-xs text-gray-500 mb-1">Current image:</p>
                                            <img src="{{ $existing_image_url }}" class="h-32 w-full object-cover rounded-lg border border-gray-200">
                                        </div>
                                    @endif
                                    
                                    <input type="file" wire:model="image" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                                    <p class="text-xs text-gray-400 mt-1">JPEG, PNG, WEBP up to 2MB</p>
                                    
                                    <div wire:loading wire:target="image" class="text-sm text-sky-600 mt-2 flex items-center gap-2">
                                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-sky-600"></div> Uploading...
                                    </div>
                                    
                                    @error('image') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-xl">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-sky-600 text-base font-medium text-white hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 sm:ml-3 sm:w-auto sm:text-sm">
                            {{ $isEditing ? 'Save Changes' : 'Create Equipment' }}
                        </button>
                        <button wire:click="closeModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
