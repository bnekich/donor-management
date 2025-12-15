<div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-lg font-semibold mb-4">Pending Pledges</h3>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-2xl font-bold text-blue-600">{{ $count }}</p>
            <p class="text-sm text-gray-600">Total Pledges</p>
        </div>
        <div>
            <p class="text-2xl font-bold text-green-600">${{ number_format($totalAmount, 2) }}</p>
            <p class="text-sm text-gray-600">Total Amount</p>
        </div>
    </div>
</div>
