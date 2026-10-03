<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit User: {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <dl class="grid grid-cols-2 gap-4 text-sm mb-6">
                    <div>
                        <dt class="text-gray-500">Name</dt>
                        <dd>{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Email</dt>
                        <dd>{{ $user->email }}</dd>
                    </div>
                </dl>

                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Role</label>
                        <select name="role" class="mt-1 block w-full rounded border-gray-300">
                            <option value="customer" @selected($user->role === 'customer')>Customer</option>
                            <option value="staff" @selected($user->role === 'staff')>Staff</option>
                            <option value="driver" @selected($user->role === 'driver')>Driver</option>
                            <option value="admin" @selected($user->role === 'admin')>Admin</option>
                        </select>
                    </div>

                    <div class="flex justify-between items-center">
                        <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:underline">Cancel</a>
                        <button type="submit" class="bg-laundry-teal text-white px-4 py-2 rounded hover:bg-laundry-dark">
                            Save Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>