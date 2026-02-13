<!-- resources/views/admin/sites.blade.php -->
@extends('layouts.admin')

@section('content')
    <div class="container mx-auto mt-4 px-4">
        <h1 class="text-xl font-black mb-4 text-gray-900 dark:text-white uppercase tracking-tight">
            <i class="fas fa-location-dot mr-2 text-blue-500"></i> Manage Sites
        </h1>

        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                <ul class="mt-1.5 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Add Site Button -->
        @can('create_sites')
        <button data-modal-target="add-site-modal" data-modal-toggle="add-site-modal"
            class="bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-widest py-2 px-4 rounded-lg mb-4 shadow-sm transition">
            <i class="fas fa-plus mr-2"></i> Add Site
        </button>
        @endcan

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 border-b dark:border-gray-700">
                        <th class="py-2 px-3 text-left">Logo</th>
                        <th class="py-2 px-3 text-left">Name</th>
                        <th class="py-2 px-3 text-left">Cash Balance</th>
                        <th class="py-2 px-3 text-left">Digital Balance</th>
                        <th class="py-2 px-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 dark:text-gray-300 text-sm font-light">
                    @foreach ($sites as $site)
                        <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <td class="py-1 px-3 text-left whitespace-nowrap">
                                @if($site->logo)
                                    <img src="{{ asset('storage/' . $site->logo) }}" alt="{{ $site->name }}" class="h-8 w-8 object-contain rounded-full border border-gray-200 dark:border-gray-600">
                                @else
                                    <div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-gray-500 text-xs">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="py-2 px-3 text-left whitespace-nowrap font-bold text-xs text-gray-900 dark:text-white">{{ $site->name }}</td>
                            <td class="py-2 px-3 text-left text-xs font-black text-green-600 dark:text-green-400 whitespace-nowrap">
                                <span class="text-[9px] opacity-70">UGX</span> {{ number_format($site->cash_sales_balance ?? 0) }}
                            </td>
                            <td class="py-2 px-3 text-left text-xs font-black text-blue-600 dark:text-blue-400 whitespace-nowrap">
                                <span class="text-[9px] opacity-70">UGX</span> {{ number_format($site->digital_sales_balance ?? 0) }}
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
                                    <div class="p-4 space-y-3">
                                        <form action="{{ route('admin.updateSite', $site->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                <div>
                                                    <label for="edit_name_{{ $site->id }}" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Name</label>
                                                    <input type="text" id="edit_name_{{ $site->id }}" name="name" value="{{ $site->name }}"
                                                        class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" required shadow-sm>
                                                </div>
                                                <div>
                                                    <label for="edit_address_{{ $site->id }}" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Address</label>
                                                    <input type="text" id="edit_address_{{ $site->id }}" name="address" value="{{ $site->address }}"
                                                        class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" shadow-sm>
                                                </div>
                                                <div>
                                                    <label for="edit_contact_email_{{ $site->id }}" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Contact Email</label>
                                                    <input type="email" id="edit_contact_email_{{ $site->id }}" name="contact_email" value="{{ $site->contact_email }}"
                                                        class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" shadow-sm>
                                                </div>
                                                <div>
                                                    <label for="edit_contact_phone_{{ $site->id }}" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Contact Phone</label>
                                                    <input type="text" id="edit_contact_phone_{{ $site->id }}" name="contact_phone" value="{{ $site->contact_phone }}"
                                                        class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" shadow-sm>
                                                </div>
                                            </div>

                                            @error('logo')
                                                <p class="mt-1 text-xs text-red-600 dark:text-red-400 font-bold">{{ $message }}</p>
                                            @enderror

                                            <div class="mt-3">
                                                <label for="edit_logo_{{ $site->id }}" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Site Logo</label>
                                                <div class="flex items-center gap-3">
                                                    @if($site->logo)
                                                        <img src="{{ asset('storage/' . $site->logo) }}" alt="Current Logo" class="h-8 w-8 object-contain rounded border dark:border-gray-600">
                                                    @endif
                                                    <input type="file" id="edit_logo_{{ $site->id }}" name="logo" class="block w-full text-[10px] text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                                </div>
                                            </div>
                                            
                                            <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-100 dark:border-gray-600/50">
                                                <div class="text-[9px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest mb-3">Settlement Configuration</div>
                                                <div class="grid grid-cols-2 gap-3">
                                                    <div>
                                                        <label for="edit_settlement_momo_{{ $site->id }}" class="block text-[8px] font-black uppercase text-gray-400 mb-1">MoMo Number</label>
                                                        <input type="text" id="edit_settlement_momo_{{ $site->id }}" name="settlement_momo_number" value="{{ $site->settlement_momo_number }}" class="bg-white dark:bg-gray-800 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2 shadow-sm">
                                                    </div>
                                                    <div>
                                                        <label for="edit_settlement_name_{{ $site->id }}" class="block text-[8px] font-black uppercase text-gray-400 mb-1">Account Name</label>
                                                        <input type="text" id="edit_settlement_name_{{ $site->id }}" name="settlement_account_name" value="{{ $site->settlement_account_name }}" class="bg-white dark:bg-gray-800 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2 shadow-sm">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-100 dark:border-gray-600/50">
                                                <div class="text-[9px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest mb-3">Fee Configuration</div>
                                                <div class="grid grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block text-[8px] font-black uppercase text-gray-400 mb-1">Customer Fee (Fixed)</label>
                                                        <input type="number" step="1" name="customer_fee_fixed" value="{{ $site->customer_fee_fixed }}" class="bg-white dark:bg-gray-800 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2 shadow-sm">
                                                    </div>
                                                    <div>
                                                        <label class="block text-[8px] font-black uppercase text-gray-400 mb-1">Customer Fee (%)</label>
                                                        <input type="number" step="0.01" name="customer_fee_percent" value="{{ $site->customer_fee_percent }}" class="bg-white dark:bg-gray-800 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2 shadow-sm">
                                                    </div>
                                                    <div>
                                                        <label class="block text-[8px] font-black uppercase text-gray-400 mb-1">Site Fee (Fixed)</label>
                                                        <input type="number" step="1" name="site_fee_fixed" value="{{ $site->site_fee_fixed }}" class="bg-white dark:bg-gray-800 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2 shadow-sm">
                                                    </div>
                                                    <div>
                                                        <label class="block text-[8px] font-black uppercase text-gray-400 mb-1">Site Fee (%)</label>
                                                        <input type="number" step="0.01" name="site_fee_percent" value="{{ $site->site_fee_percent }}" class="bg-white dark:bg-gray-800 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2 shadow-sm">
                                                    </div>
                                                </div>
                                            </div>

                                            <button type="submit"
                                                class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-widest py-2.5 rounded-lg transition shadow-sm">
                                                Update Site
                                            </button>
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
                    <div class="p-4 space-y-3">
                        <form action="{{ route('admin.addSite') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label for="add_name" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Name</label>
                                    <input type="text" id="add_name" name="name"
                                        class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" required shadow-sm>
                                </div>
                                <div>
                                    <label for="add_address" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Address</label>
                                    <input type="text" id="add_address" name="address"
                                        class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" required shadow-sm>
                                </div>
                                <div>
                                    <label for="add_contact_email" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Contact Email</label>
                                    <input type="email" id="add_contact_email" name="contact_email"
                                        class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" shadow-sm>
                                </div>
                                <div>
                                    <label for="add_contact_phone" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Contact Phone</label>
                                    <input type="text" id="add_contact_phone" name="contact_phone"
                                        class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" shadow-sm>
                                </div>
                            </div>
                            
                            <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-100 dark:border-gray-600/50">
                                <div class="text-[9px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest mb-3">Settlement Configuration</div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label for="add_settlement_momo" class="block text-[8px] font-black uppercase text-gray-400 mb-1">MoMo Number</label>
                                        <input type="text" id="add_settlement_momo" name="settlement_momo_number" class="bg-white dark:bg-gray-800 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2 shadow-sm">
                                    </div>
                                    <div>
                                        <label for="add_settlement_name" class="block text-[8px] font-black uppercase text-gray-400 mb-1">Account Name</label>
                                        <input type="text" id="add_settlement_name" name="settlement_account_name" class="bg-white dark:bg-gray-800 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2 shadow-sm">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <label for="add_logo" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Site Logo</label>
                                <input type="file" id="add_logo" name="logo" class="block w-full text-[10px] text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                            
                            <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-100 dark:border-gray-600/50">
                                <div class="text-[9px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest mb-3">Fee Configuration</div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[8px] font-black uppercase text-gray-400 mb-1">Customer Fee (Fixed)</label>
                                        <input type="number" step="1" name="customer_fee_fixed" value="0" class="bg-white dark:bg-gray-800 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2 shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-black uppercase text-gray-400 mb-1">Customer Fee (%)</label>
                                        <input type="number" step="0.01" name="customer_fee_percent" value="0.00" class="bg-white dark:bg-gray-800 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2 shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-black uppercase text-gray-400 mb-1">Site Fee (Fixed)</label>
                                        <input type="number" step="1" name="site_fee_fixed" value="0" class="bg-white dark:bg-gray-800 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2 shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-black uppercase text-gray-400 mb-1">Site Fee (%)</label>
                                        <input type="number" step="0.01" name="site_fee_percent" value="0.00" class="bg-white dark:bg-gray-800 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2 shadow-sm">
                                    </div>
                                </div>
                            </div>

                            <button type="submit"
                                class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-widest py-2.5 rounded-lg transition shadow-sm">
                                Add Site
                            </button>
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
