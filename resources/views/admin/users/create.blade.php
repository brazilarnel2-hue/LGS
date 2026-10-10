<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Add User
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto px-4" style="max-width: 480px;">
            <div class="bg-white shadow rounded-lg" style="padding: 20px 24px;">

                <form method="POST" action="{{ route('admin.users.store') }}" autocomplete="off">
                    @csrf

                    <div style="margin-bottom: 14px;">
                        <label for="name" class="block text-sm font-medium text-gray-700" style="margin-bottom: 4px;">Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                               class="block w-full rounded-md border-gray-300 shadow-sm text-sm"
                               style="padding: 8px 12px;">
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div style="margin-bottom: 14px;">
                        <label for="email" class="block text-sm font-medium text-gray-700" style="margin-bottom: 4px;">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required
                               autocomplete="off"
                               class="block w-full rounded-md border-gray-300 shadow-sm text-sm"
                               style="padding: 8px 12px;">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div style="margin-bottom: 14px;">
                        <label for="role" class="block text-sm font-medium text-gray-700" style="margin-bottom: 4px;">Role</label>
                        <select id="role" name="role" required
                                class="block w-full rounded-md border-gray-300 shadow-sm text-sm"
                                style="padding: 8px 12px;">
                            <option value="customer" @selected(old('role') === 'customer')>Customer</option>
                            <option value="driver" @selected(old('role') === 'driver')>Driver</option>
                            <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                        </select>
                        @error('role') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div style="margin-bottom: 14px;">
                        <label for="password" class="block text-sm font-medium text-gray-700" style="margin-bottom: 4px;">Password</label>
                        <input id="password" name="password" type="password" required
                               autocomplete="new-password"
                               class="block w-full rounded-md border-gray-300 shadow-sm text-sm"
                               style="padding: 8px 12px;">
                        @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div style="margin-bottom: 18px;">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700" style="margin-bottom: 4px;">Confirm Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               autocomplete="new-password"
                               class="block w-full rounded-md border-gray-300 shadow-sm text-sm"
                               style="padding: 8px 12px;">
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
                        <button type="submit"
                                class="bg-laundry-teal text-white text-sm px-4 py-2 rounded hover:bg-laundry-dark">
                            Create User
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>