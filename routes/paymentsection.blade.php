<div class="bg-white shadow rounded-lg p-6">
    <h3 class="font-semibold mb-3">Payment</h3>

    @if ($order->payment && $order->payment->status === 'paid')
        {{-- Receipt view: payment already recorded --}}
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
                    <dd class="capitalize font-medium">{{ str_replace('_', ' ', $order->payment->method) }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Amount</dt>
                    <dd class="font-medium">₱{{ number_format($order->payment->amount, 2) }}</dd>
                </div>
            </dl>
        </div>
    @else
        {{-- Not yet paid --}}
        <div class="bg-yellow-50 border border-yellow-200 rounded p-4 mb-4">
            <span class="text-yellow-800 font-semibold">⚠ NOT YET PAID</span>
            <p class="text-sm text-gray-600 mt-1">
                Amount due: <span class="font-semibold">₱{{ number_format($order->total_amount, 2) }}</span>
            </p>
        </div>

        @php
            $user = auth()->user();
            $canRecordPayment = in_array($user->role, ['staff', 'admin']) || $order->driver_id === $user->id;
        @endphp

        @if ($canRecordPayment)
            <form method="POST" action="{{ route('orders.payment.store', $order) }}">
                @csrf
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Mark as paid — select payment method:
                </label>
                <div class="flex gap-3">
                    <select name="method" class="flex-1 rounded border-gray-300" required>
                        <option value="cash">Cash</option>
                        <option value="gcash">GCash</option>
                        <option value="card">Card</option>
                        <option value="bank_transfer">Bank Transfer</option>
                    </select>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Record Payment
                    </button>
                </div>
            </form>
        @endif
    @endif
</div>