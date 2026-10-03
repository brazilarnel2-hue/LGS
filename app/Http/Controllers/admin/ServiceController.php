<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    private function ensureAdmin()
    {
        abort_unless(Auth::user()->role === 'admin', 403, 'Only admins can manage services.');
    }

    public function index()
    {
        $this->ensureAdmin();
        $services = Service::latest()->get();
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        $this->ensureAdmin();
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:20',
        ]);

        Service::create($validated + ['is_active' => true]);

        return redirect()->route('admin.services.index')->with('success', 'Service added.');
    }

    public function edit(Service $service)
    {
        $this->ensureAdmin();
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $service->update($validated);

        return redirect()->route('admin.services.index')->with('success', 'Service updated.');
    }

    public function destroy(Service $service)
    {
        $this->ensureAdmin();
        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Service removed.');
    }
}