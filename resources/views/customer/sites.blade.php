@extends('layouts.home')

@section('content')
    <div class="flex flex-col items-center justify-start md:justify-center min-h-screen px-5 pt-8 md:pt-0 pb-20">
        <div class="w-full max-w-md bg-white/95 dark:bg-gray-800/95 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-xl overflow-hidden backdrop-blur-sm">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50">
                <h5 class="text-xl font-black uppercase tracking-widest text-blue-600 dark:text-blue-400 text-center">Select Your Location</h5>
                <p class="text-[11px] font-bold text-gray-400 uppercase text-center mt-1">Connect to a nearby hotspot</p>
            </div>

            <div class="flow-root p-2">
                <ul role="list" class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($sites as $s)
                        <li class="py-3 px-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    @if($s->logo)
                                        <img class="w-10 h-10 rounded-full border border-gray-100 dark:border-gray-600 object-contain bg-white" src="{{ asset('storage/' . $s->logo) }}" alt="{{ $s->name }}">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center border border-blue-200 dark:border-blue-800">
                                            <i class="fas fa-wifi text-blue-600 dark:text-blue-400 text-[10px]"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0 ms-4 text-left">
                                    <p class="text-[13px] font-black text-gray-900 truncate dark:text-white uppercase tracking-tighter">
                                        {{ $s->name }}
                                    </p>
                                    <p class="text-[10px] font-bold text-gray-400 truncate uppercase mt-0.5">
                                        {{ $s->site_code }}
                                    </p>
                                </div>
                                <a href="{{ route('customer.packages', $s->slug ?? $s->site_code) }}"
                                    class="ml-4 inline-block text-[10px] font-black uppercase tracking-widest text-blue-600 border-2 border-blue-600 rounded-lg px-4 py-1.5 hover:bg-blue-600 hover:text-white transition-all duration-200 transform hover:scale-105 active:scale-95 whitespace-nowrap">
                                    Buy Voucher
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            
            <div class="p-4 bg-gray-50/50 dark:bg-gray-900/50 text-center border-t border-gray-50 dark:border-gray-700">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">Enjoy Cheap Internet With Us</p>
            </div>
        </div>
    </div>
@endsection
