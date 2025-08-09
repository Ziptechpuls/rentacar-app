<x-guest-layout>
    <!-- Session Status -->
    <x-admin::auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('admin.login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-admin::input-label for="email" :value="__('Email')" />
            <x-admin::text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-admin::input-error :messages="$errors->get('email')" class="mt-2" />
        </div>        

        <!-- Password -->
        <div class="mt-4">
            <x-admin::input-label for="password" :value="__('Password')" />

            <x-admin::text-input id="password" class="block mt-1 w-full"                            
                type="password"
                name="password"
                required autocomplete="current-password" />

            <x-admin::input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-4">
            <div class="flex items-center space-x-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('admin.register') }}">
                    {{ __('管理者登録') }}
                </a>
                @if (Route::has('admin.password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('admin.password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <x-admin::primary-button class="ms-3">
                {{ __('Log in') }}
            </x-admin::primary-button>        
        </div>
    </form>
</x-guest-layout>
