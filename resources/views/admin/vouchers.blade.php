@extends('layouts.admin')

@section('content')
    <div class="container mx-auto mt-4 px-4">
        <h1 class="text-xl font-black mb-4 text-gray-900 dark:text-white uppercase tracking-tight">
            <i class="fas fa-ticket-alt mr-2 text-blue-500"></i> Manage Vouchers
        </h1>

        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                <div class="font-bold mb-1"><i class="fas fa-exclamation-triangle mr-2"></i> Please fix the following errors:</div>
                <ul class="mt-1.5 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Add Vouchers Button -->
        @can('create_vouchers')
        <div class="mb-4">
            <button type="button" onclick="openUploadModal()"
                class="bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-widest py-2 px-4 rounded-lg shadow-sm transition">
                <i class="fas fa-plus mr-2"></i> Add Vouchers
            </button>
        </div>
        @endcan

        <!-- Filter Form -->
        <div class="bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm mb-4 border border-gray-100 dark:border-gray-700">
            <form method="GET" action="{{ route('admin.vouchers') }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label for="site_id" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Site</label>
                        <select name="site_id" id="site_id" onchange="filterPackages()"
                            class="bg-gray-100 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2">
                            <option value="">All Sites</option>
                            @foreach ($sites as $site)
                                <option value="{{ $site->id }}"
                                    {{ request('site_id') == $site->id ? 'selected' : '' }}>
                                    {{ $site->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="package_id" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Package</label>
                        <select name="package_id" id="package_id"
                            class="bg-gray-100 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2">
                            <option value="">All Packages</option>
                            @foreach ($packages as $package)
                                <option value="{{ $package->id }}"
                                    {{ request('package_id') == $package->id ? 'selected' : '' }}>
                                    {{ $package->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="bg-gray-800 dark:bg-gray-700 text-white text-[10px] font-black uppercase tracking-widest px-4 py-2 rounded-lg transition">
                            <i class="fas fa-filter mr-2 text-blue-400"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Bulk Actions -->
        <form method="POST" action="{{ route('admin.vouchers.bulkAction') }}" id="bulk-action-form">
            @csrf
            <div class="flex justify-between items-center mb-3">
                <div class="flex gap-2">
                    @can('delete_vouchers')
                    <button type="button" onclick="deleteSelected()"
                        class="text-[9px] font-black uppercase text-red-600 border border-red-200 dark:border-red-900/50 bg-red-50 dark:bg-red-900/10 hover:bg-red-100 px-3 py-1 rounded transition">
                        Delete Selected
                    </button>
                    @endcan
                    @can('edit_vouchers')
                    <button type="button" onclick="changeStatus('used')"
                        class="text-[9px] font-black uppercase text-yellow-600 border border-yellow-200 dark:border-yellow-900/50 bg-yellow-50 dark:bg-yellow-900/10 hover:bg-yellow-100 px-3 py-1 rounded transition">
                        Mark as Used
                    </button>
                    <button type="button" onclick="changeStatus('available')"
                        class="text-[9px] font-black uppercase text-green-600 border border-green-200 dark:border-green-900/50 bg-green-50 dark:bg-green-900/10 hover:bg-green-100 px-3 py-1 rounded transition">
                        Mark as Available
                    </button>
                    @endcan
                </div>
            </div>

            <!-- Vouchers Table -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50 text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 border-b dark:border-gray-700">
                            <th class="py-2 px-3 w-10"><input type="checkbox" id="select-all" onclick="toggleSelectAll()" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"></th>
                            <th class="py-2 px-3 text-left">Voucher Code</th>
                            <th class="py-2 px-3 text-left">Site</th>
                            <th class="py-2 px-3 text-left">Package</th>
                            <th class="py-2 px-3 text-left">Price</th>
                            <th class="py-2 px-3 text-right">Status</th>
                        </tr>
                    </thead>
                <tbody>
                    @foreach ($vouchers as $voucher)
                        <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <td class="py-2 px-3 text-center">
                                <input type="checkbox" name="voucher_ids[]" value="{{ $voucher->id }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="py-2 px-3 text-xs font-mono text-gray-900 dark:text-white whitespace-nowrap">{{ Str::mask($voucher->code, '*', 0, -4) }}</td>
                            <td class="py-2 px-3 text-xs text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $voucher->site_name }}</td>
                            <td class="py-2 px-3 text-xs text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $voucher->package_name }}</td>
                            <td class="py-2 px-3 text-xs font-black text-gray-900 dark:text-white whitespace-nowrap">UGX {{ number_format($voucher->cost) }}</td>
                            <td class="py-2 px-3 text-right whitespace-nowrap">
                                <span
                                    class="px-2 py-0.5 text-[9px] font-black uppercase rounded-full {{ $voucher->is_used ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' }}">
                                    {{ $voucher->is_used ? 'Used' : 'Available' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
        </form>

        <!-- Pagination Links -->
        <div class="mt-4">
            {{ $vouchers->links() }}
        </div>
    </div>

    <!-- Upload Vouchers Modal -->
    <div id="upload-modal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50 transition-all duration-300">
        <div class="relative top-1/4 mx-auto p-0 border-none w-full max-w-md shadow-2xl rounded-xl bg-white dark:bg-gray-800 overflow-hidden">
            <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-b dark:border-gray-600 flex justify-between items-center">
                <h3 class="text-xs font-black uppercase tracking-widest text-gray-900 dark:text-white">Upload Vouchers</h3>
                <button type="button" onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.uploadVouchers') }}" method="POST" enctype="multipart/form-data" id="upload-form" class="space-y-4">
                    @csrf
                    <div>
                        <label for="upload_site_id" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Select Site</label>
                        @if(Auth::user()->site_id)
                            <input type="text" value="{{ Auth::user()->site->name }}" readonly 
                                class="bg-gray-100 dark:bg-gray-700 border-none text-gray-900 dark:text-gray-400 text-xs rounded-lg block w-full p-2.5 cursor-not-allowed select-none">
                            <input type="hidden" name="site_id" id="upload_site_id" value="{{ Auth::user()->site_id }}">
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    filterUploadPackages();
                                });
                            </script>
                        @else
                            <select name="site_id" id="upload_site_id"
                                class="bg-gray-100 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2.5 focus:ring-2 focus:ring-blue-500 transition"
                                onchange="filterUploadPackages()" required>
                                <option value="">Select a site</option>
                                @foreach ($sites as $site)
                                    <option value="{{ $site->id }}">{{ $site->name }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    <div>
                        <label for="package" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Select Package</label>
                        <select name="package_id" id="package"
                            class="bg-gray-100 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2.5 focus:ring-2 focus:ring-blue-500 transition"
                            required>
                            <option value="">Select a package</option>
                        </select>
                    </div>
                    <div>
                        <label for="file" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Excel File (.xlsx, .csv)</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-100 dark:border-gray-700 border-dashed rounded-xl hover:border-blue-400 transition-colors group">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-cloud-upload-alt text-2xl text-gray-300 group-hover:text-blue-400 transition-colors mb-2"></i>
                                <div class="flex text-xs text-gray-600 dark:text-gray-400">
                                    <label for="file" class="relative cursor-pointer bg-transparent rounded-md font-black text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                        <span>Upload a file</span>
                                        <input id="file" name="file" type="file" class="sr-only" required>
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-[9px] text-gray-400 uppercase font-bold">XLSX, CSV up to 10MB</p>
                                <div class="mt-2 p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800/50">
                                    <p class="text-[9px] font-black text-blue-700 dark:text-blue-400 uppercase tracking-widest mb-1 text-center">Required Format</p>
                                    <div class="flex justify-center font-mono text-[10px] text-blue-600 dark:text-blue-400">
                                        <span class="px-2 py-0.5 bg-white dark:bg-gray-800 rounded border italic text-gray-400">Vouchers in First Column</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="button" onclick="closeUploadModal()"
                            class="flex-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-[10px] font-black uppercase tracking-widest py-3 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-widest py-3 rounded-lg shadow-sm transition active:scale-95">
                            Upload Vouchers
                        </button>
                    </div>
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
            const siteId = document.getElementById('site_id').value;
            const packageSelect = document.getElementById('package_id');

            // Clear existing options
            packageSelect.innerHTML = '<option value="">All Packages</option>';

            if (siteId) {
                // Fetch packages based on selected site
                fetch(`{{ route('admin.sitePackages', '') }}/${siteId}`)
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
            const siteId = document.getElementById('upload_site_id').value;
            const packageSelect = document.getElementById('package');

            // Clear existing options
            packageSelect.innerHTML = '<option value="">Select a package</option>';

            if (siteId) {
                // Fetch packages based on selected site
                fetch(`{{ route('admin.sitePackages', '') }}/${siteId}`)
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
