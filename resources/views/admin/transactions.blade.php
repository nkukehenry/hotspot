@extends('layouts.admin')

@section('content')
    <div class="container mx-auto mt-4 px-4">
        <h1 class="text-xl font-black mb-4 text-gray-900 dark:text-white uppercase tracking-tight">
            <i class="fas fa-exchange-alt mr-2 text-blue-500"></i> Transactions
        </h1>

        <!-- Filter Form -->
        <div class="bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm mb-4 border border-gray-100 dark:border-gray-700">
            <form action="{{ route('admin.transactions') }}" method="GET" id="filter-form" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                
                @if(Auth::user()->hasRole('Owner'))
                <div>
                    <label for="site_id" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Site</label>
                    <select id="site_id" name="site_id" class="bg-gray-100 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2">
                        <option value="">All Sites</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div>
                    <label for="package_id" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Package</label>
                    <select id="package_id" name="package_id" class="bg-gray-100 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2">
                        <option value="">All Packages</option>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}" {{ request('package_id') == $package->id ? 'selected' : '' }}>{{ $package->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="mobile_number" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Mobile Number</label>
                    <input type="text" id="mobile_number" name="mobile_number" value="{{ request('mobile_number') }}" placeholder="07..." class="bg-gray-100 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2">
                </div>

                <div>
                    <label for="date_from" class="block text-[9px] font-black uppercase text-gray-400 mb-1">From</label>
                    <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="bg-gray-100 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2">
                </div>

                <div>
                    <label for="date_to" class="block text-[9px] font-black uppercase text-gray-400 mb-1">To</label>
                    <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="bg-gray-100 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2">
                </div>
            </form>
             <div class="mt-3 flex justify-end gap-2">
                <a href="{{ route('admin.transactions') }}" class="text-gray-500 hover:text-gray-700 text-[10px] font-black uppercase tracking-widest px-3 py-2">Clear</a>
                <button type="submit" form="filter-form" class="bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-widest px-4 py-2 rounded-lg shadow-sm transition">
                    Apply Filter
                </button>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 border-b dark:border-gray-700">
                         <th class="py-2 px-3 text-left">Date</th>
                         <th class="py-2 px-3 text-left">Site</th>
                         <th class="py-2 px-3 text-left">Mobile Number</th>
                         <th class="py-2 px-3 text-left">Voucher Code</th>
                         <th class="py-2 px-3 text-left">Package</th>
                         <th class="py-2 px-3 text-left">Amount</th>
                         <th class="py-2 px-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 dark:text-gray-300 text-sm font-light">
                    @forelse ($transactions as $transaction)
                        <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                             <td class="py-2 px-3 text-left whitespace-nowrap">
                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ $transaction->created_at->format('M d, H:i') }}
                                </p>
                            </td>
                            <td class="py-2 px-3 text-left whitespace-nowrap">
                                <p class="text-xs font-bold text-gray-900 dark:text-white">
                                    {{ $transaction->site->name ?? 'N/A' }}
                                </p>
                            </td>
                            <td class="py-2 px-3 text-left whitespace-nowrap">
                                <p class="text-xs text-gray-900 dark:text-white">
                                    {{ $transaction->mobile_number }}
                                </p>
                            </td>
                             <td class="py-2 px-3 text-left whitespace-nowrap">
                                <p class="text-xs font-mono text-blue-600 dark:text-blue-400">
                                    {{ isset($transaction->voucher->code) ? Str::mask($transaction->voucher->code, '*', 0, -4) : 'N/A' }}
                                </p>
                            </td>
                             <td class="py-2 px-3 text-left whitespace-nowrap">
                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ $transaction->voucher->package->name ?? 'N/A' }}
                                </p>
                            </td>
                            <td class="py-2 px-3 text-left whitespace-nowrap">
                                <p class="text-xs font-black text-gray-900 dark:text-white">
                                    {{ number_format($transaction->amount) }}
                                </p>
                            </td>
                            <td class="py-2 px-3 text-left whitespace-nowrap text-[10px]">
                                <span class="px-2 py-0.5 font-black uppercase rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                    {{ $transaction->status ?? 'Completed' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-center text-gray-500">
                                No transactions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                </table>
            </div>
            <div class="px-5 py-5 bg-white dark:bg-gray-800 border-t flex flex-col xs:flex-row items-center xs:justify-between">
                {{ $transactions->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection
