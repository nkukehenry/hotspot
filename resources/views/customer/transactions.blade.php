<!-- resources/views/customer/transactions.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="flex flex-col items-center justify-center min-h-screen px-5 py-6">
        <div class="w-full max-w-4xl bg-white/95 dark:bg-gray-800/95 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-xl overflow-hidden backdrop-blur-sm">
            
            <!-- Site Branding Header (Hydrated via JS/LocalStorage) -->
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 flex items-center justify-between">
                <div class="flex items-center space-x-3 text-left">
                    <img src="{{ asset('images/logo.png') }}" class="h-8 dynamic-site-logo hidden" alt="Site Logo">
                    <div>
                        <h5 class="text-sm font-black uppercase tracking-widest text-blue-600 dark:text-blue-400 dynamic-site-name">Your Transactions</h5>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">History & Status</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-black uppercase bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 px-2.5 py-1 rounded-md border border-blue-200 dark:border-blue-800">
                        {{ count($transactions) }} Records
                    </span>
                </div>
            </div>

            <div class="p-0 overflow-x-auto">
                @if ($transactions->isEmpty())
                    <div class="flex flex-col items-center justify-center py-16 px-4">
                        <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4 transition-transform hover:scale-110">
                            <i class="fas fa-history text-gray-300 dark:text-gray-500 text-2xl"></i>
                        </div>
                        <p class="text-xs font-black uppercase tracking-widest text-gray-400">No Transactions Found</p>
                    </div>
                @else
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th class="py-3 px-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Transaction</th>
                                <th class="py-3 px-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Package</th>
                                <th class="py-3 px-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Status</th>
                                <th class="py-3 px-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            @foreach ($transactions as $transaction)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                                    <td class="py-3 px-4">
                                        <div class="flex flex-col">
                                            <span class="text-[11px] font-black text-gray-800 dark:text-white uppercase tracking-tighter">
                                                {{ $transaction->transaction_id }}
                                            </span>
                                            <span class="text-[9px] font-bold text-blue-500 tracking-widest">
                                                {{ $transaction->voucher->code ?? 'N/A' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase">
                                            {{ $transaction->package->name ?? 'Package' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @php
                                            $statusClasses = [
                                                'completed' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 border-green-200 dark:border-green-800',
                                                'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 border-yellow-200 dark:border-yellow-800',
                                                'failed' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 border-red-200 dark:border-red-800'
                                            ];
                                            $statusClass = $statusClasses[$transaction->status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400 border-gray-200 dark:border-gray-600';
                                        @endphp
                                        <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded border {{ $statusClass }}">
                                            {{ $transaction->status }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <span class="text-[10px] font-bold text-gray-400 uppercase">
                                            {{ $transaction->created_at->format('M d, H:i') }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <div class="p-4 bg-gray-50/50 dark:bg-gray-900/50 text-center border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tight">Your recent connectivity history</p>
                <a href="{{ route('customer.sites') }}" class="text-[9px] font-black uppercase text-blue-600 hover:underline">Change Site</a>
            </div>
        </div>
    </div>
@endsection
