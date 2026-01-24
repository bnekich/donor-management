<section>
    <x-alerts.success />

    <div class="mb-4 flex flex-col gap-4">
        <!-- Search and Filters -->
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <flux:field>
                    <flux:label>Search</flux:label>
                    <flux:input wire:model.live.debounce.300ms="search" placeholder="Search by reference, transaction ID, or donor name..." />
                </flux:field>
            </div>
        </div>

        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <flux:field>
                    <flux:label>Filter by Campaign</flux:label>
                    <flux:select wire:model.live="selectedCampaign" placeholder="All Campaigns">
                        <option value="">All Campaigns</option>
                        @foreach ($campaigns as $campaign)
                            <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>

            <div class="flex-1">
                <flux:field>
                    <flux:label>Filter by Donor</flux:label>
                    <flux:select wire:model.live="selectedDonor" placeholder="All Donors">
                        <option value="">All Donors</option>
                        @foreach ($donors as $donor)
                            <option value="{{ $donor->id }}">
                                {{ $donor->donorDetail?->first_name }} {{ $donor->donorDetail?->last_name }} ({{ $donor->email }})
                            </option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>
        </div>
    </div>

    <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
        <table class="w-full text-sm text-left rtl:text-right text-body">
            <thead class="text-sm text-body bg-neutral-secondary-soft border-b rounded-base border-default">
                <tr>
                    <th scope="col" class="px-6 py-3 font-medium cursor-pointer hover:bg-neutral-secondary-soft/50"
                        wire:click="sortBy('transaction_date')">
                        <div class="flex items-center gap-2">
                            Date
                            @if ($sortField === 'transaction_date')
                                <span class="text-xs">
                                    {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                </span>
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium cursor-pointer hover:bg-neutral-secondary-soft/50"
                        wire:click="sortBy('amount')">
                        <div class="flex items-center gap-2">
                            Amount
                            @if ($sortField === 'amount')
                                <span class="text-xs">
                                    {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                </span>
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">Donor</th>
                    <th scope="col" class="px-6 py-3 font-medium">Campaign</th>
                    <th scope="col" class="px-6 py-3 font-medium">Payment Method</th>
                    <th scope="col" class="px-6 py-3 font-medium">Reference</th>
                    <th scope="col" class="px-6 py-3 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($donations as $donation)
                    <tr class="border-b hover:bg-neutral-secondary-soft/50 {{ $selectedDonationId === $donation->id ? 'bg-neutral-secondary-soft/30' : '' }}">
                        <td class="px-6 py-4 font-medium">
                            {{ $donation->transaction_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 font-medium">
                            ${{ number_format($donation->amount, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            @if ($donation->donor)
                                {{ $donation->donor->donorDetail?->first_name }} {{ $donation->donor->donorDetail?->last_name }}
                                <div class="text-xs text-gray-500">{{ $donation->donor->email }}</div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if ($donation->campaign)
                                <flux:badge>{{ $donation->campaign->name }}</flux:badge>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            {{ ucfirst(str_replace('_', ' ', $donation->payment_method)) }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $donation->reference_number ?? $donation->transaction_id ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <flux:button wire:click="selectDonation({{ $donation->id }})" variant="filled" type="button">
                                {{ __('View Details') }}
                            </flux:button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            No donations found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if ($donations->hasPages())
            <div class="p-4">
                {{ $donations->links() }}
            </div>
        @endif
    </div>

    <!-- Detail Modal -->
    @if ($selectedDonation)
        <flux:modal name="donation-detail" class="max-w-4xl" wire:model="showDetailModal">
            <flux:heading>Donation Details</flux:heading>

            <div class="space-y-6">
                <!-- Basic Information -->
                <div>
                    <flux:heading size="sm" class="mb-4">Basic Information</flux:heading>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <flux:field>
                                <flux:label>Donor</flux:label>
                                <flux:input value="{{ $selectedDonation->donor ? trim(($selectedDonation->donor->first_name ?? '') . ' ' . ($selectedDonation->donor->last_name ?? '')) : '-' }}" readonly />
                            </flux:field>
                        </div>
                        <div>
                            <flux:field>
                                <flux:label>Transaction Date</flux:label>
                                <flux:input value="{{ $selectedDonation->transaction_date->format('F d, Y') }}" readonly />
                            </flux:field>
                        </div>
                        <div>
                            <flux:field>
                                <flux:label>Amount</flux:label>
                                <flux:input value="${{ number_format($selectedDonation->amount, 2) }}" readonly />
                            </flux:field>
                        </div>
                        <div>
                            <flux:field>
                                <flux:label>Processor Fee</flux:label>
                                <flux:input value="{{ $selectedDonation->processor_fee ? '$' . number_format($selectedDonation->processor_fee, 2) : '-' }}" readonly />
                            </flux:field>
                        </div>
                        <div>
                            <flux:field>
                                <flux:label>Net Amount</flux:label>
                                <flux:input value="{{ $selectedDonation->net_amount ? '$' . number_format($selectedDonation->net_amount, 2) : '-' }}" readonly />
                            </flux:field>
                        </div>
                        <div>
                            <flux:field>
                                <flux:label>Payment Method</flux:label>
                                <flux:input value="{{ ucfirst(str_replace('_', ' ', $selectedDonation->payment_method)) }}" readonly />
                            </flux:field>
                        </div>
                        <div>
                            <flux:field>
                                <flux:label>Status</flux:label>
                                <flux:input value="{{ $selectedDonation->status ?? '-' }}" readonly />
                            </flux:field>
                        </div>
                        <div>
                            <flux:field>
                                <flux:label>Reference Number</flux:label>
                                <flux:input value="{{ $selectedDonation->reference_number ?? '-' }}" readonly />
                            </flux:field>
                        </div>
                        <div>
                            <flux:field>
                                <flux:label>Transaction ID</flux:label>
                                <flux:input value="{{ $selectedDonation->transaction_id ?? '-' }}" readonly />
                            </flux:field>
                        </div>
                        <div>
                            <flux:field>
                                <flux:label>Processor</flux:label>
                                <flux:input value="{{ $selectedDonation->processor ?? '-' }}" readonly />
                            </flux:field>
                        </div>
                        <div>
                            <flux:field>
                                <flux:label>Is Recurring</flux:label>
                                <flux:input value="{{ $selectedDonation->is_recurring ? 'Yes' : 'No' }}" readonly />
                            </flux:field>
                        </div>
                    </div>
                    @if ($selectedDonation->notes)
                        <div class="mt-4">
                            <flux:field>
                                <flux:label>Notes</flux:label>
                                <flux:textarea value="{{ $selectedDonation->notes }}" readonly />
                            </flux:field>
                        </div>
                    @endif
                </div>

                <!-- Donor Information -->
                @if ($selectedDonation->donor)
                    <div>
                        <flux:heading size="sm" class="mb-4">Donor Information</flux:heading>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <flux:field>
                                    <flux:label>Name</flux:label>
                                    <flux:input value="{{ $selectedDonation->donor->first_name }} {{ $selectedDonation->donor->last_name }}" readonly />
                                </flux:field>
                            </div>
                            <div>
                                <flux:field>
                                    <flux:label>Email</flux:label>
                                    <flux:input value="{{ $selectedDonation->donor->email }}" readonly />
                                </flux:field>
                            </div>
                            <div>
                                <flux:field>
                                    <flux:label>Phone</flux:label>
                                    <flux:input value="{{ $selectedDonation->donor->phone ?? '-' }}" readonly />
                                </flux:field>
                            </div>
                            @if ($selectedDonation->donor->donorDetail)
                                <div>
                                    <flux:field>
                                        <flux:label>Birthday</flux:label>
                                        <flux:input value="{{ $selectedDonation->donor->donorDetail->birthday?->format('F d, Y') ?? '-' }}" readonly />
                                    </flux:field>
                                </div>
                                <div>
                                    <flux:field>
                                        <flux:label>Occupation</flux:label>
                                        <flux:input value="{{ $selectedDonation->donor->donorDetail->occupation ?? '-' }}" readonly />
                                    </flux:field>
                                </div>
                            @endif
                        </div>
                        @if ($selectedDonation->donor->address_line1)
                            <div class="mt-4">
                                <flux:field>
                                    <flux:label>Address</flux:label>
                                    <flux:input value="{{ $selectedDonation->donor->address_line1 }}" readonly />
                                    <flux:input value="{{ $selectedDonation->donor->address_line2 }}" readonly />
                                    <flux:input value="{{ $selectedDonation->donor->city }}" readonly />
                                    <flux:input value="{{ $selectedDonation->donor->state }}" readonly />
                                    <flux:input value="{{ $selectedDonation->donor->zip }}" readonly />
                                </flux:field>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Campaign Information -->
                @if ($selectedDonation->campaign)
                    <div>
                        <flux:heading size="sm" class="mb-4">Campaign Information</flux:heading>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <flux:field>
                                    <flux:label>Campaign Name</flux:label>
                                    <flux:input value="{{ $selectedDonation->campaign->name }}" readonly />
                                </flux:field>
                            </div>
                            <div>
                                <flux:field>
                                    <flux:label>Campaign Code</flux:label>
                                    <flux:input value="{{ $selectedDonation->campaign->code ?? '-' }}" readonly />
                                </flux:field>
                            </div>
                            @if ($selectedDonation->campaign->description)
                                <div class="md:col-span-2">
                                    <flux:field>
                                        <flux:label>Description</flux:label>
                                        <flux:textarea value="{{ $selectedDonation->campaign->description }}" readonly />
                                    </flux:field>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Account/Fund Information -->
                @if ($selectedDonation->account)
                    <div>
                        <flux:heading size="sm" class="mb-4">Account/Fund Information</flux:heading>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <flux:field>
                                    <flux:label>Account Name</flux:label>
                                    <flux:input value="{{ $selectedDonation->account->name }}" readonly />
                                </flux:field>
                            </div>
                            <div>
                                <flux:field>
                                    <flux:label>Account Code</flux:label>
                                    <flux:input value="{{ $selectedDonation->account->code ?? '-' }}" readonly />
                                </flux:field>
                            </div>
                            @if ($selectedDonation->account->description)
                                <div class="md:col-span-2">
                                    <flux:field>
                                        <flux:label>Description</flux:label>
                                        <flux:textarea value="{{ $selectedDonation->account->description }}" readonly />
                                    </flux:field>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <div class="flex justify-end space-x-2 rtl:space-x-reverse mt-6">
                <flux:button wire:click="closeDetail" variant="ghost">Close</flux:button>
            </div>
        </flux:modal>
    @endif
</section>
