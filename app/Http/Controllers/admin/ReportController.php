<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Daily sales report, may chart + table — admin at staff lang makakakita.
     */
    public function sales(Request $request)
    {
        abort_unless(in_array(Auth::user()->role, ['admin', 'staff']), 403, 'Only admin/staff can view reports.');

        // Date range filter — default: huling 30 araw hanggang ngayon.
        $to = $request->filled('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : Carbon::now()->endOfDay();

        $from = $request->filled('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : $to->copy()->subDays(29)->startOfDay();

        // Kada araw: bilang ng order (naka-book sa araw na iyon) at total na kita
        // (total_amount), base sa created_at — ito ang "daily sales" (bookings
        // placed per day), hindi lang yung na-deliver na.
        $rows = Order::query()
            ->selectRaw('DATE(created_at) as sales_date, COUNT(*) as orders_count, SUM(total_amount) as revenue')
            ->whereBetween('created_at', [$from, $to])
             ->where('status', '!=', 'cancelled')
            ->groupBy('sales_date')
            ->orderBy('sales_date')
            ->get()
            ->keyBy('sales_date');

        // Punuan ang mga araw na walang booking ng 0, para tuloy-tuloy ang chart.
        $daily = collect();
        $cursor = $from->copy();
        while ($cursor->lte($to)) {
            $key = $cursor->toDateString();
            $row = $rows->get($key);
            $daily->push([
                'date' => $key,
                'label' => $cursor->format('M d'),
                'orders_count' => $row->orders_count ?? 0,
                'revenue' => $row ? (float) $row->revenue : 0.0,
            ]);
            $cursor->addDay();
        }

        $totalRevenue = $daily->sum('revenue');
        $totalOrders = $daily->sum('orders_count');
        $averageDaily = $daily->count() > 0 ? $totalRevenue / $daily->count() : 0;

        // Chart: bookings by status (lahat ng status, kasama ang cancelled)
        $statusCounts = Order::query()
            ->selectRaw('status, COUNT(*) as total')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('status')
            ->pluck('total', 'status');

        // Chart: revenue by service (presyo ng serbisyo lang, hindi kasama ang cancelled)
        $serviceRevenue = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('services', 'services.id', '=', 'order_items.service_id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->where('orders.status', '!=', 'cancelled')
            ->selectRaw('services.name as name, SUM(order_items.subtotal) as total')
            ->groupBy('services.name')
            ->orderByDesc('total')
            ->get();

        return view('admin.reports.sales', [
            'daily' => $daily,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'averageDaily' => $averageDaily,
            'statusCounts' => $statusCounts,
            'serviceRevenue' => $serviceRevenue,
        ]);
    }
}