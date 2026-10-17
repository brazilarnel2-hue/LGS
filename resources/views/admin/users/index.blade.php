<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manage Users
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-4">
                <a href="{{ route('admin.users.create') }}"
                   class="bg-laundry-teal text-white px-4 py-2 rounded hover:bg-laundry-dark">
                    + Add User
                </a>
            </div>

            <div class="bg-white shadow overflow-hidden rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($users as $user)
                            <tr>
                                <td class="px-6 py-4">{{ $user->name }}</td>
                                <td class="px-6 py-4">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded bg-laundry-sky text-laundry-teal capitalize">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($user->is_active)
                                        <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">
                                            Active
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded bg-gray-200 text-gray-600">
                                            Deactivated
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-3">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-laundry-teal hover:underline">
                                        Change Role
                                    </a>

                                    @if ($user->id !== auth()->id() && in_array($user->role, ['staff', 'driver']))
                                        @if ($user->is_active)
                                            <form method="POST" action="{{ route('admin.users.deactivate', $user) }}" class="inline"
                                                  onsubmit="return confirm('Deactivate {{ $user->name }}\'s account?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-yellow-600 hover:underline">
                                                    Deactivate
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.users.activate', $user) }}" class="inline"
                                                  onsubmit="return confirm('Activate {{ $user->name }}\'s account?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-green-600 hover:underline">
                                                    Activate
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>