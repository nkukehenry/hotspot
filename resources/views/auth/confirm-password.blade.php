<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <label for="password" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Password') }}</label>
            <input id="password" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="password" name="password" required autocomplete="current-password">
            @error('password')
                <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
            @enderror
        </div>

        <div class="flex justify-end mt-4">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                {{ __('Confirm') }}
            </button>
        </div>
    </form>
</x-guest-layout>
