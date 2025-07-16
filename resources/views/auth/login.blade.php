<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-4 text-sm text-gray-600 text-center">
        <p class="font-medium text-base text-indigo-600">TaskCampus</p>
        <p>Learning Management System</p>
    </div>

    <form method="POST" action="{{ route('login') }}" autocomplete="on">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="email" />
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
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ml-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
    
    <!-- Registration Info -->
    <div class="mt-6 pt-5 border-t border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-medium text-gray-700">Don't have an account?</h3>
            <a href="{{ route('register') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Register</a>
        </div>
        
        <!-- Demo Accounts Info -->
        <div class="mt-4 p-4 bg-blue-50 rounded-md text-sm">
            <h4 class="font-medium text-blue-800 mb-1">Demo Accounts</h4>
            <ul class="space-y-1 text-gray-600">
                <li><span class="font-medium">Admin:</span> admin@taskcampus.com | password</li>
                <li><span class="font-medium">Lecturer:</span> lecturer@taskcampus.com | password</li>
                <li><span class="font-medium">Student:</span> student@taskcampus.com | password</li>
            </ul>
        </div>
    </div>
</x-guest-layout>