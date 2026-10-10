<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if ($role === 'staff')
                {{-- STAFF / ADMIN DASHBOARD --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white shadow rounded-lg p-5">
                        <p class="text-sm text-gray-500">Total Bookings</p>
                        <p class="text-3xl font-bold text-laundry-dark mt-1">{{ $stats['total_orders'] }}</p>
                    </div>
                    <div class="bg-white shadow rounded-lg p-5">
                        <p class="text-sm text-gray-500">Pending</p>
                        <p class="text-3xl font-bold text-laundry-warning mt-1">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="bg-white shadow rounded-lg p-5">
                        <p class="text-sm text-gray-500">Ongoing</p>
                        <p class="text-3xl font-bold text-laundry-cyan mt-1">{{ $stats['ongoing'] }}</p>
                    </div>
                    <div class="bg-white shadow rounded-lg p-5">
                        <p class="text-sm text-gray-500">Delivered</p>
                        <p class="text-3xl font-bold text-laundry-success mt-1">{{ $stats['delivered'] }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-laundry-teal text-white shadow rounded-lg p-5">
                        <p class="text-sm opacity-80">Total Revenue</p>
                        <p class="text-3xl font-bold mt-1">₱{{ number_format($stats['total_revenue'], 2) }}</p>
                    </div>
                    <div class="bg-white shadow rounded-lg p-5">
                        <p class="text-sm text-gray-500">Total Customers</p>
                        <p class="text-3xl font-bold text-laundry-dark mt-1">{{ $stats['total_customers'] }}</p>
                    </div>
                    <div class="bg-white shadow rounded-lg p-5">
                        <p class="text-sm text-gray-500">Total Drivers</p>
                        <p class="text-3xl font-bold text-laundry-dark mt-1">{{ $stats['total_drivers'] }}</p>
                    </div>
                </div>

            @elseif ($role === 'driver')
                {{-- DRIVER DASHBOARD --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white shadow rounded-lg p-5">
                        <p class="text-sm text-gray-500">Assigned to Me</p>
                        <p class="text-3xl font-bold text-laundry-dark mt-1">{{ $stats['assigned'] }}</p>
                    </div>
                    <div class="bg-white shadow rounded-lg p-5">
                        <p class="text-sm text-gray-500">Out for Delivery</p>
                        <p class="text-3xl font-bold text-laundry-cyan mt-1">{{ $stats['out_for_delivery'] }}</p>
                    </div>
                    <div class="bg-white shadow rounded-lg p-5">
                        <p class="text-sm text-gray-500">Delivered</p>
                        <p class="text-3xl font-bold text-laundry-success mt-1">{{ $stats['delivered'] }}</p>
                    </div>
                </div>

            @else
                {{-- CUSTOMER DASHBOARD --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white shadow rounded-lg p-5">
                        <p class="text-sm text-gray-500">My Total Bookings</p>
                        <p class="text-3xl font-bold text-laundry-dark mt-1">{{ $stats['total_orders'] }}</p>
                    </div>
                    <div class="bg-white shadow rounded-lg p-5">
                        <p class="text-sm text-gray-500">Pending</p>
                        <p class="text-3xl font-bold text-laundry-warning mt-1">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="bg-laundry-teal text-white shadow rounded-lg p-5">
                        <p class="text-sm opacity-80">Total Spent</p>
                        <p class="text-3xl font-bold mt-1">₱{{ number_format($stats['total_spent'], 2) }}</p>
                    </div>
                </div>
            @endif

            {{-- RECENT BOOKINGS (shown to everyone, scoped by role) --}}
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-lg">Recent Bookings</h3>
                    <a href="{{ route('orders.index') }}" class="text-sm text-laundry-teal hover:underline">
                        View all →
                    </a>
                </div>

                @if ($recentOrders->isEmpty())
                    <p class="text-gray-500 text-sm">No bookings yet.</p>
                @else
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b">
                                <th class="py-2">Booking #</th>
                                @if ($role === 'staff')
                                    <th class="py-2">Customer</th>
                                @endif
                                <th class="py-2">Status</th>
                                <th class="py-2 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentOrders as $order)
                                <tr class="border-b hover:bg-laundry-sky/40 cursor-pointer"
                                    onclick="window.location='{{ route('orders.show', $order) }}'">
                                    <td class="py-2">#{{ $order->id }}</td>
                                    @if ($role === 'staff')
                                        <td class="py-2">{{ $order->customer->name }}</td>
                                    @endif
                                    <td class="py-2">
                                        <span class="px-2 py-1 text-xs rounded bg-laundry-sky text-laundry-teal capitalize">
                                            {{ str_replace('_', ' ', $order->status) }}
                                        </span>
                                    </td>
                                    <td class="py-2 text-right">₱{{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>