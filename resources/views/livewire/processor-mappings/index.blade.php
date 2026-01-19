<section>
    <x-alerts.success />

    <div class="flex flex-grow gap-x-4 mb-4">
        <flux:button href="{{ route('processor-mappings.create') }}" variant="filled">{{ __('Add Processor Mapping') }}</flux:button>
    </div>

    {{-- Search and Filters --}}
    <div class="mb-4 space-y-4">
        <div class="flex gap-4">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('Search by processor, source field, or target field...') }}"
                class="flex-1"
            />
        </div>

        <div class="flex gap-4">
            <flux:select wire:model.live="processor" placeholder="{{ __('All Processors') }}">
                <option value="">{{ __('All Processors') }}</option>
                @foreach ($processors as $proc)
                    <option value="{{ $proc }}">{{ ucfirst($proc) }}</option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="is_active" placeholder="{{ __('All Status') }}">
                <option value="">{{ __('All Status') }}</option>
                <option value="1">{{ __('Active') }}</option>
                <option value="0">{{ __('Inactive') }}</option>
            </flux:select>
        </div>
    </div>

    {{-- Table --}}
    <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
        <table class="w-full text-sm text-left rtl:text-right text-body">
            <thead class="text-sm text-body bg-neutral-secondary-soft border-b rounded-base border-default">
                <tr>
                    <th scope="col" class="px-6 py-3 font-medium cursor-pointer" wire:click="sortBy('processor')">
                        <div class="flex items-center gap-2">
                            Processor
                            @if ($sortField === 'processor')
                                @if ($sortDirection === 'asc')
                                    ↑
                                @else
                                    ↓
                                @endif
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium cursor-pointer" wire:click="sortBy('source_field')">
                        <div class="flex items-center gap-2">
                            Source Field
                            @if ($sortField === 'source_field')
                                @if ($sortDirection === 'asc')
                                    ↑
                                @else
                                    ↓
                                @endif
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium cursor-pointer" wire:click="sortBy('target_field')">
                        <div class="flex items-center gap-2">
                            Target Field
                            @if ($sortField === 'target_field')
                                @if ($sortDirection === 'asc')
                                    ↑
                                @else
                                    ↓
                                @endif
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium cursor-pointer" wire:click="sortBy('transformation_type')">
                        <div class="flex items-center gap-2">
                            Type
                            @if ($sortField === 'transformation_type')
                                @if ($sortDirection === 'asc')
                                    ↑
                                @else
                                    ↓
                                @endif
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium cursor-pointer" wire:click="sortBy('priority')">
                        <div class="flex items-center gap-2">
                            Priority
                            @if ($sortField === 'priority')
                                @if ($sortDirection === 'asc')
                                    ↑
                                @else
                                    ↓
                                @endif
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium cursor-pointer" wire:click="sortBy('is_required')">
                        <div class="flex items-center gap-2">
                            Required
                            @if ($sortField === 'is_required')
                                @if ($sortDirection === 'asc')
                                    ↑
                                @else
                                    ↓
                                @endif
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium cursor-pointer" wire:click="sortBy('is_active')">
                        <div class="flex items-center gap-2">
                            Status
                            @if ($sortField === 'is_active')
                                @if ($sortDirection === 'asc')
                                    ↑
                                @else
                                    ↓
                                @endif
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mappings as $mapping)
                    <tr class="border-b hover:bg-neutral-secondary-soft/50">
                        <td class="px-6 py-4 font-medium">
                            <flux:badge>{{ ucfirst($mapping->processor) }}</flux:badge>
                        </td>
                        <td class="px-6 py-4 font-medium">
                            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">{{ $mapping->source_field }}</code>
                        </td>
                        <td class="px-6 py-4 font-medium">
                            <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">{{ $mapping->target_field }}</code>
                        </td>
                        <td class="px-6 py-4">
                            <flux:badge>{{ ucfirst($mapping->transformation_type) }}</flux:badge>
                        </td>
                        <td class="px-6 py-4">
                            {{ $mapping->priority }}
                        </td>
                        <td class="px-6 py-4">
                            @if ($mapping->is_required)
                                <flux:badge variant="success">Required</flux:badge>
                            @else
                                <span class="text-gray-400">Optional</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if ($mapping->is_active)
                                <flux:badge variant="success">Active</flux:badge>
                            @else
                                <flux:badge variant="danger">Inactive</flux:badge>
                            @endif
                        </td>
                        <td class="px-6 py-4 space-x-2">
                            <flux:button href="{{ route('processor-mappings.edit', $mapping) }}" variant="filled">
                                {{ __('Edit') }}
                            </flux:button>
                            <flux:button 
                                wire:confirm="{{ __('Are you sure you want to delete this processor mapping?') }}"
                                wire:click="delete({{ $mapping->id }})" 
                                variant="danger" 
                                type="button">
                                {{ __('Delete') }}
                            </flux:button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            {{ __('No processor mappings found.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if ($mappings->hasPages())
            <div class="p-4">
                {{ $mappings->links() }}
            </div>
        @endif
    </div>

</section>
