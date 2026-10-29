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
                    // Distance (km) at serviceability ng bawat barangay, naka-key sa barangay id
                    $barangayInfo = $barangays->mapWithKeys(fn ($b) => [
                        $b->id => [
                            'distance_km' => (float) $b->distance_km,
                            'is_serviceable' => (bool) $b->is_serviceable,
                        ],
                    ]);

                    // Unit ng bawat service (kg o item), naka-key sa POSITION nito sa checklist
                    // (0, 1, 2, ...) — ginagamit para malaman kung aling quantity ang ibibilang
                    // sa "load" (kg lang, hindi dry clean/ironing na per-item).
                    $indexUnits = $services->values()->mapWithKeys(fn ($s, $i) => [$i => $s->unit]);
                @endphp

                <form method="POST" action="{{ route('orders.store') }}"
                      x-data="{
                          pickup: @js(old('pickup_option', 'driver')),
                          ret: @js(old('return_option', 'deliver')),
                          barangayId: @js((string) old('barangay_id', '')),
                          barangays: @js($barangayInfo),
                          baseFee: @js($pricing['base_fee']),
                          ratePerKm: @js($pricing['rate_per_km']),
                          packageMultiplier: @js($pricing['package_multiplier']),
                          kgPerLoad: @js($pricing['kg_per_load']),
                          indexUnits: @js($indexUnits),
                          checked: {},
                          quantities: {},
                          get needsDriver() {
                              return this.pickup === 'driver' || this.ret === 'deliver';
                          },
                          get selectedBarangay() {
                              if (this.barangayId && this.barangays[this.barangayId] !== undefined) {
                                  return this.barangays[this.barangayId];
                              }
                              return null;
                          },
                          get distanceKm() {
                              return this.selectedBarangay ? this.selectedBarangay.distance_km : null;
                          },
                          get isServiceable() {
                              return this.selectedBarangay ? this.selectedBarangay.is_serviceable : true;
                          },
                          get totalKg() {
                              let kg = 0;
                              for (const i in this.quantities) {
                                  if (this.checked[i] && (this.indexUnits[i] === 'kg' || this.indexUnits[i] === 'load')) {
                                      kg += Number(this.quantities[i]) || 0;
                                  }
                              }
                              return kg;
                          },
                          get loads() {
                              return this.totalKg > 0 ? Math.ceil(this.totalKg / this.kgPerLoad) : 0;
                          },
                          get single() {
                              if (this.distanceKm === null) return null;
                              return this.baseFee + (this.ratePerKm * this.distanceKm);
                          },
                          get fee() {
                              if (!this.needsDriver) return 0;
                              if (this.single === null) return null;
                              if (this.pickup === 'driver' && this.ret === 'deliver') {
                                  return Math.round(this.single * this.packageMultiplier * 100) / 100;
                              }
                              return Math.round(this.single * 100) / 100;
                          },
                          get saving() {
                              if (this.needsDriver && this.single !== null && this.pickup === 'driver' && this.ret === 'deliver') {
                                  return Math.max(0, Math.round(((this.single * 2) - (this.single * this.packageMultiplier)) * 100) / 100);
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
                               placeholder="House no., street / purok, landmark"
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
                                   placeholder="House no., street / purok, landmark"
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

                    {{-- 3. Barangay (lalabas lang kung may driver na kasali) --}}
                    <div class="mb-4" x-show="needsDriver">
                        <label class="block text-sm font-medium text-gray-700">Your Barangay</label>
                        <select name="barangay_id" x-model="barangayId"
                                :disabled="!needsDriver"
                                :required="needsDriver"
                                class="mt-1 block w-full rounded border-gray-300">
                            <option value="">-- Select your barangay --</option>
                            @foreach ($barangays as $barangay)
                                <option value="{{ $barangay->id }}">{{ $barangay->name }}</option>
                            @endforeach
                        </select>

                        <p class="text-xs text-gray-500 mt-1" x-show="distanceKm === null">
                            The pickup / delivery fee depends on your barangay.
                        </p>
                        <p class="text-xs text-gray-500 mt-1" x-show="distanceKm !== null && isServiceable">
                            Distance from shop: <span x-text="distanceKm !== null ? distanceKm.toFixed(1) : ''"></span> km
                        </p>

                        <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded text-sm text-yellow-800"
                             x-show="barangayId && !isServiceable">
                            Sorry, we don't offer pickup/delivery in your area yet. Please choose
                            <span class="font-semibold">"I'll drop it off / pick it up at the shop"</span>
                            instead, or contact us directly to ask about your area.
                        </div>

                        @error('barangay_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Fee summary --}}
                    <div class="mb-4 rounded-lg bg-gray-50 border border-gray-200 p-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Pickup / delivery fee</span>
                            <span class="font-semibold"
                                  x-text="!needsDriver ? 'Free' : (!isServiceable ? 'Not available' : (fee === null ? 'Select your barangay' : '₱' + fee.toFixed(2)))"></span>
                        </div>
                        <p class="text-xs text-green-700 mt-1" x-show="saving > 0 && isServiceable"
                           x-text="'You save ₱' + saving.toFixed(2) + ' with pickup + delivery!'"></p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Services</label>
                        @foreach ($services as $index => $service)
                            <div class="flex items-center gap-3 mb-2">
                                <input type="checkbox" name="services[{{ $index }}][id]" value="{{ $service->id }}"
                                       x-model="checked[{{ $index }}]"
                                       class="service-checkbox rounded">
                                <span class="flex-1">
                                    {{ $service->name }}
                                    @if ($service->unit === 'load')
                                        (₱{{ number_format($service->price, 2) }} per {{ $pricing['kg_per_load'] }}kg load)
                                    @else
                                        (₱{{ number_format($service->price, 2) }} / {{ $service->unit }})
                                    @endif
                                </span>
                                <input type="number" step="0.1" min="0.1" name="services[{{ $index }}][quantity]"
                                       x-model.number="quantities[{{ $index }}]"
                                       placeholder="{{ $service->unit === 'load' ? 'Kg' : 'Qty' }}"
                                       class="w-24 rounded border-gray-300">
                            </div>
                        @endforeach
                        <p class="text-xs text-gray-500 mt-1">
                            Tick a service and enter quantity (kg or item count).
                            For Wash &amp; Fold, every {{ $pricing['kg_per_load'] }}kg (or less) is billed as 1 load.
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Notes (optional)</label>
                        <textarea name="notes" rows="3" class="mt-1 block w-full rounded border-gray-300">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit" :disabled="needsDriver && barangayId && !isServiceable"
                            class="bg-laundry-teal text-white px-4 py-2 rounded hover:bg-laundry-dark disabled:opacity-50 disabled:cursor-not-allowed">
                        Place Booking
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>