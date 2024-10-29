<!-- resources/views/admin/reports.blade.php -->
@extends('layouts.admin')

@section('content')
    <div class="container mx-auto mt-8">
        <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Sales and Statistics Reports</h1>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('admin.reports') }}" class="mb-4">
            <div class="flex space-x-4">
                <div class="w-1/3">
                    <label for="package_id" class="block text-gray-700 dark:text-gray-300 font-semibold mb-1">Package</label>
                    <select name="package_id" id="package_id"
                        class="block w-full mt-1 p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500">
                        <option value="">All Packages</option>
                        @foreach ($packages as $package)
                            <option value="{{ $package->id }}"
                                {{ request('package_id') == $package->id ? 'selected' : '' }}>
                                {{ $package->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-1/3">
                    <label for="location_id"
                        class="block text-gray-700 dark:text-gray-300 font-semibold mb-1">Location</label>
                    <select name="location_id" id="location_id"
                        class="block w-full mt-1 p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500">
                        <option value="">All Locations</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}"
                                {{ request('location_id') == $location->id ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end w-1/3">
                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Filter</button>
                </div>
            </div>
        </form>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">Sales by Package</h3>
                <canvas id="salesChart"></canvas>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">Sales by Location</h3>
                <canvas id="locationChart"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div class="px-2 py-2">
                <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">Revenue by Package</h3>
                <canvas id="packagePieChart"></canvas>
            </div>
            <div class="px-2 py-2">
                <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">Revenue by Location</h3>
                <canvas id="locationPieChart"></canvas>
            </div>
        </div>

        <table class="min-w-full bg-white mt-4 dark:bg-gray-800">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-700">
                    <th class="py-2 text-gray-900 dark:text-white">Package</th>
                    <th class="py-2 text-gray-900 dark:text-white">Location</th>
                    <th class="py-2 text-gray-900 dark:text-white">Unit Price</th>
                    <th class="py-2 text-gray-900 dark:text-white">Sales</th>
                    <th class="py-2 text-gray-900 dark:text-white">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($salesData as $data)
                    <tr class="text-gray-700 dark:text-gray-300">
                        <td class="py-2 px-2 text-center">{{ $data->package_name }}</td>
                        <td class="py-2 text-center">{{ $data->location_name }}</td>
                        <td class="py-2 text-center">{{ number_format($data->cost) }}</td>
                        <td class="py-2 text-center">{{ number_format($data->sales_count) }}</td>
                        <td class="py-2 text-center">UGX {{ number_format($data->revenue, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Sales by Package Chart
        var ctxSales = document.getElementById('salesChart').getContext('2d');
        var salesChart = new Chart(ctxSales, {
            type: 'bar',
            data: {
                labels: @json($salesData->pluck('package_name')),
                datasets: [{
                    label: 'Sales',
                    data: @json($salesData->pluck('sales_count')),
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Sales Count'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Packages'
                        }
                    }
                }
            }
        });

        // Sales by Location Chart
        var ctxLocation = document.getElementById('locationChart').getContext('2d');
        var locationChart = new Chart(ctxLocation, {
            type: 'bar',
            data: {
                labels: @json($salesData->pluck('location_name')->unique()),
                datasets: [{
                    label: 'Sales by Location',
                    data: @json($salesData->groupBy('location_name')->map->sum('sales_count')),
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Sales Count'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Locations'
                        }
                    }
                }
            }
        });

        // Revenue by Package Pie Chart
        var ctxPackagePie = document.getElementById('packagePieChart').getContext('2d');
        var packagePieChart = new Chart(ctxPackagePie, {
            type: 'pie',
            data: {
                labels: @json($packageRevenueData->pluck('package_name')),
                datasets: [{
                    label: 'Revenue by Package',
                    data: @json($packageRevenueData->pluck('total_revenue')),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
            }
        });

        // Revenue by Location Pie Chart
        var ctxLocationPie = document.getElementById('locationPieChart').getContext('2d');
        var locationPieChart = new Chart(ctxLocationPie, {
            type: 'pie',
            data: {
                labels: @json($locationRevenueData->pluck('location_name')),
                datasets: [{
                    label: 'Revenue by Location',
                    data: @json($locationRevenueData->pluck('total_revenue')),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
            }
        });
    </script>
@endsection
