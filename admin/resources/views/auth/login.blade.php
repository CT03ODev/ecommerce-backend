<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="container">
        <div class="w-full mx-auto sm:max-w-md mt-6 px-6 py-8 bg-white dark:bg-gray-800 shadow-card overflow-hidden sm:rounded-lg">
            <div class="mb-8 flex justify-center">
                <h1 class="text-xl font-bold" >{{ config('app.title') }}</h1>
            </div>
            <form method="POST" action="{{ route('login') }}">
                @csrf
            
                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
            
                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" />
            
                    <x-text-input id="password" class="block mt-1 w-full"
                                    type="password"
                                    name="password"
                                    required autocomplete="current-password" />
            
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
            
                <!-- Remember Me -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary-600 shadow-sm focus:ring-primary-500 dark:focus:ring-primary-600 dark:focus:ring-offset-gray-800" name="remember">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <div class="mt-4">
                    <x-input-recaptcha />
                    <x-input-error :messages="$errors->get('g-recaptcha-response')" class="mt-2" />
                </div>
            
                <div class="flex items-center justify-end mt-4">
                    <x-primary-button class="ml-3">
                        {{ __('Log in') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
