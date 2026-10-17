<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Customer Reviews
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white shadow rounded-lg p-6 flex items-center gap-6">
                <div class="text-center">
                    <div class="text-4xl font-bold text-laundry-teal">
                        {{ number_format($averageRating ?? 0, 1) }}
                    </div>
                    <div class="text-yellow-400 text-xl">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= round($averageRating ?? 0))
                                ★
                            @else
                                ☆
                            @endif
                        @endfor
                    </div>
                    <div class="text-sm text-gray-500 mt-1">
                        {{ $totalReviews }} {{ Str::plural('review', $totalReviews) }}
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="p-4 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="space-y-4">
                @forelse ($reviews as $review)
                    <div class="bg-white shadow rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="font-semibold text-gray-800">{{ $review->user->name }}</div>
                                <div class="text-yellow-400">
                                    @for ($i = 1; $i <= 5; $i++)
                                        {{ $i <= $review->rating ? '★' : '☆' }}
                                    @endfor
                                </div>
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ $review->created_at->format('M d, Y') }}
                            </div>
                        </div>
                        @if ($review->comment)
                            <p class="mt-2 text-gray-600">{{ $review->comment }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-500">Wala pang review.</p>
                @endforelse
            </div>

            <div>
                {{ $reviews->links() }}
            </div>
        </div>
    </div>
</x-app-layout>