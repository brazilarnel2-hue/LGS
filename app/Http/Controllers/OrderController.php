<?php

namespace App\Http\Controllers;

use App\Models\Barangay;
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

        $services  = Service::where('is_active', true)->get();
        $barangays = Barangay::orderBy('is_serviceable', 'desc')
            ->orderBy('name')
            ->get(['id', 'name', 'distance_km', 'is_serviceable']);

        // Para sa live na preview ng fee sa form (iisang source ng formula)
        $pricing = [
            'base_fee'           => Order::BASE_FEE,
            'rate_per_km'        => Order::RATE_PER_KM,
            'package_multiplier' => Order::PACKAGE_MULTIPLIER,
            'kg_per_load'        => Order::KG_PER_LOAD,
        ];

        return view('orders.create', compact('services', 'barangays', 'pricing'));
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
            'barangay_id' => 'required_if:pickup_option,driver|required_if:return_option,deliver|nullable|exists:barangays,id',
            'pickup_address' => 'required_if:pickup_option,driver|nullable|string|max:255',
            'delivery_address' => 'required_if:return_option,deliver|nullable|string|max:255',
            'scheduled_pickup_at' => 'required|date',
            'scheduled_delivery_at' => 'nullable|date|after:scheduled_pickup_at',
            'notes' => 'nullable|string',
            'services' => 'required|array|min:1',
            'services.*.id' => 'required|exists:services,id',
            'services.*.quantity' => 'required|numeric|min:0.1',
        ], [
            'barangay_id.required_if' => 'Please select your barangay.',
        ]);

        $driverPickup = $validated['pickup_option'] === 'driver';
        $wantsDelivery = $validated['return_option'] === 'deliver';

        // Ang distansya ay galing na sa napiling barangay (hindi na sa mapa)
        $barangay = !empty($validated['barangay_id'])
            ? Barangay::findOrFail($validated['barangay_id'])
            : null;

        // Hindi pa namin kaya puntahan ang lugar na pinili (outside service area) —
        // kailangan pumili ng shop drop-off/pick-up sa halip na driver.
        if (($driverPickup || $wantsDelivery) && $barangay && !$barangay->is_serviceable) {
            return back()->withInput()->withErrors([
                'barangay_id' => 'Sorry, we don\'t offer pickup/delivery in your area yet. Please choose "I\'ll drop it off / pick it up at the shop" instead.',
            ]);
        }

        $distanceKm = $barangay?->distance_km;

        $servicesById = Service::whereIn('id', collect($validated['services'])->pluck('id'))
            ->get()
            ->keyBy('id');

        $order = Order::create([
            'customer_id' => Auth::id(),
            'status' => 'pending',
            'pickup_option' => $validated['pickup_option'],
            'return_option' => $validated['return_option'],
            'barangay_id' => $barangay?->id,
            // Pickup + delivery = package fee; only one = single fee; none = free
            // Ang bayad ay awtomatikong base sa distansya ng napiling barangay lang
            'delivery_fee' => Order::calculateTripFee(
                $validated['pickup_option'],
                $validated['return_option'],
                $distanceKm
            ),
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
            $service = $servicesById->get($item['id']) ?? Service::findOrFail($item['id']);

            // "load" unit = flat na bayad kada 8kg (naka-round up), hindi diretsong
            // price x kg. Halimbawa: 5kg o 8kg, parehong 1 load; 9kg = 2 loads.
            if ($service->unit === 'load') {
                $subtotal = $service->price * Order::loadsForKg((float) $item['quantity']);
            } else {
                $subtotal = $service->price * $item['quantity'];
            }

            $order->items()->create([
                'service_id' => $service->id,
                'quantity' => $item['quantity'],
                'subtotal' => $subtotal,
            ]);
        }

        $order->recalculateTotal();
        $order->statusHistories()->create([
            'status' => 'pending',
            'changed_by' => Auth::id(),
        ]);

        // Walang Payment record na agad gagawin dito — pipiliin ng customer sa
        // order show page kung GCash o Cash on Delivery (PaymentController ang
        // bahala dun), kaya kailangang blangko muna ito pagkatapos mag-book.

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

        $order->load(['items.service', 'customer', 'driver', 'payment', 'statusHistories', 'barangay']);
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
     * Customer cancels their own booking (pending lang, at hindi pa bayad).
     */
    public function cancel(Order $order)
    {
        $user = Auth::user();

        abort_unless(
            $user->role === 'customer' && $order->customer_id === $user->id,
            403,
            'You can only cancel your own booking.'
        );

        if ($order->status !== 'pending') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'This booking can no longer be cancelled because it is already being processed.');
        }

        if (optional($order->payment)->status === 'paid') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'This booking is already paid. Please contact the shop to cancel it.');
        }

        $order->updateStatus('cancelled', $user->id);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Your booking has been cancelled.');
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