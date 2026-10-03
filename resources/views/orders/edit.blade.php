<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Update Order #{{ $order->id }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">

                <form method="POST" action="{{ route('orders.update', $order) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" class="mt-1 block w-full rounded border-gray-300">
                            @foreach (['pending', 'confirmed', 'picked_up', 'washing', 'ready', 'out_for_delivery', 'delivered', 'cancelled'] as $status)
                                <option value="{{ $status }}" @selected($order->status === $status)>
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Assign Driver</label>
                        <select name="driver_id" class="mt-1 block w-full rounded border-gray-300">
                            <option value="">— Not assigned —</option>
                            @foreach ($drivers as $driver)
                                <option value="{{ $driver->id }}" @selected($order->driver_id === $driver->id)>
                                    {{ $driver->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-between items-center">
                        <a href="{{ route('orders.show', $order) }}" class="text-gray-600 hover:underline">
                            Cancel
                        </a>
                        <button type="submit" class="bg-laundry-teal text-white px-4 py-2 rounded hover:bg-laundry-dark">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>