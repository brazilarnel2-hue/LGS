<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daily Sales Report
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Date range filter --}}
            <form method="GET" action="{{ route('admin.reports.sales') }}"
                  class="bg-white shadow rounded-lg p-4 flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">From</label>
                    <input type="date" name="from" value="{{ $from }}"
                           class="mt-1 rounded border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">To</label>
                    <input type="date" name="to" value="{{ $to }}"
                           class="mt-1 rounded border-gray-300">
                </div>
                <button type="submit"
                        class="bg-laundry-teal text-white px-4 py-2 rounded hover:bg-laundry-dark">
                    Apply
                </button>
                <a href="{{ route('admin.reports.sales') }}" class="text-sm text-gray-500 hover:underline">
                    Reset to last 30 days
                </a>
            </form>

            {{-- Summary cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-laundry-teal text-white rounded-lg shadow p-5">
                    <p class="text-sm opacity-90">Total Revenue</p>
                    <p class="text-2xl font-bold mt-1">₱{{ number_format($totalRevenue, 2) }}</p>
                    <p class="text-xs opacity-80 mt-1">{{ $from }} – {{ $to }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-5">
                    <p class="text-sm text-gray-500">Total Bookings</p>
                    <p class="text-2xl font-bold mt-1 text-gray-800">{{ $totalOrders }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-5">
                    <p class="text-sm text-gray-500">Average Daily Revenue</p>
                    <p class="text-2xl font-bold mt-1 text-gray-800">₱{{ number_format($averageDaily, 2) }}</p>
                </div>
            </div>

            {{-- Chart 1: Revenue per day --}}
            <div class="bg-white rounded-lg shadow p-5">
                <h3 class="font-semibold text-gray-800 mb-3">Revenue per Day</h3>
                <div class="relative h-72" data-chart-box>
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            {{-- Charts 2 and 3 --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow p-5">
                    <h3 class="font-semibold text-gray-800 mb-3">Bookings by Status</h3>
                    @if (empty($statusCounts) || collect($statusCounts)->isEmpty())
                        <p class="text-sm text-gray-500 py-10 text-center">No bookings in this date range.</p>
                    @else
                        <div class="relative h-64" data-chart-box>
                            <canvas id="statusChart"></canvas>
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-lg shadow p-5">
                    <h3 class="font-semibold text-gray-800 mb-3">Revenue by Service</h3>
                    @if (empty($serviceRevenue) || collect($serviceRevenue)->isEmpty())
                        <p class="text-sm text-gray-500 py-10 text-center">No sales in this date range.</p>
                    @else
                        <div class="relative h-64" data-chart-box>
                            <canvas id="serviceChart"></canvas>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Table --}}
            <div class="bg-white shadow overflow-hidden rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bookings</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($daily as $row)
                            <tr>
                                <td class="px-6 py-3">{{ \Carbon\Carbon::parse($row['date'])->format('M d, Y (D)') }}</td>
                                <td class="px-6 py-3">{{ $row['orders_count'] }}</td>
                                <td class="px-6 py-3">₱{{ number_format($row['revenue'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-6 text-center text-gray-500">No bookings in this date range.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        // Kung hindi na-load ang Chart.js (walang internet, o na-block), magpakita ng mensahe
        if (typeof Chart === 'undefined') {
            document.querySelectorAll('[data-chart-box]').forEach(box => {
                box.innerHTML = '<p class="text-sm text-red-600 py-10 text-center">Chart library failed to load. Please check your internet connection.</p>';
            });
        } else {
            const peso = (v) => '₱' + Number(v).toLocaleString(undefined, { maximumFractionDigits: 2 });

            // ---------- 1. Revenue per day (line) ----------
            const labels = @json($daily->pluck('label')->values());
            const revenue = @json($daily->pluck('revenue')->values());

            new Chart(document.getElementById('salesChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Revenue (₱)',
                        data: revenue,
                        borderColor: '#0284c7',
                        backgroundColor: 'rgba(2, 132, 199, 0.12)',
                        fill: true,
                        tension: 0.25,
                        pointRadius: 3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: (ctx) => ' ' + peso(ctx.parsed.y) } }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { callback: (v) => peso(v) } }
                    }
                }
            });

            // ---------- 2. Bookings by status (doughnut) ----------
            const statusData = @json($statusCounts ?? []);
            const statusKeys = Object.keys(statusData);
            const statusColors = {
                pending: '#FBBF24',
                confirmed: '#38bdf8',
                picked_up: '#0ea5e9',
                washing: '#6366f1',
                ready: '#14b8a6',
                out_for_delivery: '#a855f7',
                delivered: '#16a34a',
                cancelled: '#ef4444',
            };

            const statusCanvas = document.getElementById('statusChart');
            if (statusCanvas && statusKeys.length) {
                new Chart(statusCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: statusKeys.map(k => k.replace(/_/g, ' ').replace(/^\w/, c => c.toUpperCase())),
                        datasets: [{
                            data: statusKeys.map(k => statusData[k]),
                            backgroundColor: statusKeys.map(k => statusColors[k] ?? '#94a3b8'),
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '60%',
                        plugins: { legend: { position: 'bottom' } }
                    }
                });
            }

            // ---------- 3. Revenue by service (horizontal bar) ----------
            const serviceData = @json(($serviceRevenue ?? collect())->values());

            const serviceCanvas = document.getElementById('serviceChart');
            if (serviceCanvas && serviceData.length) {
                new Chart(serviceCanvas, {
                    type: 'bar',
                    data: {
                        labels: serviceData.map(s => s.name),
                        datasets: [{
                            label: 'Revenue (₱)',
                            data: serviceData.map(s => Number(s.total)),
                            backgroundColor: '#0284c7',
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { callbacks: { label: (ctx) => ' ' + peso(ctx.parsed.x) } }
                        },
                        scales: {
                            x: { beginAtZero: true, ticks: { callback: (v) => peso(v) } }
                        }
                    }
                });
            }
        }
    </script>
</x-app-layout>