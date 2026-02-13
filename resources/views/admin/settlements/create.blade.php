@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 mt-4 max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('admin.settlements.index') }}" class="text-xs font-bold text-gray-500 hover:text-gray-900 uppercase tracking-widest transition">
            <i class="fas fa-arrow-left mr-1"></i> Back to Settlements
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
            <h1 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">
                Request Withdrawal
            </h1>
            <p class="text-sm text-gray-500 mt-1">Select a date range to withdraw available digital sales.</p>
        </div>

        <div class="p-6">
            <!-- Account Info -->
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg mb-6 flex items-start">
                <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-3"></i>
                <div class="text-sm text-blue-800 dark:text-blue-200">
                    <p class="font-bold">Withdrawal Details:</p>
                    <p>Site: <span class="font-mono">{{ $site->name }}</span></p>
                    <p>Number: <span class="font-mono">{{ $site->settlement_momo_number ?? 'Not Configured' }}</span></p>
                    <p>Account: <span class="font-mono">{{ $site->settlement_account_name ?? 'Not Configured' }}</span></p>
                </div>
            </div>

            <!-- Amount Input Form -->
            <form method="POST" action="{{ route('admin.settlements.store') }}">
                @csrf
                <input type="hidden" name="site_id" value="{{ $site->id }}">

                <div class="bg-gray-50 dark:bg-gray-700/50 p-6 rounded-lg mb-6 text-center">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Available Balance</p>
                    <div class="text-4xl font-black text-gray-900 dark:text-white mb-1">
                        UGX {{ number_format($availableBalance) }}
                    </div>
                    @if(Auth::user()->hasRole('Owner'))
                        <div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-600">
                            <p class="text-[10px] font-bold text-orange-500 uppercase tracking-widest">
                                Total Site Fees (Deducted): UGX {{ number_format($pendingFees ?? 0) }}
                            </p>
                        </div>
                    @endif
                </div>

                <div class="mb-6">
                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Pass Cashout Amount</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">UGX</span>
                        </div>
                        <input type="number" name="amount" min="1000" max="{{ $availableBalance }}" step="100" required
                            class="bg-gray-100 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-lg rounded-lg block w-full pl-12 p-4" 
                            placeholder="Enter amount to withdraw">
                    </div>
                    <p class="text-[10px] text-gray-500 mt-1">Maximum available: UGX {{ number_format($availableBalance) }}</p>
                </div>

                <div class="mb-6">
                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Notes (Optional)</label>
                    <textarea name="notes" rows="3" class="bg-gray-100 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-sm rounded-lg block w-full p-2.5" placeholder="Any additional details..."></textarea>
                </div>

                <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold uppercase tracking-widest py-3 rounded-lg transition shadow-lg {{ $availableBalance <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                    {{ $availableBalance <= 0 ? 'disabled' : '' }}>
                    Submit Withdrawal Request
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
