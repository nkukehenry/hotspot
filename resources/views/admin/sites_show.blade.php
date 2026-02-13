@extends('layouts.admin')

@section('content')
    <div class="container mx-auto mt-4 px-4">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $site->name }}</h1>
            <a href="{{ route('admin.sites') }}" class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 font-bold py-1.5 px-3 rounded-lg text-xs hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
        </div>

        <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="siteTab" data-tabs-toggle="#siteTabContent" role="tablist">
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg" id="overview-tab" data-tabs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="false"><i class="fas fa-info-circle mr-2"></i>Overview</button>
                </li>
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="transactions-tab" data-tabs-target="#transactions" type="button" role="tab" aria-controls="transactions" aria-selected="false"><i class="fas fa-exchange-alt mr-2"></i>Transactions</button>
                </li>
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="packages-tab" data-tabs-target="#packages" type="button" role="tab" aria-controls="packages" aria-selected="false"><i class="fas fa-box mr-2"></i>Packages</button>
                </li>
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="inventory-tab" data-tabs-target="#inventory" type="button" role="tab" aria-controls="inventory" aria-selected="false"><i class="fas fa-ticket-alt mr-2"></i>Voucher Inventory</button>
                </li>
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="users-tab" data-tabs-target="#users" type="button" role="tab" aria-controls="users" aria-selected="false"><i class="fas fa-users mr-2"></i>Users</button>
                </li>
                <li role="presentation">
                    <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="ledger-tab" data-tabs-target="#ledger" type="button" role="tab" aria-controls="ledger" aria-selected="false"><i class="fas fa-file-invoice-dollar mr-2"></i>Settlements & Ledger</button>
                </li>
                <li role="presentation">
                    <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="fees-tab" data-tabs-target="#fees" type="button" role="tab" aria-controls="fees" aria-selected="false"><i class="fas fa-percent mr-2"></i>Fees</button>
                </li>
            </ul>
        </div>

        <div id="siteTabContent">
            <!-- Overview Tab -->
            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                <!-- Site Info Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
                    <div class="bg-white dark:bg-gray-700 p-3 rounded-lg shadow-sm">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Total Revenue</h3>
                        <p class="text-xl font-mono font-bold text-green-600 dark:text-green-400">
                            {{ number_format(($site->cash_sales_balance ?? 0) + ($site->digital_sales_balance ?? 0), 2) }}
                        </p>
                    </div>
                    <div class="bg-white dark:bg-gray-700 p-3 rounded-lg shadow-sm">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Cash Balance</h3>
                        <p class="text-xl font-mono font-bold text-green-600 dark:text-green-400">
                            {{ number_format($site->cash_sales_balance ?? 0, 2) }}
                        </p>
                    </div>
                    <div class="bg-white dark:bg-gray-700 p-3 rounded-lg shadow-sm">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Digital Balance</h3>
                        <p class="text-xl font-mono font-bold text-blue-600 dark:text-blue-400">
                            {{ number_format($site->digital_sales_balance ?? 0, 2) }}
                        </p>
                    </div>
                    <div class="bg-white dark:bg-gray-700 p-3 rounded-lg shadow-sm">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Site Code</h3>
                        <p class="text-xl font-mono font-bold text-gray-900 dark:text-white">{{ $site->site_code }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow mb-8">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Public Access</h3>
                    <div class="flex flex-col md:flex-row gap-6 items-start">
                        <div class="flex-1 w-full">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Customer Package Link</label>
                            <div class="flex items-center gap-2">
                                <input type="text" value="{{ route('customer.packages', $site->slug ?? $site->site_code) }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                    readonly>
                                <button onclick="copyToClipboard('{{ route('customer.packages', $site->slug ?? $site->site_code) }}')"
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                    Copy
                                </button>
                            </div>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Share this link with customers to purchase packages.</p>
                            <a href="{{ route('customer.packages', $site->slug ?? $site->site_code) }}" target="_blank" class="inline-flex items-center mt-2 text-blue-600 hover:underline dark:text-blue-500">
                                Customer Page <svg class="w-3 h-3 ms-2.5 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11v4.833A1.166 1.166 0 0 1 13.833 17H2.167A1.167 1.167 0 0 1 1 15.833V4.167A1.166 1.166 0 0 1 2.167 3h4.618m4.447-2H17v5.768M9.111 8.889l7.778-7.778"/></svg>
                            </a>
                        </div>
                        <div class="flex-shrink-0">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">QR Code</label>
                            <div class="p-2 bg-white rounded-lg border border-gray-200 dark:border-gray-600 inline-block">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode(route('customer.packages', $site->slug ?? $site->site_code)) }}&size=150x150" alt="Site QR Code" class="w-32 h-32">
                            </div>
                            <div class="mt-2 text-center">
                                <a href="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode(route('customer.packages', $site->slug ?? $site->site_code)) }}&size=300x300"
                                    download="qrcode-{{ $site->site_code }}.png"
                                    target="_blank"
                                    class="text-xs text-blue-600 hover:underline dark:text-blue-500">
                                    Download
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-700 p-3 rounded-lg shadow-sm mb-6">
                     <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase mb-4 border-b pb-2 dark:border-gray-600">Company Details</h3>
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                         <div>
                             <p class="text-gray-500 uppercase text-[9px] font-black">Address</p>
                             <p class="text-gray-900 dark:text-white font-medium">{{ $site->address }}</p>
                         </div>
                         <div>
                             <p class="text-gray-500 uppercase text-[9px] font-black">Contact Email</p>
                             <p class="text-gray-900 dark:text-white font-medium">{{ $site->contact_email ?? 'N/A' }}</p>
                         </div>
                         <div>
                             <p class="text-gray-500 uppercase text-[9px] font-black">Contact Phone</p>
                             <p class="text-gray-900 dark:text-white font-medium">{{ $site->contact_phone ?? 'N/A' }}</p>
                         </div>
                         <div>
                             <p class="text-gray-500 uppercase text-[9px] font-black">Status</p>
                             <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                 {{ $site->status ?? 'Active' }}
                             </span>
                         </div>
                     </div>
                </div>
            </div>

            <!-- Transactions Tab -->
            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="transactions" role="tabpanel" aria-labelledby="transactions-tab">
                <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Recent Transactions</h2>
                <div class="bg-white dark:bg-gray-700 shadow rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white dark:bg-gray-800">
                        <thead>
                            <tr class="bg-gray-200 dark:bg-gray-700 text-sm uppercase leading-normal">
                                <th class="py-2 px-3 text-left text-gray-800 dark:text-gray-200">
                                    Date
                                </th>
                                <th class="py-2 px-3 text-left text-gray-800 dark:text-gray-200">
                                    Mobile Number
                                </th>
                                 <th class="py-2 px-3 text-left text-gray-800 dark:text-gray-200">
                                    Voucher Code
                                </th>
                                <th class="py-2 px-3 text-left text-gray-800 dark:text-gray-200">
                                    Amount
                                </th>
                                <th class="py-2 px-3 text-left text-gray-800 dark:text-gray-200">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 dark:text-gray-300 text-sm font-light">
                            @forelse ($transactions as $transaction)
                                <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 even:bg-gray-50 dark:even:bg-gray-800 transition-colors duration-200">
                                    <td class="py-1 px-3 text-left whitespace-nowrap">
                                        <p class="text-gray-900 dark:text-white whitespace-no-wrap">
                                            {{ $transaction->created_at->format('M d, Y H:i') }}
                                        </p>
                                    </td>
                                    <td class="py-1 px-3 text-left whitespace-nowrap">
                                        <p class="text-gray-900 dark:text-white whitespace-no-wrap">
                                            {{ $transaction->mobile_number }}
                                        </p>
                                    </td>
                                     <td class="py-1 px-3 text-left whitespace-nowrap">
                                        <p class="text-gray-900 dark:text-white whitespace-no-wrap font-mono">
                                            {{ isset($transaction->voucher->code) ? Str::mask($transaction->voucher->code, '*', 0, -4) : 'N/A' }}
                                        </p>
                                    </td>
                                    <td class="py-1 px-3 text-left whitespace-nowrap">
                                        <p class="text-gray-900 dark:text-white whitespace-no-wrap font-bold">
                                            {{ number_format($transaction->amount, 2) }}
                                        </p>
                                    </td>
                                    <td class="py-1 px-3 text-left whitespace-nowrap">
                                        <span class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                            <span aria-hidden class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                            <span class="relative">{{ $transaction->status ?? 'Completed' }}</span>
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-center text-gray-500">
                                        No transactions found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        </table>
                    </div>
                    <div class="px-5 py-5 bg-white dark:bg-gray-800 border-t flex flex-col xs:flex-row items-center xs:justify-between">
                         <a href="{{ route('admin.transactions', ['site_id' => $site->id]) }}" class="text-blue-600 hover:underline dark:text-blue-500">View All Transactions</a>
                    </div>
                </div>
            </div>

            <!-- Inventory Tab -->
            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="inventory" role="tabpanel" aria-labelledby="inventory-tab">
                <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Voucher Inventory Summary</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($voucherInventory as $item)
                    <div class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ $item['package_name'] }}</h3>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-gray-600 dark:text-gray-400">Total:</span>
                            <span class="font-bold text-gray-900 dark:text-white">{{ $item['total'] }}</span>
                        </div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-gray-600 dark:text-gray-400">Used:</span>
                            <span class="font-bold text-red-600 dark:text-red-400">{{ $item['used'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Available:</span>
                            <span class="font-bold text-green-600 dark:text-green-400">{{ $item['available'] }}</span>
                        </div>
                         <!-- Progress Bar -->
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-600 mt-2">
                             @php
                                $percent = $item['total'] > 0 ? ($item['available'] / $item['total']) * 100 : 0;
                                $color = $percent < 20 ? 'bg-red-600' : ($percent < 50 ? 'bg-yellow-400' : 'bg-green-600');
                             @endphp
                            <div class="{{ $color }} h-2.5 rounded-full" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Packages Tab -->
             <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="packages" role="tabpanel" aria-labelledby="packages-tab">
                <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Packages</h2>
                <div class="bg-white dark:bg-gray-700 shadow rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Cost</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($packages as $package)
                            <tr>
                                <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white whitespace-nowrap">{{ $package->name }}</td>
                                <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white whitespace-nowrap">{{ number_format($package->cost) }}</td>
                                <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white">{{ $package->description }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-center text-gray-500">No packages found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Users Tab -->
            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="users" role="tabpanel" aria-labelledby="users-tab">
                <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Users</h2>
                <div class="bg-white dark:bg-gray-700 shadow rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                         <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Roles</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                            <tr>
                                <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white whitespace-nowrap">{{ $user->name }}</td>
                                <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white whitespace-nowrap">{{ $user->email }}</td>
                                <td class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white whitespace-nowrap">{{ $user->roles->pluck('name')->join(', ') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-center text-gray-500">No users found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Ledger Tab -->
            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="ledger" role="tabpanel" aria-labelledby="ledger-tab">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Balances & Stakeholders -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- Balance Card -->
                        <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow border-t-4 border-blue-500">
                            <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Available Digital Balance</h3>
                            <p class="text-3xl font-black text-blue-600 dark:text-blue-400 font-mono">
                                {{ number_format($siteAccount->balance ?? 0, 2) }}
                            </p>
                            <p class="text-xs text-gray-400 mt-2 italic">* This balance reflects mobile money sales minus site fees.</p>
                            
                            <button class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors text-sm shadow-md">
                                <i class="fas fa-hand-holding-usd mr-2"></i>Request Settlement
                            </button>
                        </div>

                        <!-- Stakeholders Card -->
                        <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow">
                            <h3 class="text-sm font-bold text-gray-800 dark:text-white uppercase mb-4 border-b pb-2 dark:border-gray-600">Stakeholders & Shares</h3>
                            <div class="space-y-4">
                                @forelse($stakeholders as $stakeholder)
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $stakeholder->name }}</p>
                                        <p class="text-[10px] text-gray-500 uppercase">{{ $stakeholder->account->name ?? 'No Account' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ number_format($stakeholder->share_percent, 1) }}%</p>
                                    </div>
                                </div>
                                @empty
                                <p class="text-sm text-gray-500 italic text-center py-4">No stakeholders configured.</p>
                                @endforelse
                            </div>
                        </div>

                        </div>
                    </div>

                    <!-- Ledger Table -->
                    <div class="lg:col-span-2">
                        <div class="bg-white dark:bg-gray-700 shadow rounded-lg overflow-hidden border border-gray-100 dark:border-gray-600">
                            <div class="px-4 py-3 border-b dark:border-gray-600 bg-gray-50 dark:bg-gray-800/50 flex justify-between items-center">
                                <h3 class="text-sm font-bold text-gray-800 dark:text-white">Recent Ledger Entries</h3>
                                <span class="text-[10px] bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 px-2 py-0.5 rounded-full uppercase font-bold">Live Ledger</span>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-xs">
                                    <thead>
                                        <tr class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 uppercase">
                                            <th class="py-2 px-3 text-left">Date</th>
                                            <th class="py-2 px-3 text-left">Description</th>
                                            <th class="py-2 px-3 text-right">Debit</th>
                                            <th class="py-2 px-3 text-right">Credit</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y dark:divide-gray-600">
                                        @forelse($ledgerEntries as $entry)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                            <td class="py-2 px-3 text-gray-500">{{ $entry->created_at->format('M d, H:i') }}</td>
                                            <td class="py-2 px-3 text-gray-900 dark:text-white font-medium">{{ $entry->description }}</td>
                                            <td class="py-2 px-3 text-right font-mono {{ $entry->debit > 0 ? 'text-red-500' : 'text-gray-400' }}">
                                                {{ $entry->debit > 0 ? number_format($entry->debit, 2) : '-' }}
                                            </td>
                                            <td class="py-2 px-3 text-right font-mono {{ $entry->credit > 0 ? 'text-green-500' : 'text-gray-400' }}">
                                                {{ $entry->credit > 0 ? number_format($entry->credit, 2) : '-' }}
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="py-10 text-center text-gray-500 italic">No ledger entries found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3 border-t dark:border-gray-600 text-center">
                                <a href="#" class="text-[10px] text-blue-600 hover:underline dark:text-blue-400 font-bold uppercase tracking-wider">Download Statement (PDF)</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fees Tab -->
            <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="fees" role="tabpanel" aria-labelledby="fees-tab">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow-sm">
                        <div class="flex items-center mb-3">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg mr-3">
                                <i class="fas fa-user-tag"></i>
                            </div>
                            <h4 class="text-xs font-black text-gray-900 dark:text-white uppercase">Customer Fee</h4>
                        </div>
                        <p class="text-[10px] text-gray-500 mb-4">Added to voucher price at checkout.</p>
                        <div class="flex justify-between items-end border-t dark:border-gray-600 pt-3">
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase">Fixed Amount</p>
                                <p class="text-xl font-black text-gray-900 dark:text-white">{{ number_format($site->customer_fee_fixed, 2) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[9px] font-black text-gray-400 uppercase">Percentage</p>
                                <p class="text-xl font-black text-blue-600">{{ $site->customer_fee_percent }}%</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow-sm">
                        <div class="flex items-center mb-3">
                            <div class="p-2 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-lg mr-3">
                                <i class="fas fa-store"></i>
                            </div>
                            <h4 class="text-xs font-black text-gray-900 dark:text-white uppercase">Site Fee</h4>
                        </div>
                        <p class="text-[10px] text-gray-500 mb-4">Deducted from site balance per sale.</p>
                        <div class="flex justify-between items-end border-t dark:border-gray-600 pt-3">
                            <div>
                                <p class="text-[9px] font-black text-gray-400 uppercase">Fixed Fee</p>
                                <p class="text-xl font-black text-gray-900 dark:text-white">{{ number_format($site->site_fee_fixed, 2) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[9px] font-black text-gray-400 uppercase">Percentage</p>
                                <p class="text-xl font-black text-green-600">{{ $site->site_fee_percent }}%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Link copied to clipboard!');
            }).catch(err => {
                console.error('Could not copy text: ', err);
            });
        }
    </script>
@endsection
