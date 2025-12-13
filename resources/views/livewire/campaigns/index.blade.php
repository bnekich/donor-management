<section>
    <x-alerts.success />

    <flux:button href="{{ route('campaigns.create') }}" variant="filled" class="mb-4">{{ __('Create Campaign') }}
    </flux:button>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Name
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Description
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Participating Donors
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($campaigns as $campaign)
                    <tr
                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                        <th scope="row"
                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $campaign->name }}
                        </th>
                        <td class="px-6 py-4">
                            {{ $campaign->description }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $campaign->donors_count }}
                        </td>
                        <td class="px-6 py-4 space-x-2">
                            <flux:button href="{{ route('campaigns.edit', $campaign) }}" variant="filled">
                                {{ __('Edit') }}</flux:button>
                            {{-- <flux:button
                                wire:confirm="Are you sure you want to delete category with {{ $category->tasks_count }} assigned tasks?"
                                wire:click="delete({{ $category->id }})" variant="danger" type="button">
                                {{ __('Delete') }}</flux:button> --}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($campaigns->hasPages())
        <div class="mt-5">
            {{ $campaigns->links() }}
        </div>
    @endif
</section>
