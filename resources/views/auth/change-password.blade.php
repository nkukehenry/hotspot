@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900 px-4">
    <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
        <div class="mb-4 text-center">
            <h2 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Set New Password</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                For security reasons, you must change your initial or reset password before continuing.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-4 rounded-lg bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400 text-xs font-bold border border-red-100 dark:border-red-800">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('auth.change-password.update') }}">
            @csrf

            <!-- Current Password -->
            <div class="mb-4">
                <label for="current_password" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Current Password</label>
                <input id="current_password" type="password" name="current_password" required autofocus
                    class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-sm rounded-lg block w-full p-2.5 focus:ring-2 focus:ring-blue-500 shadow-sm" />
            </div>

            <!-- New Password -->
            <div class="mb-4">
                <label for="password" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">New Password</label>
                <input id="password" type="password" name="password" required
                    class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-sm rounded-lg block w-full p-2.5 focus:ring-2 focus:ring-blue-500 shadow-sm" />
            </div>

            <!-- Confirm New Password -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Confirm New Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-sm rounded-lg block w-full p-2.5 focus:ring-2 focus:ring-blue-500 shadow-sm" />
            </div>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-black uppercase tracking-widest py-3 rounded-lg transition shadow-md">
                Update Password & Login
            </button>
        </form>
    </div>
</div>
@endsection
