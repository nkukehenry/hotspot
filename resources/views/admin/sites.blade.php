<!-- resources/views/admin/sites.blade.php -->
@extends('layouts.admin')

@section('content')
    <div class="container mx-auto mt-8">
        <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white"><i class="fas fa-location-dot mr-2 text-blue-500"></i>Manage Sites</h1>

        <!-- Add Site Button -->
        @can('create_sites')
        <button data-modal-target="add-site-modal" data-modal-toggle="add-site-modal"
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 shadow-lg transition duration-200">
            <i class="fas fa-plus mr-2"></i>Add Site
        </button>
        @endcan

        <div class="px-2 py-3 bg-white dark:bg-gray-800">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white dark:bg-gray-800">
                <thead>
                    <tr class="bg-gray-200 dark:bg-gray-700 text-sm uppercase leading-normal">
                        <th class="py-2 px-3 text-left text-gray-800 dark:text-gray-200">Name</th>
                        <th class="py-2 px-3 text-left text-gray-800 dark:text-gray-200">Cash Balance</th>
                        <th class="py-2 px-3 text-left text-gray-800 dark:text-gray-200">Digital Balance</th>
                        <th class="py-2 px-3 text-center text-gray-800 dark:text-gray-200">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 dark:text-gray-300 text-sm font-light">
                    @foreach ($sites as $site)
                        <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 even:bg-gray-50 dark:even:bg-gray-800 transition-colors duration-200">
                            <td class="py-1 px-3 text-left whitespace-nowrap font-medium">{{ $site->name }}</td>
                            <td class="py-1 px-3 text-left font-mono text-green-600 dark:text-green-400">
                                {{ number_format($site->cash_sales_balance ?? 0, 2) }}
                            </td>
                            <td class="py-1 px-3 text-left font-mono text-blue-600 dark:text-blue-400">
                                {{ number_format($site->digital_sales_balance ?? 0, 2) }}
                            </td>
                            <td class="py-1 px-3 text-center">
                                <button id="dropdownMenuIconButton-{{ $site->id }}" data-dropdown-toggle="dropdownDots-{{ $site->id }}" class="inline-flex items-center p-2 text-sm font-medium text-center text-gray-900 bg-white rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none dark:text-white focus:ring-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-600" type="button">
                                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 4 15">
                                        <path d="M3.5 1.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 6.041a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 5.959a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
                                    </svg>
                                </button>

                                <!-- Dropdown menu -->
                                <div id="dropdownDots-{{ $site->id }}" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 dark:divide-gray-600">
                                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownMenuIconButton-{{ $site->id }}">
                                        @can('view_sites')
                                        <li>
                                            <a href="{{ route('admin.site.details', $site->id) }}"
                                                class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white text-left">
                                                <i class="fas fa-info-circle mr-2"></i> Site Details
                                            </a>
                                        </li>
                                        @endcan
                                        <li>
                                            <a href="{{ route('customer.packages', $site->slug ?? $site->site_code) }}" target="_blank"
                                                class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white text-left">
                                                <i class="fas fa-external-link-alt mr-2"></i> Customer Page
                                            </a>
                                        </li>
                                        <li>
                                            <button onclick="copyToClipboard('{{ route('customer.packages', $site->slug ?? $site->site_code) }}')"
                                                class="block w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white text-left">
                                                <i class="fas fa-copy mr-2"></i> Copy Link
                                            </button>
                                        </li>
                                        <li>
                                            <a href="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode(route('customer.packages', $site->slug ?? $site->site_code)) }}&size=300x300"
                                                download="qrcode-{{ $site->site_code }}.png"
                                                class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white text-left"
                                                target="_blank">
                                                <i class="fas fa-qrcode mr-2"></i> Download QR
                                            </a>
                                        </li>
                                        @can('edit_sites')
                                        <li>
                                            <button data-modal-target="edit-modal-{{ $site->id }}" data-modal-toggle="edit-modal-{{ $site->id }}"
                                                class="block w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white text-left text-yellow-600 dark:text-yellow-400">
                                                <i class="fas fa-edit mr-2"></i> Edit
                                            </button>
                                        </li>
                                        @endcan
                                    </ul>
                                    @can('delete_sites')
                                    <div class="py-2">
                                        <button data-modal-target="delete-modal-{{ $site->id }}" data-modal-toggle="delete-modal-{{ $site->id }}"
                                            class="block w-full px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-red-500 dark:hover:text-white text-left">
                                            <i class="fas fa-trash mr-2"></i> Delete
                                        </button>
                                    </div>
                                    @endcan
                                </div>
                            </td>
                        </tr>

                        <!-- Edit Site Modal -->
                        <div id="edit-modal-{{ $site->id }}" tabindex="-1" aria-hidden="true"
                            class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                            <div class="relative p-4 w-full max-w-2xl max-h-full">
                                <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                                    <div
                                        class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                            Edit Site
                                        </h3>
                                        <button type="button"
                                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                            data-modal-hide="edit-modal-{{ $site->id }}">
                                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 14 14">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                            </svg>
                                            <span class="sr-only">Close modal</span>
                                        </button>
                                    </div>
                                    <div class="p-4 md:p-5 space-y-4">
                                        <form action="{{ route('admin.updateSite', $site->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="mb-4">
                                                <label for="name"
                                                    class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Name</label>
                                                <input type="text" id="name" name="name"
                                                    value="{{ $site->name }}"
                                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200"
                                                    required>
                                            </div>
                                            <div class="mb-4">
                                                <label for="address"
                                                    class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Address</label>
                                                <input type="text" id="address" name="address"
                                                    value="{{ $site->address }}"
                                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200">
                                            </div>
                                            
                                            <div class="grid grid-cols-2 gap-4 mb-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                                <div class="col-span-2 text-xs font-bold text-blue-600 dark:text-blue-400 uppercase">Fee Configuration</div>
                                                <div>
                                                    <label class="block text-gray-700 dark:text-gray-300 text-xs font-bold mb-1">Customer Fee (Fixed)</label>
                                                    <input type="number" step="0.01" name="customer_fee_fixed" value="{{ $site->customer_fee_fixed }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-xs text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200">
                                                </div>
                                                <div>
                                                    <label class="block text-gray-700 dark:text-gray-300 text-xs font-bold mb-1">Customer Fee (%)</label>
                                                    <input type="number" step="0.01" name="customer_fee_percent" value="{{ $site->customer_fee_percent }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-xs text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200">
                                                </div>
                                                <div>
                                                    <label class="block text-gray-700 dark:text-gray-300 text-xs font-bold mb-1">Site Fee (Fixed)</label>
                                                    <input type="number" step="0.01" name="site_fee_fixed" value="{{ $site->site_fee_fixed }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-xs text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200">
                                                </div>
                                                <div>
                                                    <label class="block text-gray-700 dark:text-gray-300 text-xs font-bold mb-1">Site Fee (%)</label>
                                                    <input type="number" step="0.01" name="site_fee_percent" value="{{ $site->site_fee_percent }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-xs text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200">
                                                </div>
                                            </div>

                                            <button type="submit"
                                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Save
                                                Changes</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Confirmation Modal -->
                        <div id="delete-modal-{{ $site->id }}" tabindex="-1"
                            class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                            <div class="relative p-4 w-full max-w-md max-h-full">
                                <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                                    <button type="button"
                                        class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                        data-modal-hide="delete-modal-{{ $site->id }}">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                    </button>
                                    <div class="p-4 md:p-5 text-center">
                                        <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200"
                                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 20 20">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                        <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure
                                            you
                                            want to delete this site?</h3>
                                        <form action="{{ route('admin.deleteSite', $site->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                                                Yes, I'm sure
                                            </button>
                                        </form>
                                        <button data-modal-hide="delete-modal-{{ $site->id }}" type="button"
                                            class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">No,
                                            cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
                </table>
            </div>
        </div>

        <!-- Add Site Modal -->
        <div id="add-site-modal" tabindex="-1" aria-hidden="true"
            class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-2xl max-h-full">
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Add Site
                        </h3>
                        <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-hide="add-site-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <div class="p-4 md:p-5 space-y-4">
                        <form action="{{ route('admin.addSite') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="name"
                                    class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Name</label>
                                <input type="text" id="name" name="name"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200"
                                    required>
                            </div>
                            <div class="mb-4">
                                <label for="address"
                                    class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Address</label>
                                <input type="text" id="address" name="address"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200"
                                    required>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="col-span-2 text-xs font-bold text-blue-600 dark:text-blue-400 uppercase">Fee Configuration</div>
                                <div>
                                    <label class="block text-gray-700 dark:text-gray-300 text-xs font-bold mb-1">Customer Fee (Fixed)</label>
                                    <input type="number" step="0.01" name="customer_fee_fixed" value="0.00" class="shadow appearance-none border rounded w-full py-2 px-3 text-xs text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200">
                                </div>
                                <div>
                                    <label class="block text-gray-700 dark:text-gray-300 text-xs font-bold mb-1">Customer Fee (%)</label>
                                    <input type="number" step="0.01" name="customer_fee_percent" value="0.00" class="shadow appearance-none border rounded w-full py-2 px-3 text-xs text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200">
                                </div>
                                <div>
                                    <label class="block text-gray-700 dark:text-gray-300 text-xs font-bold mb-1">Site Fee (Fixed)</label>
                                    <input type="number" step="0.01" name="site_fee_fixed" value="0.00" class="shadow appearance-none border rounded w-full py-2 px-3 text-xs text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200">
                                </div>
                                <div>
                                    <label class="block text-gray-700 dark:text-gray-300 text-xs font-bold mb-1">Site Fee (%)</label>
                                    <input type="number" step="0.01" name="site_fee_percent" value="0.00" class="shadow appearance-none border rounded w-full py-2 px-3 text-xs text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200">
                                </div>
                            </div>

                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Add
                                Site</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Link copied to clipboard!');
            }).catch(err => {
                console.error('Could not copy text: ', err);
            });
        }
    </script>
@endsection
