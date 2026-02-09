<!-- resources/views/admin/dashboard.blade.php -->
@extends('layouts.admin')

@section('content')
    <div class="container mx-auto mt-8">
        <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white"><i class="fas fa-gauge mr-2 text-blue-500"></i>Dashboard</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">Sales by Package</h3>
                <canvas id="salesChart"></canvas>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">Sales by Site</h3>
                <canvas id="siteChart"></canvas>
            </div>
        </div>
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

        // Sales by Site Chart
        var ctxSite = document.getElementById('siteChart').getContext('2d');
        var siteChart = new Chart(ctxSite, {
            type: 'bar',
            data: {
                labels: @json($salesData->pluck('site_name')->unique()),
                datasets: [{
                    label: 'Sales by Site',
                    data: @json($salesData->groupBy('site_name')->map->sum('sales_count')),
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
                            text: 'Sites'
                        }
                    }
                }
            }
        });
    </script>
@endsection
