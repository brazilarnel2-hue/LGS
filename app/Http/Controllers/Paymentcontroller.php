<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    /**
     * Driver/staff records a manual payment as RECEIVED (COD collected in person,
     * or confirming a GCash payment handed over in person).
     */
    public function store(Request $request, Order $order)
    {
        $user = Auth::user();

        $canRecord = in_array($user->role, ['staff', 'admin']) || $order->driver_id === $user->id;

        abort_unless($canRecord, 403, 'You are not allowed to record payment for this order.');

        $validated = $request->validate([
            'method' => 'required|in:cod,gcash',
            'reference_number' => 'required_if:method,gcash|nullable|string|max:100',
        ]);

        $order->payment()->updateOrCreate(
            ['order_id' => $order->id],
            [
                'method' => $validated['method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'amount' => $order->total_amount,
                'status' => 'paid',
                'paid_at' => now(),
            ]
        );

        return redirect()->route('orders.show', $order)
            ->with('success', 'Payment recorded.');
    }

    /**
     * Customer selects "Cash on Delivery" — this just records their CHOICE.
     * It stays "pending" (not yet paid) until the driver/staff confirms
     * the cash was actually received upon delivery.
     */
    public function selectCod(Order $order)
    {
        $user = Auth::user();

        abort_unless($order->customer_id === $user->id, 403, 'This is not your booking.');
        abort_if($order->payment && $order->payment->status === 'paid', 403, 'This booking is already paid.');

        $order->payment()->updateOrCreate(
            ['order_id' => $order->id],
            [
                'method' => 'cod',
                'reference_number' => null,
                'amount' => $order->total_amount,
                'status' => 'pending',
                'paid_at' => null,
            ]
        );

        return redirect()->route('orders.show', $order)
            ->with('success', 'Cash on Delivery selected. Please prepare the exact amount for the driver.');
    }

    /**
     * Show the GCash checkout page with the shop's QR code
     * (customer only, for their own unpaid booking).
     */
    public function showGcashCheckout(Order $order)
    {
        $user = Auth::user();

        abort_unless($order->customer_id === $user->id, 403, 'This is not your booking.');
        abort_if($order->payment && $order->payment->status === 'paid', 403, 'This booking is already paid.');

        return view('orders.gcash-checkout', compact('order'));
    }

    /**
     * Customer submits the GCash reference number after paying via the shop's QR.
     * The payment stays "pending" until staff/admin verifies it in the GCash app.
     */
    public function confirmGcashPayment(Request $request, Order $order)
    {
        $user = Auth::user();

        abort_unless($order->customer_id === $user->id, 403, 'This is not your booking.');
        abort_if($order->payment && $order->payment->status === 'paid', 403, 'This booking is already paid.');

        $validated = $request->validate([
            'reference_number' => [
                'required',
                'string',
                'min:8',
                'max:30',
                // A reference number can't be reused on another booking
                Rule::unique('payments', 'reference_number')->ignore($order->payment?->id),
            ],
        ]);

        $order->payment()->updateOrCreate(
            ['order_id' => $order->id],
            [
                'method' => 'gcash',
                'reference_number' => trim($validated['reference_number']),
                'amount' => $order->total_amount,
                'status' => 'pending',
                'paid_at' => null,
            ]
        );

        return redirect()->route('orders.show', $order)
            ->with('success', 'Reference number submitted. We will verify your GCash payment shortly.');
    }

    /**
     * Staff/admin confirms that the GCash payment was received
     * (after checking the shop's GCash account).
     */
    public function verifyGcash(Order $order)
    {
        abort_unless(in_array(Auth::user()->role, ['staff', 'admin']), 403, 'Only staff can verify payments.');

        $payment = $order->payment;

        abort_unless(
            $payment && $payment->method === 'gcash' && $payment->status === 'pending',
            404,
            'No GCash payment is waiting for verification.'
        );

        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'GCash payment verified and marked as paid.');
    }
}