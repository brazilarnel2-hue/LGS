<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Place a New Booking
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

                @php
                    $fees = [
                        'pickup'   => (float) config('laundry.pickup_fee', 0),
                        'delivery' => (float) config('laundry.delivery_fee', 0),
                        'package'  => (float) config('laundry.package_fee', 0),
                    ];
                @endphp

                <form method="POST" action="{{ route('orders.store') }}"
                      x-data="{
                          pickup: @js(old('pickup_option', 'driver')),
                          ret: @js(old('return_option', 'deliver')),
                          fees: @js($fees),
                          get fee() {
                              if (this.pickup === 'driver' && this.ret === 'deliver') return this.fees.package;
                              if (this.pickup === 'driver') return this.fees.pickup;
                              if (this.ret === 'deliver') return this.fees.delivery;
                              return 0;
                          },
                          get saving() {
                              if (this.pickup === 'driver' && this.ret === 'deliver') {
                                  return Math.max(0, this.fees.pickup + this.fees.delivery - this.fees.package);
                              }
                              return 0;
                          }
                      }">
                    @csrf

                    {{-- 1. How does the laundry get to the shop? --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">How will we get your laundry?</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="flex items-start gap-3 border rounded-lg p-3 cursor-pointer"
                                   :class="pickup === 'driver' ? 'border-laundry-teal bg-laundry-sky' : 'border-gray-300'">
                                <input type="radio" name="pickup_option" value="driver" x-model="pickup" class="mt-1">
                                <span>
                                    <span class="block font-medium">Pick it up from my address</span>
                                    <span class="block text-xs text-gray-500">Our driver collects it</span>
                                </span>
                            </label>

                            <label class="flex items-start gap-3 border rounded-lg p-3 cursor-pointer"
                                   :class="pickup === 'shop' ? 'border-laundry-teal bg-laundry-sky' : 'border-gray-300'">
                                <input type="radio" name="pickup_option" value="shop" x-model="pickup" class="mt-1">
                                <span>
                                    <span class="block font-medium">I'll drop it off at the shop</span>
                                    <span class="block text-xs text-gray-500">No pickup needed</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div x-show="pickup === 'driver'" class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Pickup Address</label>
                        <input type="text" name="pickup_address" value="{{ old('pickup_address') }}"
                               :disabled="pickup !== 'driver'"
                               :required="pickup === 'driver'"
                               class="mt-1 block w-full rounded border-gray-300">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700"
                               x-text="pickup === 'driver' ? 'Pickup Date & Time' : 'Drop-off Date & Time'">
                            Pickup Date & Time
                        </label>
                        <input type="datetime-local" name="scheduled_pickup_at" value="{{ old('scheduled_pickup_at') }}"
                               class="mt-1 block w-full rounded border-gray-300" required>
                    </div>

                    {{-- 2. How does the laundry get back to the customer? --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">How do you want to get your laundry back?</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="flex items-start gap-3 border rounded-lg p-3 cursor-pointer"
                                   :class="ret === 'deliver' ? 'border-laundry-teal bg-laundry-sky' : 'border-gray-300'">
                                <input type="radio" name="return_option" value="deliver" x-model="ret" class="mt-1">
                                <span>
                                    <span class="block font-medium">Deliver to me</span>
                                    <span class="block text-xs text-gray-500">Our driver brings it to you</span>
                                </span>
                            </label>

                            <label class="flex items-start gap-3 border rounded-lg p-3 cursor-pointer"
                                   :class="ret === 'shop' ? 'border-laundry-teal bg-laundry-sky' : 'border-gray-300'">
                                <input type="radio" name="return_option" value="shop" x-model="ret" class="mt-1">
                                <span>
                                    <span class="block font-medium">I'll pick it up at the shop</span>
                                    <span class="block text-xs text-gray-500">No delivery needed</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div x-show="ret === 'deliver'">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Delivery Address</label>
                            <input type="text" name="delivery_address" value="{{ old('delivery_address') }}"
                                   :disabled="ret !== 'deliver'"
                                   :required="ret === 'deliver'"
                                   class="mt-1 block w-full rounded border-gray-300">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Delivery Date & Time (optional)</label>
                            <input type="datetime-local" name="scheduled_delivery_at" value="{{ old('scheduled_delivery_at') }}"
                                   :disabled="ret !== 'deliver'"
                                   class="mt-1 block w-full rounded border-gray-300">
                        </div>
                    </div>

                    {{-- Fee summary --}}
                    <div class="mb-4 rounded-lg bg-gray-50 border border-gray-200 p-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Pickup / delivery fee</span>
                            <span class="font-semibold" x-text="fee > 0 ? '₱' + fee.toFixed(2) : 'Free'"></span>
                        </div>
                        <p class="text-xs text-green-700 mt-1" x-show="saving > 0"
                           x-text="'You save ₱' + saving.toFixed(2) + ' with pickup + delivery!'"></p>
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
                        Place Booking
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>