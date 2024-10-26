<!-- resources/views/admin/reports.blade.php -->
@extends('layouts.admin')

@section('content')
    <div class="container mx-auto mt-8">
        <h1 class="text-2xl font-bold mb-4">Sales and Statistics Reports</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h3 class="text-lg font-semibold mb-2">Sales by Package</h3>
                <canvas id="salesChart"></canvas>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-2">Usage Statistics</h3>
                <canvas id="usageChart"></canvas>
            </div>
        </div>
        <table class="min-w-full bg-white mt-4">
            <thead>
                <tr>
                    <th class="py-2">Package</th>
                    <th class="py-2">Location</th>
                    <th class="py-2">Sales</th>
                    <th class="py-2">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($salesData as $data)
                    <tr class="text-gray-700">
                        <td class="py-2">{{ $data->package_name }}</td>
                        <td class="py-2">{{ $data->location_name }}</td>
                        <td class="py-2">{{ $data->sales_count }}</td>
                        <td class="py-2">${{ $data->revenue }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
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
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endpush
