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
        html { scroll-behavior: smooth; }
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
            background-color: #0e6e8c;
            background-image: url('{{ asset('images/hero-bg.png') }}');
            background-size: cover;
            background-position: center;
        }
        .banner::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg,
                rgba(5,60,78,.55) 0%, rgba(5,60,78,.25) 45%, rgba(5,60,78,0) 70%);
        }
        .container { position: relative; z-index: 2; max-width: 1152px; margin: 0 auto; padding: 0 24px; }

        .nav { display: flex; align-items: center; justify-content: space-between; flex-wrap: nowrap; padding-top: 22px; padding-bottom: 22px; gap: 18px; }

        .nav-brand { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
        .nav-brand img, .nav-brand svg { height: 38px; width: auto; flex-shrink: 0; }
        .logo { font-size: 1.25rem; font-weight: 700; letter-spacing: -.02em; white-space: nowrap; }

        .nav-links { display: flex; gap: 10px; align-items: center; flex-shrink: 0; }

        .nav-menu { display: flex; gap: 26px; align-items: center; flex-shrink: 1; min-width: 0; }
        .nav-menu a {
            font-size: .92rem;
            font-weight: 500;
            color: #e0f2fe;
            padding: 6px 2px;
            position: relative;
            white-space: nowrap;
            transition: color .2s;
        }
        .nav-menu a::after {
            content: "";
            position: absolute;
            left: 0; right: 0; bottom: -2px;
            height: 2px;
            background: #fff;
            transform: scaleX(0);
            transform-origin: left;
            transition: transform .2s;
        }
        .nav-menu a:hover { color: #fff; }
        .nav-menu a:hover::after { transform: scaleX(1); }

        .nav-toggle {
            display: none;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 9px;
            background: rgba(255,255,255,.15);
            color: #fff;
            flex-shrink: 0;
            cursor: pointer;
            border: none;
        }
        .nav-toggle svg { width: 20px; height: 20px; }

        .nav-phone {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: .88rem;
            font-weight: 600;
            color: #fff;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .nav-phone svg { width: 17px; height: 17px; flex-shrink: 0; }

        @media (max-width: 980px) {
            .nav-phone { display: none; }
        }

        @media (max-width: 760px) {
            .nav-menu {
                display: none;
                position: absolute;
                top: 100%; left: 0; right: 0;
                flex-direction: column;
                align-items: flex-start;
                gap: 2px;
                background: #075985;
                padding: 10px 24px 18px;
                box-shadow: 0 10px 20px rgba(0,0,0,.2);
            }
            .nav-menu.open { display: flex; }
            .nav-menu a { padding: 10px 0; width: 100%; }
            .nav-toggle { display: flex; }
            .logo { font-size: 1.05rem; }
        }

        /* ---------- Sticky site nav ---------- */
        .site-nav {
            position: fixed;
            top: 0; left: 0;
            width: 100%;
            z-index: 100;
            background: linear-gradient(90deg, #075985 0%, #0284c7 100%);
            box-shadow: 0 2px 14px rgba(0,0,0,.12);
            transition: box-shadow .3s ease;
        }
        .site-nav .nav { padding-top: 16px; padding-bottom: 16px; transition: padding .3s ease; }
        .site-nav.scrolled {
            box-shadow: 0 4px 20px rgba(0,0,0,.22);
        }
        .site-nav.scrolled .nav { padding-top: 12px; padding-bottom: 12px; }

        /* push page content below the now-solid fixed navbar */
        .nav-spacer { height: 68px; }

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

        .hero { padding-top: 48px; padding-bottom: 190px; }
        .hero-text { max-width: 560px; }

        @media (max-width: 900px) {
            .hero { padding-bottom: 90px; }
        }
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
        .features { position: relative; z-index: 3; margin-top: -40px; padding-bottom: 64px; scroll-margin-top: 24px; }
        .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .card {
            background: #fff;
            border-radius: 18px;
            padding: 26px;
            box-shadow: 0 8px 24px rgba(2,132,199,.12);
            transition: transform .2s, box-shadow .2s;
        }
        .card:hover { transform: translateY(-4px); box-shadow: 0 14px 30px rgba(2,132,199,.18); }
        .icon-box {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 54px;
            height: 54px;
            border-radius: 14px;
            margin-bottom: 14px;
            background: linear-gradient(135deg, #e0f2fe, #bae6fd);
            color: #0284c7;
        }
        .icon-box svg { width: 26px; height: 26px; }
        .card h3 { font-size: 1.05rem; font-weight: 600; }
        .card p { margin-top: 6px; font-size: .92rem; color: #4b5563; line-height: 1.5; }

        /* ---------- How It Works ---------- */
        .how-it-works { padding: 70px 0; scroll-margin-top: 24px; }
        .section-head { text-align: center; max-width: 560px; margin: 0 auto 44px; }
        .section-head .eyebrow {
            display: inline-block;
            font-size: .8rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #0284c7;
            margin-bottom: 10px;
        }
        .section-head h2 { font-size: clamp(1.6rem, 4vw, 2.2rem); font-weight: 700; color: #0f172a; }
        .section-head p { margin-top: 10px; color: #4b5563; font-size: 1rem; }

        .steps {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            position: relative;
        }
        .step {
            background: #fff;
            border-radius: 18px;
            padding: 28px 22px;
            box-shadow: 0 8px 24px rgba(2,132,199,.08);
            text-align: center;
            position: relative;
        }
        .step-number {
            width: 42px;
            height: 42px;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: linear-gradient(135deg, #0284c7, #0ea5e9);
            color: #fff;
            font-weight: 700;
            font-size: 1.05rem;
        }
        .step .icon-box {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            margin: 0 auto 10px;
            background: linear-gradient(135deg, #e0f2fe, #bae6fd);
            color: #0284c7;
        }
        .step .icon-box svg { width: 22px; height: 22px; }
        .step h3 { font-size: 1rem; font-weight: 600; color: #0f172a; }
        .step p { margin-top: 6px; font-size: .88rem; color: #4b5563; line-height: 1.5; }

        @media (max-width: 760px) {
            .steps { grid-template-columns: 1fr 1fr; }
        }

        /* ---------- Testimonials ---------- */
        .testimonials { padding: 70px 0; background: #f9fafb; scroll-margin-top: 24px; }
        .rating-summary {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin: -24px auto 36px;
            color: #4b5563;
            font-size: .95rem;
        }
        .rating-summary .stars { color: #f59e0b; letter-spacing: 2px; }
        .testimonial-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        .testimonial-card {
            background: #fff;
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 8px 24px rgba(2,132,199,.08);
        }
        .testimonial-card .stars { color: #f59e0b; letter-spacing: 2px; font-size: .95rem; }
        .testimonial-card p.comment {
            margin-top: 10px;
            color: #334155;
            font-size: .92rem;
            line-height: 1.6;
        }
        .testimonial-card .author {
            margin-top: 14px;
            font-weight: 600;
            font-size: .88rem;
            color: #0f172a;
        }
        .testimonials-empty {
            text-align: center;
            color: #6b7280;
            font-size: .95rem;
            padding: 24px 0;
        }

        @media (max-width: 900px) {
            .testimonial-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 640px) {
            .testimonial-grid { grid-template-columns: 1fr; }
        }

        /* ---------- Footer ---------- */
        .site-footer {
            background: linear-gradient(135deg, #075985 0%, #0284c7 100%);
            color: #e0f2fe;
            padding-top: 56px;
            border-radius: 32px 32px 0 0;
            scroll-margin-top: 24px;
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
            .hero { padding-top: 16px; padding-bottom: 70px; }
        }
    </style>
</head>
<body>

    <header class="site-nav" id="siteNav">
        <div class="container nav">
            <button type="button" class="nav-toggle" id="navToggle" aria-label="Toggle menu" aria-expanded="false">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>

            <a href="{{ url('/') }}" class="nav-brand">
                <x-application-logo />
            </a>

            <nav class="nav-menu" id="navMenu">
                <a href="{{ url('/') }}">Home</a>
                <a href="{{ url('/#features') }}">Services</a>
                <a href="{{ url('/#how-it-works') }}">How It Works</a>
                <a href="{{ url('/#testimonials') }}">Reviews</a>
                <a href="{{ url('/#contact') }}">Contact</a>
            </nav>

            <a href="tel:09XXXXXXXXX" class="nav-phone">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                </svg>
                09XX XXX XXXX
            </a>

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
        </div>
    </header>

    <div class="nav-spacer"></div>

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
            <div class="hero">
                <div class="hero-text">
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
        </div>

        <svg class="wave" viewBox="0 0 1440 120" preserveAspectRatio="none" aria-hidden="true">
            <path fill="#f9fafb" d="M0,64 C240,120 480,0 720,40 C960,80 1200,110 1440,50 L1440,120 L0,120 Z"/>
        </svg>
    </section>

    <section class="features" id="features">
        <div class="container">
            <div class="grid">
                <div class="card">
                    <div class="icon-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2.5"/>
                            <circle cx="12" cy="13" r="5.5"/>
                            <circle cx="12" cy="13" r="2.3"/>
                            <circle cx="7.3" cy="6.3" r=".9" fill="currentColor" stroke="none"/>
                        </svg>
                    </div>
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

    <section class="how-it-works" id="how-it-works">
        <div class="container">
            <div class="section-head">
                <span class="eyebrow">How It Works</span>
                <h2>Laundry day, sorted in 4 easy steps</h2>
                <p>From booking to clean clothes at your door — we handle the rest.</p>
            </div>

            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="icon">📲</div>
                    <h3>Book Online</h3>
                    <p>Choose your service and schedule a pickup in just a few clicks.</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="icon">🚗</div>
                    <h3>We Pick Up</h3>
                    <p>Our driver collects your laundry right at your doorstep.</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="icon">🧺</div>
                    <h3>We Wash &amp; Fold</h3>
                    <p>Your clothes are cleaned, dried, and neatly folded with care.</p>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <div class="icon">🏠</div>
                    <h3>We Deliver</h3>
                    <p>Fresh, clean laundry delivered back to you — track it the whole way.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="testimonials" id="testimonials">
        <div class="container">
            <div class="section-head">
                <span class="eyebrow">Testimonials</span>
                <h2>What our customers say</h2>
                <p>Real feedback from real bookings.</p>
            </div>

            @if ($totalReviews > 0)
                <div class="rating-summary">
                    <span class="stars">{{ str_repeat('★', round($averageRating)) }}{{ str_repeat('☆', 5 - round($averageRating)) }}</span>
                    <span>{{ number_format($averageRating, 1) }} out of 5 ({{ $totalReviews }} {{ Str::plural('review', $totalReviews) }})</span>
                </div>
            @endif

            @if ($testimonials->isNotEmpty())
                <div class="testimonial-grid">
                    @foreach ($testimonials as $review)
                        <div class="testimonial-card">
                            <div class="stars">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</div>
                            <p class="comment">"{{ $review->comment }}"</p>
                            <p class="author">— {{ $review->user->name ?? 'Customer' }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="testimonials-empty">No reviews yet — be the first to book and share your experience!</p>
            @endif
        </div>
    </section>

    <footer class="site-footer" id="contact">
        <div class="container">
            <div class="footer-grid">

                <div class="footer-brand">
                    <span class="logo">WAVES N' BUBBLES LAUNDRYSHOP</span>
                    <p>A Web-Based Laundry Pickup and Delivery Management System proudly serving Abuyog, Leyte. Book in a few clicks and track your order anytime.</p>
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
                        <li><a href="{{ url('/#features') }}">Wash &amp; Fold</a></li>
                        <li><a href="{{ url('/#features') }}">Pickup &amp; Delivery</a></li>
                        <li>
                            @auth
                                <a href="{{ route('orders.index') }}">Order Tracking</a>
                            @else
                                <a href="{{ route('login') }}">Order Tracking</a>
                            @endauth
                        </li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Contact Us</h4>
                    <ul>
                        <li><a href="https://maps.app.goo.gl/UXQkwY5h3xLhVcJq6" target="_blank" rel="noopener">📍 Maharlika Highway, Brgy. Bunga, Abuyog, Leyte</a></li>
                        <li><a href="tel:09XXXXXXXXX">📞 09XX XXX XXXX</a></li>
                        <li><a href="mailto:hello@golaundry.test">✉️ hello@golaundry.test</a></li>
                        <li>🕒 Mon–Sat, 8:00 AM – 6:00 PM</li>
                    </ul>
                </div>

            </div>

            <div class="footer-bottom">
                <span>&copy; {{ date('Y') }} WAVES N' BUBBLES LAUNDRYSHOP. All rights reserved.</span>
                <span>Made with 💙 for fresh, clean laundry</span>
            </div>
        </div>
    </footer>

    <script>
        // Navbar gets a slightly deeper shadow once the page is scrolled.
        var siteNav = document.getElementById('siteNav');
        function updateNavShadow() {
            if (window.scrollY > 10) {
                siteNav.classList.add('scrolled');
            } else {
                siteNav.classList.remove('scrolled');
            }
        }
        updateNavShadow();
        window.addEventListener('scroll', updateNavShadow);

        // Mobile hamburger toggle.
        var navToggle = document.getElementById('navToggle');
        var navMenu = document.getElementById('navMenu');
        navToggle.addEventListener('click', function () {
            var isOpen = navMenu.classList.toggle('open');
            navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
        navMenu.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                navMenu.classList.remove('open');
                navToggle.setAttribute('aria-expanded', 'false');
            });
        });
    </script>

</body>
</html>