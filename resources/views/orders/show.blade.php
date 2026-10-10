<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Booking #{{ $order->id }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <span class="inline-block mt-1 px-3 py-1 text-sm rounded bg-laundry-sky text-laundry-teal capitalize">
                            {{ str_replace('_', ' ', $order->status) }}
                        </span>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('orders.receipt', $order) }}" target="_blank"
                           class="bg-laundry-teal text-white px-4 py-2 rounded hover:bg-laundry-dark">
                            View Receipt
                        </a>
                        @if (in_array(auth()->user()->role, ['staff', 'admin']))
                            <a href="{{ route('orders.edit', $order) }}"
                               class="bg-laundry-dark text-white px-4 py-2 rounded hover:bg-slate-900">
                                Update Booking
                            </a>
                        @endif
                    </div>
                </div>

                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Customer</dt>
                        <dd>{{ $order->customer->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Driver</dt>
                        <dd>{{ $order->driver->name ?? 'Not yet assigned' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Pickup Address</dt>
                        <dd>{{ $order->pickup_address }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Delivery Address</dt>
                        <dd>{{ $order->delivery_address }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Pickup Schedule</dt>
                        <dd>{{ $order->scheduled_pickup_at?->format('M d, Y h:i A') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Delivery Schedule</dt>
                        <dd>{{ $order->scheduled_delivery_at?->format('M d, Y h:i A') ?? '—' }}</dd>
                    </div>
                </dl>

                @if ($order->notes)
                    <div class="mt-4">
                        <dt class="text-gray-500 text-sm">Notes</dt>
                        <dd class="text-sm">{{ $order->notes }}</dd>
                    </div>
                @endif
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="font-semibold mb-3">Items</h3>
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2">Service</th>
                            <th class="py-2">Quantity</th>
                            <th class="py-2 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr class="border-b">
                                <td class="py-2">{{ $item->service->name }}</td>
                                <td class="py-2">{{ $item->quantity }} {{ $item->service->unit }}</td>
                                <td class="py-2 text-right">₱{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach

                        @if ($order->delivery_fee > 0)
                            <tr class="border-b">
                                <td class="py-2" colspan="2">{{ $order->fee_label }}</td>
                                <td class="py-2 text-right">₱{{ number_format($order->delivery_fee, 2) }}</td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="pt-3 font-semibold">Total</td>
                            <td class="pt-3 text-right font-semibold">₱{{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="font-semibold mb-3">Payment</h3>

                @php
                    $user = auth()->user();
                    $isOwner = $user->role === 'customer' && $user->id === $order->customer_id;
                    $canRecordPayment = in_array($user->role, ['staff', 'admin']) || $order->driver_id === $user->id;
                    $paymentStatus = $order->payment->status ?? null; // null, 'pending', or 'paid'
                @endphp

                @if ($paymentStatus === 'paid')
                    {{-- FULLY PAID — show receipt --}}
                    <div class="bg-green-50 border border-green-200 rounded p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-green-800 font-semibold">✓ PAID</span>
                            <span class="text-sm text-gray-500">
                                {{ $order->payment->paid_at?->format('M d, Y h:i A') }}
                            </span>
                        </div>
                        <dl class="grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <dt class="text-gray-500">Method</dt>
                                <dd class="font-medium">
                                    {{ $order->payment->method === 'cod' ? 'Cash on Delivery' : 'GCash' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Amount</dt>
                                <dd class="font-medium">₱{{ number_format($order->payment->amount, 2) }}</dd>
                            </div>
                            @if ($order->payment->reference_number)
                                <div class="col-span-2">
                                    <dt class="text-gray-500">Reference Number</dt>
                                    <dd class="font-medium">{{ $order->payment->reference_number }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                @elseif ($paymentStatus === 'pending' && $order->payment->method === 'cod')
                    {{-- COD SELECTED but not yet collected --}}
                    <div class="bg-laundry-sky border border-laundry-cyan rounded p-4 mb-4">
                        <span class="text-laundry-dark font-semibold">💵 Cash on Delivery selected</span>
                        <p class="text-sm text-gray-600 mt-1">
                            Please prepare <span class="font-semibold">₱{{ number_format($order->total_amount, 2) }}</span>
                            for the driver upon delivery.
                        </p>
                        @if ($order->delivery_fee > 0)
                            <p class="text-xs text-gray-500 mt-1">
                                Includes ₱{{ number_format($order->delivery_fee, 2) }} {{ strtolower($order->fee_label) }}.
                            </p>
                        @endif
                    </div>

                    @if ($canRecordPayment)
                        <form method="POST" action="{{ route('orders.payment.store', $order) }}">
                            @csrf
                            <input type="hidden" name="method" value="cod">
                            <button type="submit" class="bg-laundry-success text-white px-4 py-2 rounded hover:bg-green-700">
                                Mark Cash Received
                            </button>
                        </form>
                    @endif

                @elseif ($paymentStatus === 'pending' && $order->payment->method === 'gcash')
                    {{-- GCASH reference submitted, waiting for staff to verify --}}
                    <div class="bg-yellow-50 border border-yellow-200 rounded p-4 mb-4">
                        <span class="text-yellow-800 font-semibold">⏳ GCash payment awaiting verification</span>
                        <p class="text-sm text-gray-600 mt-1">
                            Reference No.: <span class="font-semibold">{{ $order->payment->reference_number }}</span>
                        </p>
                        <p class="text-sm text-gray-600">
                            Amount: <span class="font-semibold">₱{{ number_format($order->total_amount, 2) }}</span>
                        </p>
                    </div>

                    @if (in_array($user->role, ['staff', 'admin']))
                        <form method="POST" action="{{ route('orders.payment.verify', $order) }}">
                            @csrf
                            <button type="submit" class="bg-laundry-success text-white px-4 py-2 rounded hover:bg-green-700">
                                Confirm Payment Received
                            </button>
                        </form>
                    @endif

                    @if ($isOwner)
                        <a href="{{ route('orders.gcash.checkout', $order) }}"
                           class="inline-block mt-3 text-sm text-gray-500 hover:underline">
                            Wrong reference number? Submit again
                        </a>
                    @endif

                @else
                    {{-- NOTHING SELECTED YET --}}
                    <div class="bg-yellow-50 border border-yellow-200 rounded p-4 mb-4">
                        <span class="text-yellow-800 font-semibold">⚠ NOT YET PAID</span>
                        <p class="text-sm text-gray-600 mt-1">
                            Amount due: <span class="font-semibold">₱{{ number_format($order->total_amount, 2) }}</span>
                        </p>
                    </div>

                    @if ($isOwner)
                        {{-- Customer picks how they want to pay --}}
                        <p class="text-sm font-medium text-gray-700 mb-2">Choose your payment method:</p>
                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('orders.gcash.checkout', $order) }}"
                               class="block text-center bg-laundry-teal text-white py-3 rounded-lg font-semibold hover:bg-laundry-dark">
                                Pay with GCash
                            </a>
                            <form method="POST" action="{{ route('orders.cod.select', $order) }}">
                                @csrf
                                <button type="submit"
                                        class="w-full bg-white border-2 border-laundry-teal text-laundry-teal py-3 rounded-lg font-semibold hover:bg-laundry-sky">
                                    Cash on Delivery
                                </button>
                            </form>
                        </div>
                    @endif

                    @if ($canRecordPayment)
                        {{-- Staff/driver can also manually record a payment directly --}}
                        <details class="mt-4">
                            <summary class="text-sm text-gray-500 cursor-pointer hover:text-laundry-teal">
                                Record payment manually
                            </summary>
                            <form method="POST" action="{{ route('orders.payment.store', $order) }}" x-data="{ method: 'cod' }" class="mt-3">
                                @csrf
                                <div class="mb-3">
                                    <select name="method" x-model="method" class="w-full rounded border-gray-300" required>
                                        <option value="cod">Cash on Delivery (COD)</option>
                                        <option value="gcash">GCash</option>
                                    </select>
                                </div>
                                <div class="mb-3" x-show="method === 'gcash'">
                                    <input type="text" name="reference_number" placeholder="GCash reference number"
                                           class="w-full rounded border-gray-300">
                                    @error('reference_number')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="submit" class="bg-laundry-success text-white px-4 py-2 rounded hover:bg-green-700">
                                    Record Payment
                                </button>
                            </form>
                        </details>
                    @endif
                @endif
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="font-semibold mb-3">Status History</h3>
                <ul class="space-y-2 text-sm">
                    @foreach ($order->statusHistories->sortByDesc('created_at') as $history)
                        <li class="flex justify-between border-b pb-2">
                            <span class="capitalize">{{ str_replace('_', ' ', $history->status) }}</span>
                            <span class="text-gray-500">{{ $history->created_at->format('M d, Y h:i A') }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>