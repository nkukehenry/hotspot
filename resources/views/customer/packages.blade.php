<!-- resources/views/customer/packages.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container mx-auto mt-8 flex flex-col items-center">

        <div
            class="w-full max-w-md p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white">Select Package</h5>

            </div>
            <div class="flow-root">
                <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($packages as $package)
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
                                <a href="{{ route('customer.payment', $package->code) }}"
                                    class="ml-4 inline-block text-blue-600 border border-blue-600 rounded px-3 py-1 hover:bg-blue-600 hover:text-white dark:text-blue-500 dark:border-blue-500 dark:hover:bg-blue-500 dark:hover:text-white whitespace-nowrap">
                                    Buy Now
                                </a>
                            </div>
                        </li>
                    @empty
                        <li class="py-12 flex flex-col items-center justify-center text-center">
                            <div class="w-16 h-16 bg-blue-50 dark:bg-blue-900/20 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-tools text-blue-600 dark:text-blue-400 text-xl"></i>
                            </div>
                            <h6 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest mb-1">
                                Updates in Progress
                            </h6>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">
                                Please try again in a few minutes
                            </p>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        @if(isset($site) && ($site->contact_email || $site->contact_phone))
            <div class="mt-4 w-full max-w-md bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 border border-gray-100 dark:border-gray-700 text-center">
                <p class="text-[10px] font-black uppercase text-gray-400 mb-2 tracking-widest">Customer Support</p>
                <div class="flex flex-wrap justify-center gap-x-4 gap-y-1">
                    @if($site->contact_email)
                        <a href="mailto:{{ $site->contact_email }}" class="text-[11px] font-bold text-blue-600 dark:text-blue-400 hover:underline flex items-center">
                            <i class="fas fa-envelope mr-1.5 opacity-70"></i> {{ $site->contact_email }}
                        </a>
                    @endif
                    @if($site->contact_phone)
                        <a href="tel:{{ $site->contact_phone }}" class="text-[11px] font-bold text-blue-600 dark:text-blue-400 hover:underline flex items-center">
                            <i class="fas fa-phone mr-1.5 opacity-70"></i> {{ $site->contact_phone }}
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection
