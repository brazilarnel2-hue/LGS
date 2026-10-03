<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'GoLaundry') }}</title>

    @fonts

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: "Instrument Sans", ui-sans-serif, system-ui, sans-serif;
            background: #f9fafb;
            color: #1f2937;
            -webkit-font-smoothing: antialiased;
        }
        a { color: inherit; text-decoration: none; }

        /* ---------- Banner ---------- */
        .banner {
            position: relative;
            overflow: hidden;
            color: #fff;
            background-color: #0f766e;
            background-image: url('{{ asset('images/banner.jpg') }}');
            background-size: cover;
            background-position: center;
        }
        .banner::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg,
                rgba(19,78,74,.88) 0%, rgba(17,94,89,.62) 55%, rgba(13,148,136,.30) 100%);
        }
        .container { position: relative; z-index: 2; max-width: 1152px; margin: 0 auto; padding: 0 24px; }

        .nav { display: flex; align-items: center; justify-content: space-between; padding-top: 22px; padding-bottom: 22px; }
        .logo { font-size: 1.6rem; font-weight: 700; letter-spacing: -.02em; }
        .nav-links { display: flex; gap: 10px; align-items: center; }

        .btn {
            display: inline-block;
            padding: 10px 22px;
            border-radius: 10px;
            font-weight: 600;
            font-size: .95rem;
            transition: background .2s, transform .2s;
        }
        .btn-white { background: #fff; color: #0f766e; box-shadow: 0 6px 16px rgba(0,0,0,.18); }
        .btn-white:hover { background: #f0fdfa; transform: translateY(-1px); }
        .btn-ghost { border: 1px solid rgba(255,255,255,.75); color: #fff; }
        .btn-ghost:hover { background: rgba(255,255,255,.15); }
        .btn-lg { padding: 14px 28px; font-size: 1rem; }

        .hero { padding-top: 80px; padding-bottom: 170px; }
        .badge {
            display: inline-block;
            padding: 6px 16px;
            margin-bottom: 18px;
            border-radius: 999px;
            background: rgba(255,255,255,.2);
            backdrop-filter: blur(6px);
            font-size: .85rem;
        }
        .hero h1 { max-width: 680px; font-size: clamp(2.2rem, 6vw, 3.8rem); line-height: 1.1; font-weight: 700; }
        .hero h1 span { color: #cffafe; }
        .hero p { max-width: 540px; margin-top: 20px; font-size: 1.1rem; line-height: 1.6; color: #ccfbf1; }
        .hero-actions { margin-top: 32px; display: flex; flex-wrap: wrap; gap: 12px; }

        .wave { position: absolute; z-index: 2; bottom: -1px; left: 0; width: 100%; height: 110px; display: block; }

        /* ---------- Bubbles ---------- */
        .bubbles { position: absolute; inset: 0; z-index: 1; pointer-events: none; overflow: hidden; }
        .bubble {
            position: absolute;
            bottom: -150px;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%,
                rgba(255,255,255,.95), rgba(255,255,255,.25) 55%, rgba(255,255,255,.08));
            border: 1px solid rgba(255,255,255,.7);
            box-shadow: inset 0 0 12px rgba(255,255,255,.5);
            animation: rise linear infinite;
        }
        @keyframes rise {
            0%   { transform: translate(0, 0); opacity: 0; }
            10%  { opacity: 1; }
            100% { transform: translate(40px, -120vh); opacity: 0; }
        }
        @media (prefers-reduced-motion: reduce) {
            .bubble { animation: none; opacity: .6; bottom: 20%; }
        }

        /* ---------- Features ---------- */
        .features { position: relative; z-index: 3; margin-top: -40px; padding-bottom: 64px; }
        .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .card {
            background: #fff;
            border-radius: 18px;
            padding: 26px;
            box-shadow: 0 8px 24px rgba(15,118,110,.12);
            transition: transform .2s, box-shadow .2s;
        }
        .card:hover { transform: translateY(-4px); box-shadow: 0 14px 30px rgba(15,118,110,.18); }
        .card .icon { font-size: 2rem; margin-bottom: 10px; }
        .card h3 { font-size: 1.05rem; font-weight: 600; }
        .card p { margin-top: 6px; font-size: .92rem; color: #4b5563; line-height: 1.5; }

        footer { text-align: center; padding: 0 0 32px; font-size: .85rem; color: #6b7280; }

        @media (max-width: 760px) {
            .grid { grid-template-columns: 1fr; }
            .hero { padding-top: 48px; padding-bottom: 150px; }
        }
    </style>
</head>
<body>

    <section class="banner">
        <div class="bubbles">
            @foreach (range(1, 12) as $i)
                @php $size = rand(20, 80); @endphp
                <span class="bubble"
                      style="left: {{ rand(0, 95) }}%;
                             width: {{ $size }}px; height: {{ $size }}px;
                             animation-duration: {{ rand(10, 22) }}s;
                             animation-delay: -{{ rand(0, 20) }}s;"></span>
            @endforeach
        </div>

        <div class="container">
            <header class="nav">
                <span class="logo">GoLaundry</span>
                <nav class="nav-links">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-white">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-ghost" style="border-color: transparent;">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-white">Register</a>
                        @endif
                    @endauth
                </nav>
            </header>

            <div class="hero">
                <p class="badge" style="max-width: none; margin-top: 0; font-size: .85rem; color: #fff;">🧺 Pickup &amp; delivery laundry service</p>
                <h1>Fresh, clean laundry, <span>hassle-free.</span></h1>
                <p>Book your laundry service in a few clicks and track your order from drop-off to pick-up.</p>

                <div class="hero-actions">
                    @guest
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-white btn-lg">Get Started</a>
                        @endif
                        <a href="{{ route('login') }}" class="btn btn-ghost btn-lg">Log in</a>
                    @else
                        <a href="{{ url('/dashboard') }}" class="btn btn-white btn-lg">Go to Dashboard</a>
                    @endguest
                </div>
            </div>
        </div>

        <svg class="wave" viewBox="0 0 1440 120" preserveAspectRatio="none" aria-hidden="true">
            <path fill="#f9fafb" d="M0,64 C240,120 480,0 720,40 C960,80 1200,110 1440,50 L1440,120 L0,120 Z"/>
        </svg>
    </section>

    <section class="features">
        <div class="container">
            <div class="grid">
                <div class="card">
                    <div class="icon">🧼</div>
                    <h3>Wash &amp; Fold</h3>
                    <p>Clean, folded, and ready to wear.</p>
                </div>
                <div class="card">
                    <div class="icon">🚚</div>
                    <h3>Pickup &amp; Delivery</h3>
                    <p>We come to you, so you save time.</p>
                </div>
                <div class="card">
                    <div class="icon">📱</div>
                    <h3>Track Your Order</h3>
                    <p>See the status of your laundry anytime.</p>
                </div>
            </div>
        </div>
    </section>

    <footer>&copy; {{ date('Y') }} LaundryGo</footer>

</body>
</html>