<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile picture, name, and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- Profile picture --}}
        <div x-data="{
                src: @js($user->profile_photo_url),
                initial: @js($user->initial),
                remove: false,
                choose(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    this.src = URL.createObjectURL(file);
                    this.remove = false;
                },
                clear() {
                    this.src = null;
                    this.remove = true;
                    this.$refs.photo.value = '';
                }
            }">
            <x-input-label for="photo" :value="__('Profile Picture')" />

            <div class="mt-2 flex items-center gap-4">
                <div class="h-24 w-24 shrink-0 rounded-full overflow-hidden bg-laundry-teal text-white flex items-center justify-center text-4xl font-semibold shadow">
                    <img x-show="src" :src="src" alt="Profile picture" class="h-full w-full object-cover">
                    <span x-show="!src" x-text="initial"></span>
                </div>

                <div>
                    <input type="file" id="photo" name="photo" x-ref="photo" class="hidden"
                           accept="image/jpeg,image/png,image/webp" @change="choose($event)">
                    <input type="hidden" name="remove_photo" :value="remove ? 1 : 0">

                    <div class="flex flex-wrap gap-2">
                        <button type="button" @click="$refs.photo.click()"
                                class="px-4 py-2 bg-laundry-teal text-white text-sm font-medium rounded-md hover:bg-laundry-dark">
                            {{ __('Choose photo') }}
                        </button>
                        <button type="button" x-show="src" @click="clear()"
                                class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                            {{ __('Remove photo') }}
                        </button>
                    </div>

                    <p class="mt-2 text-xs text-gray-500">JPG, PNG, or WebP. Max 2 MB.</p>
                </div>
            </div>

            <x-input-error class="mt-2" :messages="$errors->get('photo')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>