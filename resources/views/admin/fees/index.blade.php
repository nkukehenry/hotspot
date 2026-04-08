@extends('layouts.admin')

@section('content')
    <div class="container mx-auto mt-4 px-4">
        <h1 class="text-xl font-black mb-4 text-gray-900 dark:text-white uppercase tracking-tight">
            <i class="fas fa-money-bill-wave mr-2 text-green-500"></i> Manage Fee Structures
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

        @can('create_fees')
        <button data-modal-target="add-fee-modal" data-modal-toggle="add-fee-modal"
            class="bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-widest py-2 px-4 rounded-lg mb-4 shadow-sm transition">
            <i class="fas fa-plus mr-2"></i> Add Fee Structure
        </button>
        @endcan

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 border-b dark:border-gray-700">
                        <th class="py-2 px-3 text-left">Name</th>
                        <th class="py-2 px-3 text-left">Customer Fee</th>
                        <th class="py-2 px-3 text-left">Site Fee</th>
                        <th class="py-2 px-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 dark:text-gray-300 text-sm font-light">
                    @foreach ($fees as $fee)
                        <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <td class="py-2 px-3 text-left whitespace-nowrap font-bold text-xs text-gray-900 dark:text-white">{{ $fee->name }}</td>
                            <td class="py-2 px-3 text-left text-xs whitespace-nowrap">
                                <span class="font-black text-gray-900 dark:text-white">UGX {{ number_format($fee->customer_fee_fixed) }}</span>
                                <span class="text-gray-400 mx-1">+</span>
                                <span class="font-black text-blue-600 dark:text-blue-400">{{ $fee->customer_fee_percent }}%</span>
                            </td>
                            <td class="py-2 px-3 text-left text-xs whitespace-nowrap">
                                <span class="font-black text-gray-900 dark:text-white">UGX {{ number_format($fee->site_fee_fixed) }}</span>
                                <span class="text-gray-400 mx-1">+</span>
                                <span class="font-black text-green-600 dark:text-green-400">{{ $fee->site_fee_percent }}%</span>
                            </td>
                            <td class="py-1 px-3 text-right">
                                @can('edit_fees')
                                <button data-modal-target="edit-fee-modal-{{ $fee->id }}" data-modal-toggle="edit-fee-modal-{{ $fee->id }}"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-2">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @endcan
                                @can('delete_fees')
                                <form action="{{ route('admin.fees.destroy', $fee->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </td>
                        </tr>

                        <!-- Edit Fee Modal -->
                        <div id="edit-fee-modal-{{ $fee->id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                            <div class="relative p-4 w-full max-w-md max-h-full">
                                <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                                    <div class="flex items-center justify-between p-4 border-b rounded-t dark:border-gray-600">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Fee Structure</h3>
                                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="edit-fee-modal-{{ $fee->id }}">
                                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                                        </button>
                                    </div>
                                    <form action="{{ route('admin.fees.update', $fee->id) }}" method="POST" class="p-4 space-y-4">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <label class="block text-[9px] font-black uppercase text-gray-400 mb-1">Name</label>
                                            <input type="text" name="name" value="{{ $fee->name }}" class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" required shadow-sm>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-[9px] font-black uppercase text-gray-400 mb-1">Customer Fixed</label>
                                                <input type="number" step="1" name="customer_fee_fixed" value="{{ $fee->customer_fee_fixed }}" class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" required shadow-sm>
                                            </div>
                                            <div>
                                                <label class="block text-[9px] font-black uppercase text-gray-400 mb-1">Customer %</label>
                                                <input type="number" step="0.01" name="customer_fee_percent" value="{{ $fee->customer_fee_percent }}" class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" required shadow-sm>
                                            </div>
                                            <div>
                                                <label class="block text-[9px] font-black uppercase text-gray-400 mb-1">Site Fixed</label>
                                                <input type="number" step="1" name="site_fee_fixed" value="{{ $fee->site_fee_fixed }}" class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" required shadow-sm>
                                            </div>
                                            <div>
                                                <label class="block text-[9px] font-black uppercase text-gray-400 mb-1">Site %</label>
                                                <input type="number" step="0.01" name="site_fee_percent" value="{{ $fee->site_fee_percent }}" class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" required shadow-sm>
                                            </div>
                                        </div>
                                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-widest py-2.5 rounded-lg transition shadow-sm">Update Fee Structure</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                {{ $fees->links('partials.pagination') }}
            </div>
        </div>

        <!-- Add Fee Modal -->
        <div id="add-fee-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-md max-h-full">
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                    <div class="flex items-center justify-between p-4 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Add Fee Structure</h3>
                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="add-fee-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                        </button>
                    </div>
                    <form action="{{ route('admin.fees.store') }}" method="POST" class="p-4 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-[9px] font-black uppercase text-gray-400 mb-1">Name</label>
                            <input type="text" name="name" class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" placeholder="e.g., Standard Fees" required shadow-sm>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[9px] font-black uppercase text-gray-400 mb-1">Customer Fixed</label>
                                <input type="number" step="1" name="customer_fee_fixed" value="0" class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" required shadow-sm>
                            </div>
                            <div>
                                <label class="block text-[9px] font-black uppercase text-gray-400 mb-1">Customer %</label>
                                <input type="number" step="0.01" name="customer_fee_percent" value="0" class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" required shadow-sm>
                            </div>
                            <div>
                                <label class="block text-[9px] font-black uppercase text-gray-400 mb-1">Site Fixed</label>
                                <input type="number" step="1" name="site_fee_fixed" value="0" class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" required shadow-sm>
                            </div>
                            <div>
                                <label class="block text-[9px] font-black uppercase text-gray-400 mb-1">Site %</label>
                                <input type="number" step="0.01" name="site_fee_percent" value="0" class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" required shadow-sm>
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-widest py-2.5 rounded-lg transition shadow-sm">Save Fee Structure</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
