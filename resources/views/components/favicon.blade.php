@php
    $setting = \App\Models\Setting::current();

    if ($setting->logo_path) {
        $href = asset('storage/' . $setting->logo_path);

        // Para mag-refresh ang favicon kapag may bagong upload
        $version = optional($setting->updated_at)->timestamp ?? 1;

        $mime = match (strtolower(pathinfo($setting->logo_path, PATHINFO_EXTENSION))) {
            'svg'         => 'image/svg+xml',
            'jpg', 'jpeg' => 'image/jpeg',
            default       => 'image/png',
        };
    } else {
        // Fallback kung wala pang na-upload na logo
        $href = asset('favicon.svg');
        $version = 1;
        $mime = 'image/svg+xml';
    }
@endphp

<link rel="icon" type="{{ $mime }}" href="{{ $href }}?v={{ $version }}">