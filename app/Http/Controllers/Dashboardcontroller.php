<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (in_array($user->role, ['staff', 'admin'])) {
            return $this->staffDashboard();
        }

        if ($user->role === 'driver') {
            return $this->driverDashboard($user);
        }

        return $this->customerDashboard($user);
    }

    private function staffDashboard()
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'ongoing' => Order::whereIn('status', ['confirmed', 'picked_up', 'washing', 'ready', 'out_for_delivery'])->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'total_revenue' => Payment::where('status', 'paid')->sum('amount'),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_drivers' => User::where('role', 'driver')->count(),
        ];

        $recentOrders = Order::with('customer')->latest()->take(5)->get();

        return view('dashboard', [
            'role' => 'staff',
            'stats' => $stats,
            'recentOrders' => $recentOrders,
        ]);
    }

    private function driverDashboard($user)
    {
        $stats = [
            'assigned' => Order::where('driver_id', $user->id)->count(),
            'out_for_delivery' => Order::where('driver_id', $user->id)->where('status', 'out_for_delivery')->count(),
            'delivered' => Order::where('driver_id', $user->id)->where('status', 'delivered')->count(),
        ];

        $recentOrders = Order::with('customer')
            ->where('driver_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', [
            'role' => 'driver',
            'stats' => $stats,
            'recentOrders' => $recentOrders,
        ]);
    }

    private function customerDashboard($user)
    {
        $stats = [
            'total_orders' => Order::where('customer_id', $user->id)->count(),
            'pending' => Order::where('customer_id', $user->id)->where('status', 'pending')->count(),
            'total_spent' => Payment::whereHas('order', function ($q) use ($user) {
                $q->where('customer_id', $user->id);
            })->where('status', 'paid')->sum('amount'),
        ];

        $recentOrders = Order::where('customer_id', $user->id)->latest()->take(5)->get();

        return view('dashboard', [
            'role' => 'customer',
            'stats' => $stats,
            'recentOrders' => $recentOrders,
        ]);
    }
}