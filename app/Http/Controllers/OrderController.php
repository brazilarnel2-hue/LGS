<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Show orders depending on the logged-in user's role.
     */
    public function index()
    {
        $user = Auth::user();

        $orders = match ($user->role) {
            'customer' => Order::where('customer_id', $user->id)->latest()->get(),
            'driver'   => Order::where('driver_id', $user->id)->latest()->get(),
            default    => Order::latest()->get(), // staff/admin see everything
        };

        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for placing a new order (customer only).
     */
    public function create()
    {
        abort_unless(Auth::user()->role === 'customer', 403, 'Only customers can place orders.');

        $services = Service::where('is_active', true)->get();
        return view('orders.create', compact('services'));
    }

    /**
     * Store a new order with its line items (customer only).
     */
    public function store(Request $request)
    {
        abort_unless(Auth::user()->role === 'customer', 403, 'Only customers can place orders.');

        // Remove rows where the checkbox wasn't ticked (no service id sent)
        $services = array_filter($request->input('services', []), function ($item) {
            return !empty($item['id']) && !empty($item['quantity']);
        });
        $request->merge(['services' => array_values($services)]);

        $validated = $request->validate([
            'pickup_option' => 'required|in:driver,shop',
            'return_option' => 'required|in:deliver,shop',
            'pickup_address' => 'required_if:pickup_option,driver|nullable|string|max:255',
            'delivery_address' => 'required_if:return_option,deliver|nullable|string|max:255',
            'scheduled_pickup_at' => 'required|date',
            'scheduled_delivery_at' => 'nullable|date|after:scheduled_pickup_at',
            'notes' => 'nullable|string',
            'services' => 'required|array|min:1',
            'services.*.id' => 'required|exists:services,id',
            'services.*.quantity' => 'required|numeric|min:0.1',
        ]);

        $driverPickup = $validated['pickup_option'] === 'driver';
        $wantsDelivery = $validated['return_option'] === 'deliver';

        $order = Order::create([
            'customer_id' => Auth::id(),
            'status' => 'pending',
            'pickup_option' => $validated['pickup_option'],
            'return_option' => $validated['return_option'],
            // Pickup + delivery = package fee; only one = single fee; none = free
            'delivery_fee' => Order::calculateTripFee($validated['pickup_option'], $validated['return_option']),
            'pickup_address' => $driverPickup
                ? $validated['pickup_address']
                : 'Customer drop-off at shop',
            'delivery_address' => $wantsDelivery
                ? $validated['delivery_address']
                : 'Customer pick-up at shop',
            'scheduled_pickup_at' => $validated['scheduled_pickup_at'],
            'scheduled_delivery_at' => $wantsDelivery
                ? ($validated['scheduled_delivery_at'] ?? null)
                : null,
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['services'] as $item) {
            $service = Service::findOrFail($item['id']);
            $order->items()->create([
                'service_id' => $service->id,
                'quantity' => $item['quantity'],
                'subtotal' => $service->price * $item['quantity'],
            ]);
        }

        $order->recalculateTotal();
        $order->statusHistories()->create([
            'status' => 'pending',
            'changed_by' => Auth::id(),
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Booking placed successfully!');
    }

    /**
     * Show a single order — only its own customer, assigned driver, or staff/admin can view.
     */
    public function show(Order $order)
    {
        $user = Auth::user();

        $canView = $user->role === 'staff'
            || $user->role === 'admin'
            || $order->customer_id === $user->id
            || $order->driver_id === $user->id;

        abort_unless($canView, 403, 'You are not allowed to view this order.');

        $order->load(['items.service', 'customer', 'driver', 'payment', 'statusHistories']);
        return view('orders.show', compact('order'));
    }

    /**
     * Show a printable receipt — proof of payment for the driver/customer.
     */
    public function receipt(Order $order)
    {
        $user = Auth::user();

        $canView = in_array($user->role, ['staff', 'admin'])
            || $order->customer_id === $user->id
            || $order->driver_id === $user->id;

        abort_unless($canView, 403, 'You are not allowed to view this receipt.');

        $order->load(['items.service', 'customer', 'payment']);

        return view('orders.receipt', compact('order'));
    }

    /**
     * Show the edit form (staff/admin only).
     */
    public function edit(Order $order)
    {
        abort_unless(in_array(Auth::user()->role, ['staff', 'admin']), 403, 'Only staff can update orders.');

        $drivers = User::where('role', 'driver')->get();
        return view('orders.edit', compact('order', 'drivers'));
    }

    /**
     * Update order status and/or assigned driver (staff/admin only).
     */
    public function update(Request $request, Order $order)
    {
        abort_unless(in_array(Auth::user()->role, ['staff', 'admin']), 403, 'Only staff can update orders.');

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,picked_up,washing,ready,out_for_delivery,delivered,cancelled',
            'driver_id' => 'nullable|exists:users,id',
        ]);

        if (!empty($validated['driver_id'])) {
            $order->update(['driver_id' => $validated['driver_id']]);
        }

        $order->updateStatus($validated['status'], Auth::id());

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order updated.');
    }

    /**
     * Cancel/delete an order (staff/admin only).
     */
    public function destroy(Order $order)
    {
        abort_unless(in_array(Auth::user()->role, ['staff', 'admin']), 403, 'Only staff can delete orders.');

        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order removed.');
    }
}