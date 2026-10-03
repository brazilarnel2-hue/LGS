<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            GCash Payment
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg overflow-hidden">

                {{-- Simulated GCash-style header --}}
                <div class="bg-laundry-teal text-white p-6 text-center">
                    <p class="text-sm opacity-80">You are paying</p>
                    <p class="text-3xl font-bold mt-1">₱{{ number_format($order->total_amount, 2) }}</p>
                    <p class="text-sm opacity-80 mt-1">Order #{{ $order->id }} — LaundryGo</p>
                </div>

                <div class="p-6">
                    <p class="text-sm text-gray-500 mb-4 text-center">
                        This is a simulated checkout for demo purposes. No real transaction
                        or GCash account is involved — clicking confirm will mark this order
                        as paid.
                    </p>

                    <form method="POST" action="{{ route('orders.gcash.confirm', $order) }}">
                        @csrf
                        <button type="submit"
                                class="w-full bg-laundry-teal text-white py-3 rounded-lg font-semibold hover:bg-laundry-dark">
                            Confirm Payment
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