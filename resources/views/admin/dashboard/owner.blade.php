@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Platform Overview</h1>
            <p class="text-gray-500 dark:text-gray-400">Analysis from {{ $dateFrom }} to {{ $dateTo }}</p>
        </div>
        
        <!-- Date Filters -->
        <form action="{{ route('admin.dashboard') }}" method="GET" class="flex flex-wrap items-center gap-2 bg-white dark:bg-gray-800 p-2 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-2 px-2 border-r border-gray-100 dark:border-gray-700">
                <i class="fas fa-calendar-alt text-blue-500"></i>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="bg-transparent border-none text-xs font-bold focus:ring-0">
                <span class="text-gray-300 font-bold">TO</span>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="bg-transparent border-none text-xs font-bold focus:ring-0">
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black text-[10px] uppercase px-4 py-2 rounded-lg transition shadow-sm active:scale-95">
                Apply Filter
            </button>
        </form>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <!-- Top Site Alert -->
        @php $topSite = $sitePerformance->sortByDesc('revenue')->first(); @endphp
        <div class="bg-gradient-to-br from-indigo-600 to-blue-700 p-6 rounded-2xl shadow-lg border-none lg:col-span-1">
            <p class="text-[10px] font-black text-indigo-100 uppercase tracking-widest mb-1">Top Performing Site</p>
            <p class="text-xl font-black text-white truncate">{{ $topSite->name ?? 'N/A' }}</p>
            <div class="mt-4 pt-4 border-t border-white/10 flex items-center justify-between">
                <span class="text-[10px] font-bold text-white/80">{{ number_format($topSite->revenue ?? 0) }} UGX</span>
                <i class="fas fa-crown text-yellow-400"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Revenue</p>
            <p class="text-2xl font-black text-gray-900 dark:text-white">{{ number_format($stats['total_revenue']) }}</p>
            <div class="mt-4 flex items-center text-[10px] font-bold text-green-500">
                <i class="fas fa-chart-line mr-1"></i> +8.4% growth
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Active Sites</p>
            <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $stats['total_sites'] }}</p>
            <div class="mt-4 flex items-center text-[10px] font-bold text-blue-500">
                <i class="fas fa-check-circle mr-1"></i> All systems online
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Voucher Stock</p>
            <p class="text-2xl font-black text-orange-600">{{ number_format($stats['active_vouchers']) }}</p>
            <div class="mt-4 flex items-center text-[10px] font-bold text-orange-500">
                <i class="fas fa-exclamation-triangle mr-1"></i> 3 sites low stock
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Platform ATV</p>
            <p class="text-2xl font-black text-purple-600">{{ number_format($stats['avg_transaction']) }}</p>
            <div class="mt-4 flex items-center text-[10px] font-bold text-purple-500">
                <i class="fas fa-wallet mr-1"></i> Per transaction
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase mb-6 tracking-tight">Revenue by Site</h3>
            <div class="aspect-[16/9]">
                <canvas id="siteRevenueChart"></canvas>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase mb-6 tracking-tight">Sales Distribution</h3>
            <div class="aspect-[16/9]">
                <canvas id="siteSalesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">Recent Platform Transactions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/50">
                        <th class="px-6 py-3 text-[10px] font-black text-gray-400 uppercase">Transaction ID</th>
                        <th class="px-6 py-3 text-[10px] font-black text-gray-400 uppercase">Site</th>
                        <th class="px-6 py-3 text-[10px] font-black text-gray-400 uppercase">Amount</th>
                        <th class="px-6 py-3 text-[10px] font-black text-gray-400 uppercase">Agent</th>
                        <th class="px-6 py-3 text-[10px] font-black text-gray-400 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($stats['recent_transactions'] as $tx)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 font-mono text-xs font-bold text-gray-900 dark:text-white">{{ $tx->transaction_id }}</td>
                        <td class="px-6 py-4 text-sm font-bold text-blue-600">{{ $tx->site->name ?? 'SYSTEM' }}</td>
                        <td class="px-6 py-4 text-sm font-black text-gray-900 dark:text-white">{{ number_format($tx->amount) }} UGX</td>
                        <td class="px-6 py-4 text-xs text-gray-500">{{ $tx->agent->name ?? 'API/Direct' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-full text-[10px] font-black uppercase bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
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
