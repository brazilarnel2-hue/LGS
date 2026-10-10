<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Site Settings
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="font-semibold mb-1">Site Logo</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Dito mo mapapalitan ang logo na lumalabas sa navigation bar ng buong site.
                </p>

                <div class="mb-6">
                    <p class="text-sm text-gray-500 mb-2">Current logo:</p>
                    <div class="flex items-center gap-3 bg-laundry-teal rounded-lg p-4 w-fit">
                        <x-application-logo class="block h-12 w-auto text-white" />
                        <span class="text-white font-bold text-lg">GoLaundry</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="mb-4">
                    @csrf
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload new logo</label>
                    <input type="file" name="logo" accept="image/png,image/jpeg,image/svg+xml"
                           class="block w-full text-sm border border-gray-300 rounded cursor-pointer mb-1">
                    <p class="text-xs text-gray-500 mb-3">PNG, JPG, or SVG. Max 1MB. Square images work best.</p>

                    <button type="submit" class="bg-laundry-teal text-white px-4 py-2 rounded hover:bg-laundry-dark">
                        Upload Logo
                    </button>
                </form>

                @if ($setting->logo_path)
                    <form method="POST" action="{{ route('admin.settings.reset') }}"
                          onsubmit="return confirm('Reset to the default GoLaundry logo?');">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:underline">
                            Reset to default logo
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>