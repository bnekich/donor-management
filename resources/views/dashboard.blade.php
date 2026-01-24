<x-layouts.app :title="__('Dashboard')">
    @vite('resources/js/charts.js')
    <div x-data="{ selectedChart: 'pledges' }" class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div 
                @click="selectedChart = 'pledges'"
                :class="selectedChart === 'pledges' ? 'ring-2 ring-blue-500' : ''"
                class="cursor-pointer transition-all hover:shadow-lg rounded-lg"
            >
                <livewire:dashboard.pending-pledges-widget />
            </div>
            <div 
                @click="selectedChart = 'donations'"
                :class="selectedChart === 'donations' ? 'ring-2 ring-blue-500' : ''"
                class="cursor-pointer transition-all hover:shadow-lg rounded-lg"
            >
                <livewire:dashboard.donations-by-campaign-widget />
            </div>
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern
                    class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
        </div>
        <div 
            x-show="selectedChart === 'pledges'" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
        >
            <livewire:dashboard.pledges-by-campaign-chart />
        </div>
        <div 
            x-show="selectedChart === 'donations'" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
        >
            <livewire:dashboard.donations-by-campaign-chart />
        </div>
    </div>
</x-layouts.app>
