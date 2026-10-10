<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'GoLaundry') }}</title>

    <x-favicon />

    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .auth-bg {
            position: relative;
            min-height: 100vh;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
            background-color: #0ea5e9;
            background-image: url('{{ asset('images/banner.jpg') }}');
            background-size: cover;
            background-position: center;
        }
        .auth-bg::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg,
                rgba(7,89,133,.85) 0%, rgba(2,132,199,.65) 55%, rgba(56,189,248,.45) 100%);
        }

        .auth-content {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 440px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .auth-logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            margin-bottom: 22px;
            color: #fff;
            text-decoration: none;
        }
        .auth-logo-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 88px;
            height: 88px;
            border-radius: 24px;
            background: rgba(255,255,255,.92);
            box-shadow: 0 10px 30px rgba(0,0,0,.25);
        }
        .auth-logo-badge svg, .auth-logo-badge img { width: 56px; height: 56px; }
        .auth-brand { font-size: 1.6rem; font-weight: 700; letter-spacing: -.02em; }

        .auth-card {
            width: 100%;
            padding: 28px;
            border-radius: 20px;
            background: rgba(255,255,255,.94);
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 50px rgba(0,0,0,.28);
        }

        .auth-back {
            margin-top: 18px;
            font-size: .9rem;
            color: #e0f2fe;
            text-decoration: none;
        }
        .auth-back:hover { color: #fff; text-decoration: underline; }
    </style>
</head>
<body style="margin:0; font-family: 'Figtree', 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; -webkit-font-smoothing: antialiased;">

    <div class="auth-bg">
        <div class="auth-content">
            <a href="/" class="auth-logo">
                <span class="auth-logo-badge">
                    <x-application-logo />
                </span>
                <span class="auth-brand">GoLaundry</span>
            </a>

            <div class="auth-card">
                {{ $slot }}
            </div>

            <a href="/" class="auth-back">&larr; Back to home</a>
        </div>
    </div>

</body>
</html>