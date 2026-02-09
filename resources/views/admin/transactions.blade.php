@extends('layouts.admin')

@section('content')
    <div class="container mx-auto mt-8">
        <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white"><i class="fas fa-exchange-alt mr-2 text-blue-500"></i>Transactions</h1>

        <!-- Filter Form -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow mb-6">
            <form action="{{ route('admin.transactions') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                
                @if(Auth::user()->hasRole('Owner'))
                <div>
                    <label for="site_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Site</label>
                    <select id="site_id" name="site_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="">All Sites</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div>
                    <label for="package_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Package</label>
                    <select id="package_id" name="package_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="">All Packages</option>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}" {{ request('package_id') == $package->id ? 'selected' : '' }}>{{ $package->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="mobile_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mobile Number</label>
                    <input type="text" id="mobile_number" name="mobile_number" value="{{ request('mobile_number') }}" placeholder="07..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                </div>

                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date From</label>
                    <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                </div>

                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date To</label>
                    <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                </div>
            </form>
             <div class="mt-4 flex justify-end">
                <button type="submit" form="filter-form" onclick="document.querySelector('form').submit()" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Filter</button>
                <a href="{{ route('admin.transactions') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Clear</a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white dark:bg-gray-800">
                <thead>
                    <tr class="bg-gray-200 dark:bg-gray-700 text-sm uppercase leading-normal">
                         <th class="py-2 px-3 text-left text-gray-800 dark:text-gray-200">
                            Date
                        </th>
                        <th class="py-2 px-3 text-left text-gray-800 dark:text-gray-200">
                            Site
                        </th>
                        <th class="py-2 px-3 text-left text-gray-800 dark:text-gray-200">
                            Mobile Number
                        </th>
                         <th class="py-2 px-3 text-left text-gray-800 dark:text-gray-200">
                            Voucher Code
                        </th>
                         <th class="py-2 px-3 text-left text-gray-800 dark:text-gray-200">
                            Package
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
                                    {{ $transaction->site->name ?? 'N/A' }}
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
                                <p class="text-gray-900 dark:text-white whitespace-no-wrap">
                                    {{ $transaction->voucher->package->name ?? 'N/A' }}
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
