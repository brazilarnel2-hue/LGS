<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Service
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <form method="POST" action="{{ route('admin.services.update', $service) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" value="{{ old('name', $service->name) }}" class="mt-1 block w-full rounded border-gray-300" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Description (optional)</label>
                        <textarea name="description" rows="2" class="mt-1 block w-full rounded border-gray-300">{{ old('description', $service->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Price (₱)</label>
                            <input type="number" step="0.01" name="price" value="{{ old('price', $service->price) }}" class="mt-1 block w-full rounded border-gray-300" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Unit</label>
                            <select name="unit" class="mt-1 block w-full rounded border-gray-300">
                                <option value="kg" @selected($service->unit === 'kg')>kg</option>
                                <option value="item" @selected($service->unit === 'item')>item</option>
                                <option value="load" @selected($service->unit === 'load')>load</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4 flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1" @checked($service->is_active) class="rounded">
                        <label for="is_active" class="text-sm text-gray-700">Active (visible to customers)</label>
                    </div>

                    <div class="flex justify-between items-center">
                        <a href="{{ route('admin.services.index') }}" class="text-gray-600 hover:underline">Cancel</a>
                        <button type="submit" class="bg-laundry-teal text-white px-4 py-2 rounded hover:bg-laundry-dark">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>