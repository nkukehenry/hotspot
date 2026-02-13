@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 mt-4">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
        <h1 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">
            <i class="fas fa-hand-holding-usd mr-2 text-blue-500"></i> Settlements & Balances
        </h1>

        <!-- Site Managers: Request Button -->
        @if(Auth::user()->site_id)
        <div class="flex gap-2">
            <button data-modal-target="withdraw-modal" data-modal-toggle="withdraw-modal" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-black uppercase tracking-widest transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                {{ $availableBalance <= 0 ? 'disabled' : '' }}>
                <i class="fas fa-plus mr-2"></i> Request Withdraw
            </button>
        </div>
        @endif
        
        <!-- Admins: Site Filter -->
         @if(!Auth::user()->site_id)
         <form method="GET" action="{{ route('admin.settlements.index') }}" class="flex gap-2">
            <select name="site_id" onchange="this.form.submit()" class="bg-gray-100 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg p-2">
                <option value="">Select Site to View Balance</option>
                @foreach($sites as $s)
                    <option value="{{ $s->id }}" {{ request('site_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
         </form>
         @endif
    </div>

    <!-- Balance Card (Only visible if a site is selected or user is site manager) -->
    @if(Auth::user()->site_id || request('site_id'))
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div>
                <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-1">Available Digital Balance</p>
                <div class="flex items-end gap-2">
                    <h2 class="text-3xl font-black text-gray-900 dark:text-white">UGX {{ number_format($availableBalance) }}</h2>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    Running total of unsettled digital sales for <span class="font-bold text-blue-500">{{ $site->name ?? Auth::user()->site->name }}</span>
                </p>
                @if(Auth::user()->hasRole('Owner'))
                    <p class="text-[10px] font-bold text-orange-500 uppercase tracking-widest mt-2">
                        Total Site Fees (Deducted): UGX {{ number_format($pendingFees ?? 0) }}
                    </p>
                @endif
            </div>
            @if(Auth::user()->site_id || ($site && !Auth::user()->site_id))
            <div class="mt-4 md:mt-0">
                <button data-modal-target="withdraw-modal" data-modal-toggle="withdraw-modal" 
                    class="inline-flex items-center justify-center px-5 py-3 text-sm font-medium text-center text-white bg-green-600 rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-300 dark:focus:ring-green-900 disabled:opacity-50 disabled:cursor-not-allowed"
                    {{ $availableBalance <= 0 ? 'disabled' : '' }}>
                    Request Withdraw
                </button>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Settlements Table -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700">
        <div class="p-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wide">Request History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 border-b dark:border-gray-700">
                        <th class="py-3 px-4 text-left">Date Requested</th>
                        <th class="py-3 px-4 text-left">Site</th>
                        @if(Auth::user()->hasRole('Owner'))
                        <th class="py-3 px-4 text-right">Total</th>
                        <th class="py-3 px-4 text-right">Fees</th>
                        <th class="py-3 px-4 text-right">Net Amount</th>
                        @else
                        <th class="py-3 px-4 text-right">Amount</th>
                        @endif
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @forelse ($settlements as $settlement)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="py-3 px-4 text-xs font-medium text-gray-900 dark:text-white">
                                {{ $settlement->created_at->format('d M Y') }}
                                <div class="text-[10px] text-gray-500">{{ $settlement->id }}</div>
                            </td>
                            <td class="py-3 px-4 text-xs text-gray-600 dark:text-gray-400">
                                {{ $settlement->site->name }}
                            </td>
                            @if(Auth::user()->hasRole('Owner'))
                            <td class="py-3 px-4 text-xs text-right font-medium text-gray-900 dark:text-white-400">
                                {{ number_format($settlement->amount + $settlement->transactions_sum_site_fee) }}
                            </td>
                            <td class="py-3 px-4 text-xs text-right font-medium text-orange-600 dark:text-orange-400">
                                {{ number_format($settlement->transactions_sum_site_fee) }}
                            </td>
                             <td class="py-3 px-4 text-xs text-right font-black text-blue-600 dark:text-blue-400">
                                UGX {{ number_format($settlement->amount) }}
                            </td>
                            @else
                            <td class="py-3 px-4 text-xs text-right font-black text-gray-900 dark:text-white">
                                UGX {{ number_format($settlement->amount) }}
                            </td>
                            @endif
                            <td class="py-3 px-4 text-center">
                                @if($settlement->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                        Approved
                                    </span>
                                @elseif($settlement->status === 'rejected')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                        Rejected
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                @if($settlement->status === 'pending' && !Auth::user()->site_id)
                                    <form action="{{ route('admin.settlements.approve', $settlement->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Approve disbursement of {{ number_format($settlement->amount) }} to {{ $settlement->site->name }}? This will debit their wallet.')">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900 text-xs font-bold uppercase transition">
                                            Approve
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500 dark:text-gray-400 italic">No settlement history found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
            {{ $settlements->links() }}
        </div>
    </div>
</div>

<!-- Withdraw Modal -->
@if(Auth::user()->site_id || ($site ?? false))
<div id="withdraw-modal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="withdraw-modal">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
                <span class="sr-only">Close modal</span>
            </button>
            <div class="px-6 py-6 lg:px-8">
                <h3 class="mb-4 text-xl font-medium text-gray-900 dark:text-white">Request Withdrawal</h3>
                <form class="space-y-6" action="{{ route('admin.settlements.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="site_id" value="{{ $site->id ?? Auth::user()->site_id }}">
                    
                    <div class="bg-gray-50 dark:bg-gray-600 p-4 rounded-lg text-center">
                         <p class="text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-widest mb-1">Available Balance</p>
                         <h2 class="text-2xl font-black text-gray-900 dark:text-white">UGX {{ number_format($availableBalance) }}</h2>
                         @if(Auth::user()->hasRole('Owner'))
                            <p class="text-[10px] font-bold text-orange-500 uppercase tracking-widest mt-1 border-t border-gray-200 dark:border-gray-500 pt-1">
                                Fees (Deducted): {{ number_format($pendingFees ?? 0) }}
                            </p>
                        @endif
                    </div>

                    <div>
                        <label for="amount" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Amount (UGX)</label>
                        <input type="number" name="amount" id="amount" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="Enter amount" min="1000" max="{{ $availableBalance }}" step="100" required>
                        <p class="text-[10px] text-gray-500 mt-1">Max: {{ number_format($availableBalance) }}</p>
                    </div>
                    
                    <div>
                        <label for="notes" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Notes (Optional)</label>
                        <textarea name="notes" id="notes" rows="3" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="Additional details..."></textarea>
                    </div>

                    <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        {{ $availableBalance <= 0 ? 'disabled' : '' }}>
                        Submit Request
                    </button>
                    
                    @if($availableBalance <= 0)
                    <p class="text-xs text-red-500 text-center">Insufficient balance to request withdrawal.</p>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@endsection
