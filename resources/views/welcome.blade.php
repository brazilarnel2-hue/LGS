<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'GoLaundry') }}</title>
    <x-favicon />
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
            background-color: #0ea5e9;
            background-image: url('{{ asset('images/banner.jpg') }}');
            background-size: cover;
            background-position: center;
        }
        .banner::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg,
                rgba(7,89,133,.85) 0%, rgba(2,132,199,.60) 55%, rgba(56,189,248,.30) 100%);
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
        .btn-white { background: #fff; color: #0284c7; box-shadow: 0 6px 16px rgba(0,0,0,.18); }
        .btn-white:hover { background: #f0f9ff; transform: translateY(-1px); }
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
        .hero h1 span { color: #bae6fd; }
        .hero p { max-width: 540px; margin-top: 20px; font-size: 1.1rem; line-height: 1.6; color: #e0f2fe; }
        .hero-actions { margin-top: 32px; display: flex; flex-wrap: wrap; gap: 12px; }

        .wave { position: absolute; z-index: 2; bottom: -1px; left: 0; width: 100%; height: 110px; display: block; }
          /* ---------- Features ---------- */
        .features { position: relative; z-index: 3; margin-top: -40px; padding-bottom: 64px; }
        .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .card {
            background: #fff;
            border-radius: 18px;
            padding: 26px;
            box-shadow: 0 8px 24px rgba(2,132,199,.12);
            transition: transform .2s, box-shadow .2s;
        }
        .card:hover { transform: translateY(-4px); box-shadow: 0 14px 30px rgba(2,132,199,.18); }
        .card .icon { font-size: 2rem; margin-bottom: 10px; }
        .card h3 { font-size: 1.05rem; font-weight: 600; }
        .card p { margin-top: 6px; font-size: .92rem; color: #4b5563; line-height: 1.5; }

        /* ---------- Footer ---------- */
        .site-footer {
            background: linear-gradient(135deg, #075985 0%, #0284c7 100%);
            color: #e0f2fe;
            padding-top: 56px;
            border-radius: 32px 32px 0 0;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 1.6fr 1fr 1fr 1.4fr;
            gap: 36px;
            padding-bottom: 40px;
        }
        .footer-brand .logo { color: #fff; font-size: 1.5rem; }
        .footer-brand p { margin-top: 12px; max-width: 300px; font-size: .92rem; line-height: 1.6; color: #bae6fd; }

        .footer-col h4 {
            margin-bottom: 14px;
            font-size: .95rem;
            font-weight: 600;
            color: #fff;
            letter-spacing: .02em;
        }
        .footer-col ul { list-style: none; }
        .footer-col li { margin-bottom: 10px; font-size: .9rem; color: #bae6fd; }
        .footer-col a { transition: color .2s; }
        .footer-col a:hover { color: #fff; text-decoration: underline; }

        .socials { display: flex; gap: 10px; margin-top: 18px; }
        .socials a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255,255,255,.15);
            transition: background .2s, transform .2s;
        }
        .socials a:hover { background: rgba(255,255,255,.3); transform: translateY(-2px); }
        .socials svg { width: 18px; height: 18px; fill: #fff; }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,.2);
            padding: 18px 0 22px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 8px;
            font-size: .85rem;
            color: #bae6fd;
        }

        @media (max-width: 760px) {
            .grid { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
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

    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">

                <div class="footer-brand">
                    <span class="logo">GoLaundry</span>
                    <p>Fresh, clean laundry with pickup and delivery. Book in a few clicks and track your order anytime.</p>
                    <div class="socials">
                        <a href="#" aria-label="Facebook">
                            <svg viewBox="0 0 24 24"><path d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.24.2 2.24.2v2.47h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0 0 22 12z"/></svg>
                        </a>
                        <a href="#" aria-label="Instagram">
                            <svg viewBox="0 0 24 24"><path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5zm0 2a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H7zm5 3.5A4.5 4.5 0 1 1 7.5 12 4.5 4.5 0 0 1 12 7.5zm0 2A2.5 2.5 0 1 0 14.5 12 2.5 2.5 0 0 0 12 9.5zm5.2-3.2a1.1 1.1 0 1 1-1.1 1.1 1.1 1.1 0 0 1 1.1-1.1z"/></svg>
                        </a>
                    </div>
                </div>

                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="{{ url('/') }}">Home</a></li>
                        @guest
                            <li><a href="{{ route('login') }}">Log in</a></li>
                            @if (Route::has('register'))
                                <li><a href="{{ route('register') }}">Register</a></li>
                            @endif
                        @else
                            <li><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                        @endguest
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Our Services</h4>
                    <ul>
                        <li>Wash &amp; Fold</li>
                        <li>Pickup &amp; Delivery</li>
                        <li>Order Tracking</li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Contact Us</h4>
                    <ul>
                        <li>📍 Your shop address here</li>
                        <li>📞 09XX XXX XXXX</li>
                        <li>✉️ hello@golaundry.test</li>
                        <li>🕒 Mon–Sat, 8:00 AM – 6:00 PM</li>
                    </ul>
                </div>

            </div>

            <div class="footer-bottom">
                <span>&copy; {{ date('Y') }} GoLaundry. All rights reserved.</span>
                <span>Made with 💙 for fresh, clean laundry</span>
            </div>
        </div>
    </footer>

</body>
</html>