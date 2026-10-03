<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Place a New Order
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('orders.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Pickup Address</label>
                        <input type="text" name="pickup_address" value="{{ old('pickup_address') }}"
                               class="mt-1 block w-full rounded border-gray-300" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Delivery Address</label>
                        <input type="text" name="delivery_address" value="{{ old('delivery_address') }}"
                               class="mt-1 block w-full rounded border-gray-300" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pickup Date & Time</label>
                            <input type="datetime-local" name="scheduled_pickup_at"
                                   class="mt-1 block w-full rounded border-gray-300" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Delivery Date & Time (optional)</label>
                            <input type="datetime-local" name="scheduled_delivery_at"
                                   class="mt-1 block w-full rounded border-gray-300">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Services</label>
                        @foreach ($services as $index => $service)
                            <div class="flex items-center gap-3 mb-2">
                                <input type="checkbox" name="services[{{ $index }}][id]" value="{{ $service->id }}"
                                       class="service-checkbox rounded">
                                <span class="flex-1">{{ $service->name }} (₱{{ number_format($service->price, 2) }} / {{ $service->unit }})</span>
                                <input type="number" step="0.1" min="0.1" name="services[{{ $index }}][quantity]"
                                       placeholder="Qty" class="w-24 rounded border-gray-300">
                            </div>
                        @endforeach
                        <p class="text-xs text-gray-500 mt-1">Tick a service and enter quantity (kg or item count).</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Notes (optional)</label>
                        <textarea name="notes" rows="3" class="mt-1 block w-full rounded border-gray-300">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit" class="bg-laundry-teal text-white px-4 py-2 rounded hover:bg-laundry-dark">
                        Place Order
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>