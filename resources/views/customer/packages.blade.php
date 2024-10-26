<!-- resources/views/customer/packages.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container mx-auto mt-8 flex justify-center">
        <div
            class="w-full max-w-md p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white">Select Package</h5>
            </div>
            <div class="flow-root">
                <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($packages as $package)
                        <li class="py-3 sm:py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <img class="w-8 h-8 rounded-full" src="{{ $package->icon }}" alt="{{ $package->name }}">
                                </div>
                                <div class="flex-1 min-w-0 ms-4">
                                    <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                        {{ $package->name }}
                                    </p>
                                    <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                        {{ Str::limit($package->description, 60) }}
                                    </p>
                                </div>
                                <div
                                    class="inline-block items-center text-base font-semibold text-green-600 dark:text-white">
                                    UGX {{ number_format($package->cost, 0, '.', ',') }}
                                </div>
                                <a href="{{ route('customer.payment', $package->id) }}"
                                    class="ml-4 inline-block text-blue-600 border border-blue-600 rounded px-3 py-1 hover:bg-blue-600 hover:text-white dark:text-blue-500 dark:border-blue-500 dark:hover:bg-blue-500 dark:hover:text-white">
                                    Buy Now
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection
