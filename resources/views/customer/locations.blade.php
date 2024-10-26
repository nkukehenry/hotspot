<!-- resources/views/customer/locations.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container mx-auto mt-8 flex justify-center">
        <div
            class="w-full max-w-md p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white">Choose Your Location</h5>
            </div>
            <div class="flow-root">
                <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($locations as $location)
                        <li class="py-3 sm:py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M10 2a8 8 0 0 0-8 8h2a6 6 0 0 1 12 0h2a8 8 0 0 0-8-8zm0 4a4 4 0 0 0-4 4h2a2 2 0 0 1 4 0h2a4 4 0 0 0-4-4zm0 4a2 2 0 0 0-2 2h2v2h2v-2h2a2 2 0 0 0-2-2z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0 ms-4">
                                    <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                        {{ $location->name }}
                                    </p>
                                </div>
                                <a href="{{ route('customer.packages', $location->id) }}"
                                    class="inline-block text-blue-600 border border-blue-600 rounded px-3 py-1 hover:bg-blue-600 hover:text-white dark:text-blue-500 dark:border-blue-500 dark:hover:bg-blue-500 dark:hover:text-white">
                                    Connect Now
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection
