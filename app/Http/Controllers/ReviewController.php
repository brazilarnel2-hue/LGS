<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['user', 'order'])->latest()->paginate(10);

        $averageRating = Review::avg('rating');
        $totalReviews = Review::count();

        return view('reviews.index', compact('reviews', 'averageRating', 'totalReviews'));
    }

    public function store(Request $request, Order $order)
    {
        abort_unless($order->customer_id === Auth::id(), 403, 'Hindi mo pwedeng i-review ang booking na ito.');

        abort_unless($order->status === 'delivered', 403, 'Puwede lang mag-review kapag tapos na ang order.');

        if ($order->review) {
            return back()->with('error', 'Na-review mo na ang booking na ito.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        Review::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return back()->with('success', 'Salamat sa iyong review!');
    }
}