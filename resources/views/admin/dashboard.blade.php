@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-center min-h-[60vh]">
    <div class="max-w-md w-full text-center space-y-6 p-8 bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700">
        <div class="w-20 h-20 bg-blue-50 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto text-blue-600 dark:text-blue-400">
            <i class="fas fa-user-shield text-4xl"></i>
        </div>
        
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight mb-2">Welcome, {{ Auth::user()->name }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                You are currently logged into the **{{ config('app.name') }}** administrative console.
            </p>
        </div>

        <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-2xl border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-center space-x-2 text-orange-500 mb-2">
                <i class="fas fa-exclamation-triangle"></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Configuration Notice</span>
            </div>
            <p class="text-xs text-gray-600 dark:text-gray-400">
                No personalized dashboard has been configured for your specific role yet. You can still navigate using the sidebar menu if you have the necessary permissions.
            </p>
        </div>

        <p class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">
            Please contact the platform owner if you believe this is an error.
        </p>
    </div>
</div>
@endsection

@section('scripts')
{{-- No scripts needed for fallback --}}
@endsection
