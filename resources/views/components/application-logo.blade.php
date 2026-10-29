{{--
    Totoong logo ng shop (icon + "WAVES N' BUBBLES LAUNDRY SHOP" text),
    galing sa file na hiningi/ginawa para sa may-ari. Malinaw ito sa 1166x291
    resolution — gamitin ang class attribute para sa laki, hal.
    <x-application-logo class="h-10 w-auto" />
--}}
<img src="{{ asset('images/logo-full.png') }}"
     alt="Waves N' Bubbles Laundry Shop"
     {{ $attributes->merge(['class' => 'h-10 w-auto']) }}
     style="object-fit: contain;">