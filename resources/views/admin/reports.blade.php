@extends('layouts.admin')

@section('content')
    <div class="container mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                <i class="fas fa-chart-line mr-2 text-blue-500"></i>Sales Overview & Analytics
            </h1>
            
            <!-- Export/Refresh Buttons (Optional future proofing) -->
            <div class="flex gap-2">
                <button onclick="window.location.reload()" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 px-3 py-1.5 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                </button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 mr-4">
                        <i class="fas fa-money-bill-wave fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Total Revenue</p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">UGX {{ number_format($summary['total_revenue']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-400 mr-4">
                        <i class="fas fa-shopping-cart fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Vouchers Sold</p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($summary['total_sales']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400 mr-4">
                        <i class="fas fa-star fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Top Package</p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['top_package'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-8">
            <form method="GET" action="{{ route('admin.reports') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label for="date_from" class="block text-xs font-semibold text-gray-500 uppercase mb-1">From Date</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                        class="block w-full text-sm p-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="date_to" class="block text-xs font-semibold text-gray-500 uppercase mb-1">To Date</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                        class="block w-full text-sm p-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                @if(!Auth::user()->site_id)
                <div>
                    <label for="site_id" class="block text-xs font-semibold text-gray-500 uppercase mb-1">Site</label>
                    <select name="site_id" id="site_id"
                        class="block w-full text-sm p-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500">
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
                    <label for="package_id" class="block text-xs font-semibold text-gray-500 uppercase mb-1">Package</label>
                    <select name="package_id" id="package_id"
                        class="block w-full text-sm p-2 border border-gray-300 rounded-lg dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Packages</option>
                        @foreach ($packages as $package)
                            <option value="{{ $package->id }}" 
                                data-site-id="{{ $package->site_id }}"
                                {{ request('package_id') == $package->id ? 'selected' : '' }}>
                                {{ $package->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg text-sm transition-colors">
                        Apply Filters
                    </button>
                    <a href="{{ route('admin.reports') }}" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-bold py-2 px-4 rounded-lg text-sm transition-colors">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Revenue Trend Chart -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 lg:col-span-2">
                <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Revenue Trend</h3>
                <div class="h-[300px]">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <!-- Sales by Package Bar Chart -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Sales Distribution</h3>
                <div class="h-[300px]">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <!-- Revenue by Site Pie Chart -->
            @if(!Auth::user()->site_id)
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Revenue by Site</h3>
                <div class="h-[300px] flex justify-center">
                    <canvas id="sitePieChart"></canvas>
                </div>
            </div>
            @else
            <!-- Revenue by Package Pie Chart for Site Managers -->
             <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Revenue by Package</h3>
                <div class="h-[300px] flex justify-center">
                    <canvas id="packagePieChart"></canvas>
                </div>
            </div>
            @endif
        </div>

        <!-- Data Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400 font-bold border-b border-gray-200 dark:border-gray-700">
                            <th class="py-3 px-4 text-left">Package</th>
                            @if(!Auth::user()->site_id)
                            <th class="py-3 px-4 text-left">Site</th>
                            @endif
                            <th class="py-3 px-4 text-center">Unit Price</th>
                            <th class="py-3 px-4 text-center">Sales Count</th>
                            <th class="py-3 px-4 text-right">Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        @forelse ($salesData as $data)
                            <tr class="text-sm hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="py-3 px-4 font-medium text-gray-900 dark:text-white">{{ $data->package_name }}</td>
                                @if(!Auth::user()->site_id)
                                <td class="py-3 px-4 text-gray-600 dark:text-gray-400">{{ $data->site_name }}</td>
                                @endif
                                <td class="py-3 px-4 text-center text-gray-600 dark:text-gray-400">{{ number_format($data->cost) }}</td>
                                <td class="py-3 px-4 text-center font-bold text-blue-600 dark:text-blue-400">{{ number_format($data->sales_count) }}</td>
                                <td class="py-3 px-4 text-right font-bold text-gray-900 dark:text-white">UGX {{ number_format($data->revenue) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-500 dark:text-gray-400 italic">No sales data found for the selected filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const siteSelect = document.getElementById('site_id');
            const packageSelect = document.getElementById('package_id');
            
            if (siteSelect && packageSelect) {
                const allPackageOptions = Array.from(packageSelect.options);

                function filterPackages() {
                    const selectedSiteId = siteSelect.value;
                    const selectedPackageId = packageSelect.value;

                    // Clear currently shown options
                    packageSelect.innerHTML = '';

                    // Add "All Packages" option back
                    packageSelect.appendChild(allPackageOptions[0]);

                    allPackageOptions.forEach((option, index) => {
                        if (index === 0) return; // Skip "All Packages"

                        const packageSiteId = option.getAttribute('data-site-id');
                        
                        if (!selectedSiteId || packageSiteId === selectedSiteId) {
                            packageSelect.appendChild(option);
                        }
                    });

                    // Restore selection if it's still valid
                    if (Array.from(packageSelect.options).some(opt => opt.value === selectedPackageId)) {
                        packageSelect.value = selectedPackageId;
                    } else {
                        packageSelect.value = '';
                    }
                }

                siteSelect.addEventListener('change', filterPackages);
                
                // Initial filter on load
                if (siteSelect.value) {
                    filterPackages();
                }
            }
        });

        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#4b5563' }
                }
            }
        };

        // Monthly Trend Chart (Line)
        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: @json($trendData->pluck('month')->map(fn($m) => Carbon\Carbon::parse($m)->format('M Y'))),
                datasets: [{
                    label: 'Monthly Revenue',
                    data: @json($trendData->pluck('monthly_revenue')),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                ...chartOptions,
                scales: {
                    y: { 
                        beginAtZero: true,
                        ticks: { color: '#9ca3af' },
                        grid: { color: 'rgba(156, 163, 175, 0.1)' }
                    },
                    x: {
                        ticks: { color: '#9ca3af' },
                        grid: { display: false }
                    }
                }
            }
        });

        // Sales Distribution (Bar)
        new Chart(document.getElementById('salesChart'), {
            type: 'bar',
            data: {
                labels: @json($salesData->pluck('package_name')),
                datasets: [{
                    label: 'Sales Count',
                    data: @json($salesData->pluck('sales_count')),
                    backgroundColor: '#3b82f6',
                    borderRadius: 6
                }]
            },
            options: {
                ...chartOptions,
                scales: {
                    y: { 
                        beginAtZero: true,
                        ticks: { color: '#9ca3af' },
                        grid: { color: 'rgba(156, 163, 175, 0.1)' }
                    },
                    x: {
                        ticks: { color: '#9ca3af' },
                        grid: { display: false }
                    }
                }
            }
        });

        // Pie Charts
        const pieColors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];

        @if(!Auth::user()->site_id)
        new Chart(document.getElementById('sitePieChart'), {
            type: 'doughnut',
            data: {
                labels: @json($siteRevenueData->pluck('site_name')),
                datasets: [{
                    data: @json($siteRevenueData->pluck('total_revenue')),
                    backgroundColor: pieColors,
                    borderWidth: 0
                }]
            },
            options: { ...chartOptions, cutout: '65%' }
        });
        @else
        new Chart(document.getElementById('packagePieChart'), {
            type: 'doughnut',
            data: {
                labels: @json($packageRevenueData->pluck('package_name')),
                datasets: [{
                    data: @json($packageRevenueData->pluck('total_revenue')),
                    backgroundColor: pieColors,
                    borderWidth: 0
                }]
            },
            options: { ...chartOptions, cutout: '65%' }
        });
        @endif
    </script>
@endsection
