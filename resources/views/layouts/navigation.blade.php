<nav x-data="{ open: false }" class="bg-laundry-teal border-b border-teal-900">
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
                              {{ request()->routeIs('dashboard') ? 'border-white text-white' : 'border-transparent text-teal-100 hover:text-white hover:border-teal-200' }}">
                        Dashboard
                    </a>

                    <a href="{{ route('orders.index') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition
                              {{ request()->routeIs('orders.*') ? 'border-white text-white' : 'border-transparent text-teal-100 hover:text-white hover:border-teal-200' }}">
                        @if (auth()->user()->role === 'customer')
                            My Orders
                        @elseif (auth()->user()->role === 'driver')
                            My Deliveries
                        @else
                            All Orders
                        @endif
                    </a>

                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('admin.services.index') }}"
                           class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition
                                  {{ request()->routeIs('admin.services.*') ? 'border-white text-white' : 'border-transparent text-teal-100 hover:text-white hover:border-teal-200' }}">
                            Services
                        </a>
                        <a href="{{ route('admin.users.index') }}"
                           class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition
                                  {{ request()->routeIs('admin.users.*') ? 'border-white text-white' : 'border-transparent text-teal-100 hover:text-white hover:border-teal-200' }}">
                            Users
                        </a>
                    @endif
                </div>
            </div>

            <!-- User dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-white hover:bg-teal-800 focus:outline-none transition">
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
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-teal-800 focus:outline-none transition">
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
               class="block px-4 py-2 text-base font-medium {{ request()->routeIs('dashboard') ? 'text-white bg-teal-800' : 'text-teal-100 hover:text-white hover:bg-teal-800' }}">
                Dashboard
            </a>
            <a href="{{ route('orders.index') }}"
               class="block px-4 py-2 text-base font-medium {{ request()->routeIs('orders.*') ? 'text-white bg-teal-800' : 'text-teal-100 hover:text-white hover:bg-teal-800' }}">
                @if (auth()->user()->role === 'customer')
                    My Orders
                @elseif (auth()->user()->role === 'driver')
                    My Deliveries
                @else
                    All Orders
                @endif
            </a>

            @if (auth()->user()->role === 'admin')
                <a href="{{ route('admin.services.index') }}"
                   class="block px-4 py-2 text-base font-medium {{ request()->routeIs('admin.services.*') ? 'text-white bg-teal-800' : 'text-teal-100 hover:text-white hover:bg-teal-800' }}">
                    Services
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="block px-4 py-2 text-base font-medium {{ request()->routeIs('admin.users.*') ? 'text-white bg-teal-800' : 'text-teal-100 hover:text-white hover:bg-teal-800' }}">
                    Users
                </a>
            @endif
        </div>

        <div class="pt-4 pb-3 border-t border-teal-800">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-teal-200">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-base font-medium text-teal-100 hover:text-white hover:bg-teal-800">
                    Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-base font-medium text-teal-100 hover:text-white hover:bg-teal-800">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>