@extends('layouts.admin')

@section('content')
    <div class="container mx-auto mt-8">
        <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Manage Vouchers</h1>

        <!-- Add Vouchers Button -->
        <div class="mb-4">
            <button type="button" onclick="openUploadModal()"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-md">
                Add Vouchers
            </button>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('admin.vouchers') }}" class="mb-4">
            <div class="flex space-x-4">
                <div class="w-1/3">
                    <label for="location_id"
                        class="block text-gray-700 dark:text-gray-300 font-semibold mb-1">Location</label>
                    <select name="location_id" id="location_id" onchange="filterPackages()"
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
                <div class="w-1/3">
                    <label for="package_id"
                        class="block text-gray-700 dark:text-gray-300 font-semibold mb-1">Package</label>
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
                <div class="flex items-end w-1/3">
                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-md">
                        Filter
                    </button>
                </div>
            </div>
        </form>

        <!-- Bulk Actions -->
        <form method="POST" action="{{ route('admin.vouchers.bulkAction') }}" id="bulk-action-form">
            @csrf
            <div class="flex justify-between mb-4">
                <div>
                    <button type="button" onclick="deleteSelected()"
                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-4 rounded shadow-md">
                        Delete Selected
                    </button>
                    <button type="button" onclick="changeStatus('used')"
                        class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-4 rounded shadow-md">
                        Mark as Used
                    </button>
                    <button type="button" onclick="changeStatus('available')"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-4 rounded shadow-md">
                        Mark as Available
                    </button>
                </div>
            </div>

            <!-- Vouchers Table -->
            <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden dark:bg-gray-800">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700">
                        <th class="py-2 px-4"><input type="checkbox" id="select-all" onclick="toggleSelectAll()"></th>
                        <th class="py-2 px-4 text-gray-900 dark:text-white">Voucher Code</th>
                        <th class="py-2 px-4 text-gray-900 dark:text-white">Location</th>
                        <th class="py-2 px-4 text-gray-900 dark:text-white">Package</th>
                        <th class="py-2 px-4 text-gray-900 dark:text-white">Price</th>
                        <th class="py-2 px-4 text-gray-900 dark:text-white">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($vouchers as $voucher)
                        <tr class="text-gray-700 border-b dark:text-gray-300 dark:border-gray-600">
                            <td class="py-2 px-4 text-center">
                                <input type="checkbox" name="voucher_ids[]" value="{{ $voucher->id }}">
                            </td>
                            <td class="py-2 px-4 text-center">{{ $voucher->code }}</td>
                            <td class="py-2 px-4 text-center">{{ $voucher->location_name }}</td>
                            <td class="py-2 px-4 text-center">{{ $voucher->package_name }}</td>
                            <td class="py-2 px-4 text-center">{{ number_format($voucher->cost) }}</td>
                            <td class="py-2 px-4 text-center">
                                <span
                                    class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $voucher->is_used ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $voucher->is_used ? 'Used' : 'Available' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </form>

        <!-- Pagination Links -->
        <div class="mt-4">
            {{ $vouchers->links() }}
        </div>
    </div>

    <!-- Upload Vouchers Modal -->
    <div id="upload-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-1/4 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-700">
            <div class="mt-3">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Upload Vouchers</h3>
                <form action="{{ route('admin.uploadVouchers') }}" method="POST" enctype="multipart/form-data"
                    id="upload-form">
                    @csrf
                    <div class="mb-4">
                        <label for="upload_location_id"
                            class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Select
                            Location</label>
                        <select name="location_id" id="upload_location_id"
                            class="block w-full mt-1 p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500"
                            onchange="filterUploadPackages()">
                            <option value="">Select a location</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="package" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Select
                            Package</label>
                        <select name="package_id" id="package"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500"
                            required>
                            <option value="">Select a package</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="file" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Upload
                            Excel File</label>
                        <input type="file" id="file" name="file"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500"
                            required>
                    </div>
                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Upload</button>
                    <button type="button" onclick="closeUploadModal()"
                        class="mt-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Cancel</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openUploadModal() {
            document.getElementById('upload-modal').classList.remove('hidden');
        }

        function closeUploadModal() {
            document.getElementById('upload-modal').classList.add('hidden');
        }

        function filterPackages() {
            const locationId = document.getElementById('location_id').value;
            const packageSelect = document.getElementById('package_id');

            // Clear existing options
            packageSelect.innerHTML = '<option value="">All Packages</option>';

            if (locationId) {
                // Fetch packages based on selected location
                fetch(`{{ route('admin.locationPackages', '') }}/${locationId}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log(data)
                        data.forEach(package => {
                            const option = document.createElement('option');
                            option.value = package.id;
                            option.textContent = package.name;
                            packageSelect.appendChild(option);
                        });
                    });
            }
        }

        function filterUploadPackages() {
            const locationId = document.getElementById('upload_location_id').value;
            const packageSelect = document.getElementById('package');

            // Clear existing options
            packageSelect.innerHTML = '<option value="">Select a package</option>';

            if (locationId) {
                // Fetch packages based on selected location
                fetch(`{{ route('admin.locationPackages', '') }}/${locationId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(package => {
                            const option = document.createElement('option');
                            option.value = package.id;
                            option.textContent = package.name;
                            packageSelect.appendChild(option);
                        });
                    });
            }
        }

        function toggleSelectAll() {
            const selectAllCheckbox = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('input[name="voucher_ids[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        }

        function deleteSelected() {
            const checkboxes = document.querySelectorAll('input[name="voucher_ids[]"]:checked');
            if (checkboxes.length === 0) {
                alert('Please select at least one voucher to delete.');
                return;
            }
            if (confirm('Are you sure you want to delete the selected vouchers?')) {
                document.getElementById('bulk-action-form').submit();
            }
        }

        function changeStatus(status) {
            const checkboxes = document.querySelectorAll('input[name="voucher_ids[]"]:checked');
            if (checkboxes.length === 0) {
                alert('Please select at least one voucher to change status.');
                return;
            }
            const form = document.getElementById('bulk-action-form');
            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = status;
            form.appendChild(statusInput);
            form.submit();
        }
    </script>
@endsection
