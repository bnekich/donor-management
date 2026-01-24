<div class="bg-white dark:bg-zinc-800 p-6 rounded-lg shadow-md border border-neutral-200 dark:border-neutral-700">
    <h3 class="text-lg font-semibold mb-4 dark:text-white">Donations Summary</h3>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $count }}</p>
            <p class="text-sm text-gray-600 dark:text-gray-400">Total Donations</p>
        </div>
        <div>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">${{ number_format($totalAmount, 2) }}</p>
            <p class="text-sm text-gray-600 dark:text-gray-400">Total Amount</p>
        </div>
    </div>
</div>
