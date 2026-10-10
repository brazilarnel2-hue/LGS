@php
    $role = auth()->user()->role;

    $ordersLabel = match ($role) {
        'customer' => 'My Bookings',
        'driver'   => 'My Deliveries',
        default    => 'All Bookings',
    };
@endphp

<nav x-data="{ open: false }" class="bg-laundry-teal border-b border-sky-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo + brand name -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <x-application-logo class="block h-10 w-auto text-white" />
                        <span class="text-white font-bold text-xl tracking-wide">GoLaundry</span>
                    </a>
                </div>

                <!-- Desktop Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition
                              {{ request()->routeIs('dashboard') ? 'border-white text-white' : 'border-transparent text-sky-100 hover:text-white hover:border-sky-200' }}">
                        Dashboard
                    </a>

                    <a href="{{ route('orders.index') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition
                              {{ request()->routeIs('orders.*') ? 'border-white text-white' : 'border-transparent text-sky-100 hover:text-white hover:border-sky-200' }}">
                        {{ $ordersLabel }}
                    </a>

                    @if ($role === 'admin')
                        <a href="{{ route('admin.services.index') }}"
                           class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition
                                  {{ request()->routeIs('admin.services.*') ? 'border-white text-white' : 'border-transparent text-sky-100 hover:text-white hover:border-sky-200' }}">
                            Services
                        </a>
                        <a href="{{ route('admin.users.index') }}"
                           class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition
                                  {{ request()->routeIs('admin.users.*') ? 'border-white text-white' : 'border-transparent text-sky-100 hover:text-white hover:border-sky-200' }}">
                            Users
                        </a>
                    @endif
                </div>
            </div>

            <!-- User dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-white hover:bg-sky-800 focus:outline-none transition">
                            <span class="me-2 h-8 w-8 shrink-0 rounded-full overflow-hidden bg-white/20 flex items-center justify-center text-sm font-semibold">
                                @if (Auth::user()->profile_photo_url)
                                    <img src="{{ Auth::user()->profile_photo_url }}" alt="" class="h-full w-full object-cover">
                                @else
                                    {{ Auth::user()->initial }}
                                @endif
                            </span>
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        {{-- Settings (para magpalit ng logo) - admin lang --}}
                        @if ($role === 'admin')
                            <x-dropdown-link :href="route('admin.settings.edit')">
                                {{ __('Settings') }}
                            </x-dropdown-link>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-sky-800 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-laundry-teal">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}"
               class="block px-4 py-2 text-base font-medium {{ request()->routeIs('dashboard') ? 'text-white bg-sky-800' : 'text-sky-100 hover:text-white hover:bg-sky-800' }}">
                Dashboard
            </a>
            <a href="{{ route('orders.index') }}"
               class="block px-4 py-2 text-base font-medium {{ request()->routeIs('orders.*') ? 'text-white bg-sky-800' : 'text-sky-100 hover:text-white hover:bg-sky-800' }}">
                {{ $ordersLabel }}
            </a>

            @if ($role === 'admin')
                <a href="{{ route('admin.services.index') }}"
                   class="block px-4 py-2 text-base font-medium {{ request()->routeIs('admin.services.*') ? 'text-white bg-sky-800' : 'text-sky-100 hover:text-white hover:bg-sky-800' }}">
                    Services
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="block px-4 py-2 text-base font-medium {{ request()->routeIs('admin.users.*') ? 'text-white bg-sky-800' : 'text-sky-100 hover:text-white hover:bg-sky-800' }}">
                    Users
                </a>
            @endif
        </div>

        <div class="pt-4 pb-3 border-t border-sky-800">
            <div class="px-4 flex items-center gap-3">
                <span class="h-10 w-10 shrink-0 rounded-full overflow-hidden bg-white/20 flex items-center justify-center text-base font-semibold text-white">
                    @if (Auth::user()->profile_photo_url)
                        <img src="{{ Auth::user()->profile_photo_url }}" alt="" class="h-full w-full object-cover">
                    @else
                        {{ Auth::user()->initial }}
                    @endif
                </span>
                <div>
                    <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-sky-200">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-base font-medium text-sky-100 hover:text-white hover:bg-sky-800">
                    Profile
                </a>

                {{-- Settings (para magpalit ng logo) - admin lang --}}
                @if ($role === 'admin')
                    <a href="{{ route('admin.settings.edit') }}"
                       class="block px-4 py-2 text-base font-medium {{ request()->routeIs('admin.settings.*') ? 'text-white bg-sky-800' : 'text-sky-100 hover:text-white hover:bg-sky-800' }}">
                        Settings
                    </a>
                @endif

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-base font-medium text-sky-100 hover:text-white hover:bg-sky-800">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>