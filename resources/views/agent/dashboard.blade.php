@extends('layouts.admin')

@section('content')
<div class="max-w-md mx-auto space-y-6 pb-20">
    <!-- Agent Welcome -->
    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-6 text-white shadow-lg">
        <h2 class="text-lg font-medium opacity-80">Hello, {{ Auth::user()->name }}</h2>
        <p class="text-2xl font-bold mt-1">Ready to sell?</p>
        
        <div class="grid grid-cols-2 gap-4 mt-6">
            <div class="bg-white/10 rounded-xl p-3 backdrop-blur-sm">
                <p class="text-[10px] uppercase font-bold tracking-wider opacity-70">Today's Sales</p>
                <p class="text-xl font-bold">{{ number_format($dailySales) }}</p>
            </div>
            <div class="bg-white/10 rounded-xl p-3 backdrop-blur-sm">
                <p class="text-[10px] uppercase font-bold tracking-wider opacity-70">Vouchers Sold</p>
                <p class="text-xl font-bold">{{ $dailyCount }}</p>
            </div>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm animate-bounce" role="alert">
        <p class="font-bold">Success!</p>
        <p>{{ session('success') }}</p>
        @if(session('voucher_code'))
        <div class="mt-2 p-3 bg-white rounded border border-green-200 text-center">
            <p class="text-xs uppercase text-gray-500 font-bold">Voucher Code</p>
            <p class="text-3xl font-black text-gray-900 tracking-widest font-mono">{{ session('voucher_code') }}</p>
        </div>
        @endif
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm" role="alert">
        <p>{{ session('error') }}</p>
    </div>
    @endif

    <!-- Package Selection -->
    <div>
        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Available Packages</h3>
        <div class="grid grid-cols-1 gap-3">
            @forelse($packages as $package)
            <button onclick="openSaleModal({{ $package->id }}, '{{ $package->name }}', {{ $package->cost }})" 
                    class="flex items-center justify-between bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 hover:border-blue-500 transition-all text-left">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 rounded-full flex items-center justify-center text-blue-600">
                        <i class="fas fa-wifi text-lg"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white">{{ $package->name }}</h4>
                        <p class="text-xs text-gray-500">{{ $package->description }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-lg font-black text-blue-600 dark:text-blue-400">{{ number_format($package->cost) }}</p>
                    <p class="text-[10px] text-gray-400 uppercase font-bold">UGX</p>
                </div>
            </button>
            @empty
            <div class="p-8 text-center bg-gray-50 dark:bg-gray-800 rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                <p class="text-gray-500 italic">No packages configured for this site.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Recent History -->
    <div>
        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-3 ml-1">Your Recent Sales</h3>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="divide-y dark:divide-gray-700">
                @forelse($recentSales as $sale)
                <div class="p-3 flex justify-between items-center">
                    <div>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $sale->package->name ?? 'Unknown' }}</p>
                        <p class="text-[10px] text-gray-500">{{ $sale->created_at->format('H:i') }} • {{ $sale->mobile_number }}</p>
                    </div>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($sale->amount) }}</p>
                </div>
                @empty
                <div class="p-4 text-center text-gray-500 text-sm">No sales yet today.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Sale Confirmation Modal -->
<div id="saleModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 w-full max-w-sm rounded-2xl overflow-hidden shadow-2xl transform transition-all">
        <div class="p-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Confirm Sale</h3>
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-xl mb-6">
                <div class="flex justify-between items-center mb-1">
                    <span class="text-sm text-gray-500">Package:</span>
                    <span id="modalPackageName" class="font-bold"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Amount:</span>
                    <span id="modalPackageCost" class="text-lg font-black text-blue-600"></span>
                </div>
            </div>

            <form action="{{ route('agent.sell.store') }}" method="POST">
                @csrf
                <input type="hidden" name="package_id" id="modalPackageId">
                
                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Customer Phone (Optional)</label>
                    <input type="text" name="mobile_number" placeholder="e.g. 256700..." 
                           class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-xl p-4 text-lg font-bold focus:ring-2 focus:ring-blue-500 transition-all">
                </div>

                <div class="flex flex-col space-y-3">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg transition-transform active:scale-95">
                        <i class="fas fa-check-circle mr-2"></i>Confirm Cash Payment
                    </button>
                    <button type="button" onclick="closeSaleModal()" class="w-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-bold py-3 rounded-xl">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openSaleModal(id, name, cost) {
        document.getElementById('modalPackageId').value = id;
        document.getElementById('modalPackageName').innerText = name;
        document.getElementById('modalPackageCost').innerText = new Intl.NumberFormat().format(cost) + ' UGX';
        document.getElementById('saleModal').classList.remove('hidden');
    }

    function closeSaleModal() {
        document.getElementById('saleModal').classList.add('hidden');
    }
</script>
@endsection
