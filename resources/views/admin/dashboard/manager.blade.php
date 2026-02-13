@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 border-b border-gray-100 dark:border-gray-700 pb-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ Auth::user()->site->name ?? 'Site' }} Management</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Analysis: {{ $dateFrom }} to {{ $dateTo }}</p>
        </div>
        
        <!-- Date Filters -->
        <form action="{{ route('admin.dashboard') }}" method="GET" class="flex flex-wrap items-center gap-2 bg-white dark:bg-gray-800 p-1.5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-2 px-2 border-r border-gray-100 dark:border-gray-700">
                <i class="fas fa-calendar-alt text-blue-500 text-xs"></i>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="bg-transparent border-none text-[10px] font-bold focus:ring-0 p-1">
                <span class="text-gray-300 font-bold text-[10px]">TO</span>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="bg-transparent border-none text-[10px] font-bold focus:ring-0 p-1">
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black text-[9px] uppercase px-3 py-1.5 rounded-lg transition shadow-sm active:scale-95">
                Apply Filter
            </button>
        </form>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 p-4 rounded-xl shadow-lg border-none">
            <p class="text-[9px] font-black text-blue-100 uppercase tracking-widest mb-1">Current Balance</p>
            <p class="text-2xl font-black text-white">{{ number_format($stats['site_revenue']) }} <span class="text-xs font-normal opacity-70">UGX</span></p>
            <div class="mt-3 pt-3 border-t border-white/10 flex items-center justify-between text-[10px] font-bold text-white/80">
                <span>Total Accumulated</span>
                <i class="fas fa-wallet bg-white/20 p-1.5 rounded-lg"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Digital Sales</p>
            <p class="text-xl font-black text-blue-600">{{ number_format($stats['digital_revenue']) }}</p>
            <div class="mt-3 flex flex-col gap-0.5 text-[9px] font-bold text-gray-500 box-border">
                <div class="flex justify-between w-full">
                    <span>Fees:</span>
                    <span class="text-gray-700 dark:text-gray-300">{{ number_format($stats['digital_site_fee']) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Cash Sales</p>
            <p class="text-xl font-black text-green-600">{{ number_format($stats['cash_revenue']) }}</p>
            <div class="mt-3 flex flex-col gap-0.5 text-[9px] font-bold text-gray-500 box-border">
                <div class="flex justify-between w-full">
                    <span>Fees:</span>
                    <span class="text-gray-700 dark:text-gray-300">{{ number_format($stats['cash_site_fee']) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Vouchers Available</p>
            <p class="text-2xl font-black text-gray-900 dark:text-white">{{ number_format($stats['available_vouchers']) }}</p>
            <div class="mt-3 flex items-center gap-2">
                <div class="flex-1 h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-orange-500 rounded-full" style="width: 45%"></div>
                </div>
                <span class="text-[9px] font-black text-gray-500">45% STOCK</span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Field Agents</p>
            <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $stats['total_agents'] }}</p>
            <div class="mt-3 flex items-center text-[10px] font-bold text-blue-500">
                <i class="fas fa-users mr-1"></i> Active
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Agent Leaderboard -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">Agent Performance</h3>
                <span class="text-[9px] font-black text-blue-600 bg-blue-50 dark:bg-blue-900/30 px-2 py-0.5 rounded">BY SALES</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/50">
                            <th class="px-4 py-2 text-[9px] font-black text-gray-400 uppercase">Agent</th>
                            <th class="px-4 py-2 text-[9px] font-black text-gray-400 uppercase text-center">Sales</th>
                            <th class="px-4 py-2 text-[9px] font-black text-gray-400 uppercase text-right">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700 text-xs">
                        @foreach($agentPerformance as $agent)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-2 flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center font-bold text-blue-600 text-[10px]">
                                    {{ substr($agent->name, 0, 1) }}
                                </div>
                                <span class="font-bold text-gray-900 dark:text-white">{{ $agent->name }}</span>
                            </td>
                            <td class="px-4 py-2 font-bold text-center text-gray-600 dark:text-gray-400">{{ $agent->sales_count }}</td>
                            <td class="px-4 py-2 font-black text-gray-900 dark:text-white text-right">{{ number_format($agent->revenue) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Package Distribution -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col">
            <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight mb-4">Popular Packages</h3>
            <div class="flex-1 flex items-center justify-center aspect-square">
                <canvas id="packageSalesChart"></canvas>
            </div>
            <div class="mt-4 space-y-2">
                @foreach($packagePerformance->take(3) as $pkg)
                <div class="flex items-center justify-between text-[10px]">
                    <span class="text-gray-500 font-bold uppercase">{{ $pkg->name }}</span>
                    <span class="text-gray-900 dark:text-white font-black">{{ $pkg->sales_count }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('packageSalesChart'), {
        type: 'doughnut',
        data: {
            labels: @json($packagePerformance->pluck('name')),
            datasets: [{
                data: @json($packagePerformance->pluck('sales_count')),
                backgroundColor: ['#3b82f6', '#8b5cf6', '#f59e0b', '#10b981'],
                borderWidth: 0,
                cutout: '70%'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } }
        }
    });
</script>
@endsection
