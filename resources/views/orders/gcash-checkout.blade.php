<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            GCash Payment
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg overflow-hidden">

                {{-- Amount header --}}
                <div class="bg-laundry-teal text-white p-6 text-center">
                    <p class="text-sm opacity-80">Amount to pay</p>
                    <p class="text-3xl font-bold mt-1">₱{{ number_format($order->total_amount, 2) }}</p>
                    <p class="text-sm opacity-80 mt-1">Booking #{{ $order->id }} — GoLaundry</p>
                </div>

                <div class="p-6">

                    @if ($errors->any())
                        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded text-sm">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- QR code --}}
                    <div class="text-center">
                        <img src="{{ asset('images/gcash-qr.jpg') }}" alt="LaundryGo GCash QR"
                             class="mx-auto w-64 rounded-lg border">
                        <a href="{{ asset('images/gcash-qr.jpg') }}" download="LaundryGo-GCash-QR.jpg"
                           class="inline-block mt-2 text-sm text-laundry-teal hover:underline">
                            Save QR to my phone
                        </a>
                    </div>

                    {{-- Steps --}}
                    <ol class="mt-5 text-sm text-gray-700 space-y-2 list-decimal list-inside">
                        <li>Open your <span class="font-semibold">GCash</span> app and tap <span class="font-semibold">Pay QR</span>.</li>
                        <li>Scan the QR above. On the same phone? Tap <span class="font-semibold">Upload QR</span> and pick the saved image.</li>
                        <li>Enter the exact amount: <span class="font-semibold">₱{{ number_format($order->total_amount, 2) }}</span>.</li>
                        <li>After paying, copy the <span class="font-semibold">Reference No.</span> from your GCash receipt and enter it below.</li>
                    </ol>

                    {{-- Reference number --}}
                    <form method="POST" action="{{ route('orders.gcash.confirm', $order) }}" class="mt-5">
                        @csrf
                        <label class="block text-sm font-medium text-gray-700">GCash Reference Number</label>
                        <input type="text" name="reference_number" value="{{ old('reference_number') }}"
                               placeholder="e.g. 1234 567 890123"
                               class="mt-1 block w-full rounded border-gray-300" required>
                        <p class="text-xs text-gray-500 mt-1">
                            We will verify your payment first. Your booking is marked PAID once confirmed.
                        </p>

                        <button type="submit"
                                class="mt-4 w-full bg-laundry-teal text-white py-3 rounded-lg font-semibold hover:bg-laundry-dark">
                            I've Paid — Submit Reference
                        </button>
                    </form>

                    <a href="{{ route('orders.show', $order) }}"
                       class="block text-center text-sm text-gray-500 mt-4 hover:underline">
                        Cancel and go back
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>