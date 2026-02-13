@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 mt-4">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
            <h1 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">
                <i class="fas fa-wallet mr-2 text-blue-500"></i> Collections Report
            </h1>
            
            <div class="flex gap-2">
                <button onclick="window.location.reload()" class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    <i class="fas fa-sync-alt mr-2"></i> Refresh
                </button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-6">
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="p-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 mr-3">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase text-gray-400 tracking-widest">Total Collections</p>
                        <h3 class="text-lg font-black text-gray-900 dark:text-white">UGX {{ number_format($totalRevenue) }}</h3>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="p-2 rounded-lg bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 mr-3">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase text-gray-400 tracking-widest">Digital Sales</p>
                        <h3 class="text-lg font-black text-gray-900 dark:text-white">UGX {{ number_format($digitalRevenue) }}</h3>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="p-2 rounded-lg bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 mr-3">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase text-gray-400 tracking-widest">Agent Sales</p>
                        <h3 class="text-lg font-black text-gray-900 dark:text-white">UGX {{ number_format($agentRevenue) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 mb-6">
            <form method="GET" action="{{ route('admin.collections') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                <div>
                    <label for="date_from" class="block text-[9px] font-black uppercase text-gray-400 mb-1">From</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                        class="bg-gray-100 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2">
                </div>
                <div>
                    <label for="date_to" class="block text-[9px] font-black uppercase text-gray-400 mb-1">To</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                        class="bg-gray-100 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2">
                </div>
                
                @if(!Auth::user()->site_id)
                <div>
                    <label for="site_id" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Site</label>
                    <select name="site_id" id="site_id"
                        class="bg-gray-100 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2">
                        <option value="">All Sites</option>
                        @foreach ($sites as $site)
                            <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>
                                {{ $site->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div>
                    <label for="type" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Type</label>
                    <select name="type" id="type"
                        class="bg-gray-100 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2">
                        <option value="">All Types</option>
                        <option value="digital" {{ request('type') == 'digital' ? 'selected' : '' }}>Digital Only</option>
                        <option value="agent" {{ request('type') == 'agent' ? 'selected' : '' }}>Agent Only</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('admin.collections') }}" class="flex-1 text-center bg-gray-100 dark:bg-gray-700 text-gray-500 text-[10px] font-black uppercase tracking-widest py-2 rounded-lg transition">
                        Clear
                    </a>
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-widest py-2 rounded-lg transition shadow-sm">
                        Apply
                    </button>
                </div>
            </form>
        </div>

        <!-- Data Table -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50 text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 border-b dark:border-gray-700">
                            <th class="py-2 px-3 text-left">Date</th>
                            <th class="py-2 px-3 text-left">Transaction ID</th>
                            @if(!Auth::user()->site_id)
                            <th class="py-2 px-3 text-left">Site</th>
                            @endif
                            <th class="py-2 px-3 text-center">Type</th>
                            <th class="py-2 px-3 text-left">Details</th>
                            <th class="py-2 px-3 text-right">C. Fee</th>
                            <th class="py-2 px-3 text-right">M. Fee</th>
                            <th class="py-2 px-3 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        @forelse ($transactions as $transaction)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="py-2 px-3 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                    {{ $transaction->created_at->format('d M Y H:i') }}
                                </td>
                                <td class="py-2 px-3 text-xs font-mono text-gray-600 dark:text-gray-300">
                                    {{ $transaction->transaction_id }}
                                </td>
                                @if(!Auth::user()->site_id)
                                <td class="py-2 px-3 text-xs text-gray-600 dark:text-gray-400">
                                    {{ $transaction->site->name ?? '-' }}
                                </td>
                                @endif
                                <td class="py-2 px-3 text-center">
                                    @if($transaction->agent_id)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300">
                                            AGENT
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                            DIGITAL
                                        </span>
                                    @endif
                                </td>
                                <td class="py-2 px-3 text-xs text-gray-600 dark:text-gray-400">
                                    <div class="font-bold text-gray-900 dark:text-white">{{ $transaction->package->name ?? 'Unknown Package' }}</div>
                                    <div class="text-[10px]">{{ $transaction->mobile_number }}</div>
                                    @if($transaction->agent_id)
                                        <div class="text-[9px] text-purple-600 dark:text-purple-400">Agent: {{ $transaction->agent->name ?? 'Unknown' }}</div>
                                    @endif
                                </td>
                                <td class="py-2 px-3 text-xs text-right text-red-500">
                                    {{ number_format($transaction->customer_fee) }}
                                </td>
                                <td class="py-2 px-3 text-xs text-right text-orange-500">
                                    {{ number_format($transaction->site_fee) }}
                                </td>
                                <td class="py-2 px-3 text-xs text-right font-black text-gray-900 dark:text-white">
                                    UGX {{ number_format($transaction->amount) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-500 dark:text-gray-400 italic">No collections found for the selected filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
@endsection
