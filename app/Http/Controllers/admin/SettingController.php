<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    private function ensureAdmin()
    {
        abort_unless(Auth::user()->role === 'admin', 403, 'Only admins can manage site settings.');
    }

    public function edit()
    {
        $this->ensureAdmin();
        $setting = Setting::current();
        return view('admin.settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $this->ensureAdmin();

        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,svg|max:1024',
        ]);

        $setting = Setting::current();

        // Delete the old uploaded logo file, if any.
        if ($setting->logo_path) {
            Storage::disk('public')->delete($setting->logo_path);
        }

        $path = $request->file('logo')->store('logos', 'public');
        $setting->update(['logo_path' => $path]);

        return redirect()->route('admin.settings.edit')->with('success', 'Logo updated.');
    }

    public function reset()
    {
        $this->ensureAdmin();

        $setting = Setting::current();

        if ($setting->logo_path) {
            Storage::disk('public')->delete($setting->logo_path);
        }

        $setting->update(['logo_path' => null]);

        return redirect()->route('admin.settings.edit')->with('success', 'Logo reset to default.');
    }
}