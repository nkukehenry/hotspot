@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Platform Overview</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Analysis from {{ $dateFrom }} to {{ $dateTo }}</p>
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
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Top Site Alert -->
        @php $topSite = $sitePerformance->sortByDesc('revenue')->first(); @endphp
        <div class="bg-gradient-to-br from-indigo-600 to-blue-700 p-4 rounded-xl shadow-md border-none lg:col-span-1">
            <p class="text-[9px] font-black text-indigo-100 uppercase tracking-widest mb-1">Top Site</p>
            <p class="text-lg font-black text-white truncate">{{ $topSite->name ?? 'N/A' }}</p>
            <div class="mt-3 pt-3 border-t border-white/10 flex items-center justify-between">
                <span class="text-[9px] font-bold text-white/80">{{ number_format($topSite->revenue ?? 0) }} UGX</span>
                <i class="fas fa-crown text-yellow-400 text-xs"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Revenue</p>
            <p class="text-xl font-black text-gray-900 dark:text-white">{{ number_format($stats['total_revenue']) }}</p>
            <div class="mt-3 flex items-center text-[9px] font-bold text-green-500">
                <i class="fas fa-chart-line mr-1"></i> +8.4%
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Active Sites</p>
            <p class="text-xl font-black text-gray-900 dark:text-white">{{ $stats['total_sites'] }}</p>
            <div class="mt-3 flex items-center text-[9px] font-bold text-blue-500">
                <i class="fas fa-check-circle mr-1"></i> Healthy
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Digital Sales</p>
            <p class="text-xl font-black text-blue-600">{{ number_format($stats['digital_revenue']) }}</p>
            <div class="mt-3 flex flex-col gap-0.5 text-[9px] font-bold text-gray-500 box-border">
                <div class="flex justify-between w-full">
                    <span>Customer Fee:</span>
                    <span class="text-gray-700 dark:text-gray-300">{{ number_format($stats['digital_customer_fee']) }}</span>
                </div>
                <div class="flex justify-between w-full">
                    <span>Site Fee:</span>
                    <span class="text-gray-700 dark:text-gray-300">{{ number_format($stats['digital_site_fee']) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Cash Sales</p>
            <p class="text-xl font-black text-green-600">{{ number_format($stats['cash_revenue']) }}</p>
            <div class="mt-3 flex flex-col gap-0.5 text-[9px] font-bold text-gray-500 box-border">
                 <div class="flex justify-between w-full">
                    <span>Customer Fee:</span>
                    <span class="text-gray-700 dark:text-gray-300">{{ number_format($stats['cash_customer_fee']) }}</span>
                </div>
                <div class="flex justify-between w-full">
                    <span>Site Fee:</span>
                    <span class="text-gray-700 dark:text-gray-300">{{ number_format($stats['cash_site_fee']) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase mb-4 tracking-tight">Revenue by Site</h3>
            <div class="aspect-[16/7]">
                <canvas id="siteRevenueChart"></canvas>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase mb-4 tracking-tight">Sales Distribution</h3>
            <div class="aspect-[16/7]">
                <canvas id="siteSalesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">Recent Platform Transactions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/50">
                        <th class="px-4 py-2 text-[9px] font-black text-gray-400 uppercase">TX ID</th>
                        <th class="px-4 py-2 text-[9px] font-black text-gray-400 uppercase">Site</th>
                        <th class="px-4 py-2 text-[9px] font-black text-gray-400 uppercase">Amount</th>
                        <th class="px-4 py-2 text-[9px] font-black text-gray-400 uppercase">Agent</th>
                        <th class="px-4 py-2 text-[9px] font-black text-gray-400 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-xs">
                    @foreach($stats['recent_transactions'] as $tx)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-4 py-2 font-mono font-bold text-gray-900 dark:text-white">{{ $tx->transaction_id }}</td>
                        <td class="px-4 py-2 font-bold text-blue-600">{{ $tx->site->name ?? 'SYSTEM' }}</td>
                        <td class="px-4 py-2 font-black text-gray-900 dark:text-white">{{ number_format($tx->amount) }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $tx->agent->name ?? 'API' }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                {{ $tx->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartConfig = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { grid: { display: false }, ticks: { font: { size: 10, weight: 'bold' } } },
            x: { grid: { display: false }, ticks: { font: { size: 10, weight: 'bold' } } }
        }
    };

    new Chart(document.getElementById('siteRevenueChart'), {
        type: 'line',
        data: {
            labels: @json($trendData->pluck('date')),
            datasets: [{
                label: 'Revenue Trend',
                data: @json($trendData->pluck('revenue')),
                borderColor: '#6366f1',
                borderWidth: 4,
                tension: 0.4,
                fill: true,
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                pointBackgroundColor: '#6366f1',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            ...chartConfig,
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: '#1f2937',
                    titleFont: { size: 12, weight: 'bold' },
                    bodyFont: { size: 12 },
                    padding: 12,
                    displayColors: false,
                    callbacks: {
                        label: (ctx) => 'Revenue: ' + new Intl.NumberFormat().format(ctx.parsed.y) + ' UGX'
                    }
                }
            }
        }
    });

    new Chart(document.getElementById('siteSalesChart'), {
        type: 'doughnut',
        data: {
            labels: @json($sitePerformance->pluck('name')),
            datasets: [{
                data: @json($sitePerformance->pluck('revenue')),
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444'],
                borderWidth: 0,
                hoverOffset: 20
            }]
        },
        options: {
            ...chartConfig,
            cutout: '70%',
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: { size: 10, weight: 'bold' }
                    }
                }
            }
        }
    });
</script>
@endsection
