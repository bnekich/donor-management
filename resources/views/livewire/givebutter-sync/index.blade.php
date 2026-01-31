<section>
        <x-alerts.success />

        <div class="mb-4 flex flex-wrap items-center gap-4">
            <flux:heading size="lg">{{ __('Givebutter Contacts Sync') }}</flux:heading>
            @if (!$this->apiConnected)
                <flux:badge variant="danger">{{ __('API key missing or unreachable') }}</flux:badge>
            @else
                <flux:badge variant="success">{{ __('API connected') }}</flux:badge>
            @endif
        </div>

        <p class="mb-4 text-sm text-body">
            {{ __('Fetch contacts from Givebutter into staging (raw JSON), then optionally load them into Donors and Donor Details.') }}
        </p>

        {{-- Actions --}}
        <div class="mb-6 flex flex-wrap gap-4">
            <flux:button
                wire:click="startFetchAndStage"
                variant="filled"
                :disabled="!$this->apiConnected"
            >
                {{ __('Sync from Givebutter (fetch & stage)') }}
            </flux:button>

            <div class="flex flex-wrap items-center gap-2">
                <flux:select wire:model.live="loadFromSyncRunId" placeholder="{{ __('All unloaded staging') }}" class="min-w-[200px]">
                    <option value="">{{ __('All unloaded staging') }}</option>
                    @foreach ($runs->getCollection() as $run)
                        <option value="{{ $run->id }}">
                            Run #{{ $run->id }} ({{ $run->contacts_staged }} staged) {{ $run->started_at?->format('Y-m-d H:i') }}
                        </option>
                    @endforeach
                </flux:select>
                <flux:button
                    wire:click="startLoadFromStaging"
                    variant="outline"
                >
                    {{ __('Load from staging') }}
                </flux:button>
            </div>
        </div>

        {{-- Sync runs table --}}
        <flux:subheading class="mb-2">{{ __('Sync runs') }}</flux:subheading>
        <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default mb-6">
            <table class="w-full text-sm text-left rtl:text-right text-body">
                <thead class="text-sm text-body bg-neutral-secondary-soft border-b rounded-base border-default">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-medium">ID</th>
                        <th scope="col" class="px-6 py-3 font-medium">Started</th>
                        <th scope="col" class="px-6 py-3 font-medium">Finished</th>
                        <th scope="col" class="px-6 py-3 font-medium">Status</th>
                        <th scope="col" class="px-6 py-3 font-medium">Pages</th>
                        <th scope="col" class="px-6 py-3 font-medium">Staged</th>
                        <th scope="col" class="px-6 py-3 font-medium">Loaded</th>
                        <th scope="col" class="px-6 py-3 font-medium">Error</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($runs as $run)
                        <tr class="border-b hover:bg-neutral-secondary-soft/50">
                            <td class="px-6 py-4 font-medium">{{ $run->id }}</td>
                            <td class="px-6 py-4">{{ $run->started_at?->format('Y-m-d H:i:s') ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $run->finished_at?->format('Y-m-d H:i:s') ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @if ($run->status === 'running')
                                    <flux:badge variant="warning">{{ __('Running') }}</flux:badge>
                                @elseif ($run->status === 'success')
                                    <flux:badge variant="success">{{ __('Success') }}</flux:badge>
                                @elseif ($run->status === 'failed')
                                    <flux:badge variant="danger">{{ __('Failed') }}</flux:badge>
                                @else
                                    <flux:badge>{{ $run->status }}</flux:badge>
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ $run->pages_fetched }}</td>
                            <td class="px-6 py-4">{{ $run->contacts_staged }}</td>
                            <td class="px-6 py-4">{{ $run->contacts_loaded ?? '-' }}</td>
                            <td class="px-6 py-4 text-red-600 dark:text-red-400 max-w-xs truncate" title="{{ $run->error_message }}">
                                {{ $run->error_message ? Str::limit($run->error_message, 40) : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                {{ __('No sync runs yet.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($runs->hasPages())
                <div class="p-4">{{ $runs->links() }}</div>
            @endif
        </div>

        {{-- Staging summary --}}
        <flux:subheading class="mb-2">{{ __('Staging (raw JSON)') }}</flux:subheading>
        <p class="mb-2 text-sm text-body">
            {{ __('Total staged:') }} <strong>{{ $stagingCount }}</strong>.
            {{ __('Not yet loaded:') }} <strong>{{ $stagingNotLoadedCount }}</strong>.
        </p>

        {{-- Staging preview (key fields + raw payload toggle) --}}
        <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
            <table class="w-full text-sm text-left rtl:text-right text-body">
                <thead class="text-sm text-body bg-neutral-secondary-soft border-b rounded-base border-default">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-medium">GB ID</th>
                        <th scope="col" class="px-6 py-3 font-medium">Name</th>
                        <th scope="col" class="px-6 py-3 font-medium">Email</th>
                        <th scope="col" class="px-6 py-3 font-medium">Loaded</th>
                        <th scope="col" class="px-6 py-3 font-medium">Sync run</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stagingPreview as $row)
                        <tr class="border-b hover:bg-neutral-secondary-soft/50">
                            <td class="px-6 py-4 font-medium">{{ $row->givebutter_contact_id }}</td>
                            <td class="px-6 py-4">{{ ($row->payload['first_name'] ?? '') . ' ' . ($row->payload['last_name'] ?? '') }}</td>
                            <td class="px-6 py-4">{{ $row->payload['primary_email'] ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $row->loaded_at ? $row->loaded_at->format('Y-m-d H:i') : '-' }}</td>
                            <td class="px-6 py-4">{{ $row->sync_run_id ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                {{ __('No staging rows. Run "Sync from Givebutter" first.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <p class="mt-2 text-xs text-body">
            {{ __('Table schema: donors (processor_id, processor, first_name, last_name, email, phone, address_line1/2, city, county, state, zip, country); donor_details (donor_id, birthday, occupation, organization_id, can_be_contacted). Staging stores full contact as JSON in givebutter_contacts_staging.payload.') }}
        </p>
    </section>
