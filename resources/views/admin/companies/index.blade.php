@extends('layouts.admin')

@section('content')
    <div class="container mx-auto mt-4 px-4">
        <h1 class="text-xl font-black mb-4 text-gray-900 dark:text-white uppercase tracking-tight">
            <i class="fas fa-building mr-2 text-blue-500"></i> Manage Companies
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

        @can('create_companies')
        <button data-modal-target="add-company-modal" data-modal-toggle="add-company-modal"
            class="bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-widest py-2 px-4 rounded-lg mb-4 shadow-sm transition">
            <i class="fas fa-plus mr-2"></i> Add Company
        </button>
        @endcan

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 border-b dark:border-gray-700">
                        <th class="py-2 px-3 text-left">Name</th>
                        <th class="py-2 px-3 text-left">Fee Structure</th>
                        <th class="py-2 px-3 text-left">Status</th>
                        <th class="py-2 px-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 dark:text-gray-300 text-sm font-light">
                    @foreach ($companies as $company)
                        <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <td class="py-2 px-3 text-left whitespace-nowrap font-bold text-xs text-gray-900 dark:text-white">{{ $company->name }}</td>
                            <td class="py-2 px-3 text-left text-xs whitespace-nowrap">
                                <span class="bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 px-2 py-1 rounded text-[10px] font-bold">
                                    {{ $company->fee->name ?? 'None' }}
                                </span>
                            </td>
                            <td class="py-2 px-3 text-left text-xs whitespace-nowrap">
                                @if($company->status)
                                    <span class="bg-green-100 text-green-800 text-[10px] font-bold px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Active</span>
                                @else
                                    <span class="bg-red-100 text-red-800 text-[10px] font-bold px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Inactive</span>
                                @endif
                            </td>
                            <td class="py-1 px-3 text-right">
                                @can('edit_companies')
                                <button data-modal-target="edit-company-modal-{{ $company->id }}" data-modal-toggle="edit-company-modal-{{ $company->id }}"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-2">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @endcan
                                @can('delete_companies')
                                <form action="{{ route('admin.companies.destroy', $company->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </td>
                        </tr>

                        <!-- Edit Company Modal -->
                        <div id="edit-company-modal-{{ $company->id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                            <div class="relative p-4 w-full max-w-md max-h-full">
                                <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                                    <div class="flex items-center justify-between p-4 border-b rounded-t dark:border-gray-600">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Company</h3>
                                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="edit-company-modal-{{ $company->id }}">
                                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                                        </button>
                                    </div>
                                    <form action="{{ route('admin.companies.update', $company->id) }}" method="POST" class="p-4 space-y-4">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <label class="block text-[9px] font-black uppercase text-gray-400 mb-1">Company Name</label>
                                            <input type="text" name="name" value="{{ $company->name }}" class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" required shadow-sm>
                                        </div>
                                        <div>
                                            <label class="block text-[9px] font-black uppercase text-gray-400 mb-1">Fee Structure</label>
                                            <select name="fee_id" class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" required>
                                                @foreach($fees as $fee)
                                                    <option value="{{ $fee->id }}" {{ $company->fee_id == $fee->id ? 'selected' : '' }}>{{ $fee->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[9px] font-black uppercase text-gray-400 mb-1">Status</label>
                                            <select name="status" class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2">
                                                <option value="1" {{ $company->status ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ !$company->status ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-widest py-2.5 rounded-lg transition shadow-sm">Update Company</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                {{ $companies->links('partials.pagination') }}
            </div>
        </div>

        <!-- Add Company Modal -->
        <div id="add-company-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-md max-h-full">
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                    <div class="flex items-center justify-between p-4 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Add Company</h3>
                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="add-company-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                        </button>
                    </div>
                    <form action="{{ route('admin.companies.store') }}" method="POST" class="p-4 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-[9px] font-black uppercase text-gray-400 mb-1">Company Name</label>
                            <input type="text" name="name" class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" placeholder="e.g., Neonet Ltd" required shadow-sm>
                        </div>
                        <div>
                            <label class="block text-[9px] font-black uppercase text-gray-400 mb-1">Fee Structure</label>
                            <select name="fee_id" class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" required>
                                <option value="" disabled selected>Select a fee structure</option>
                                @foreach($fees as $fee)
                                    <option value="{{ $fee->id }}">{{ $fee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="status" value="1">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-widest py-2.5 rounded-lg transition shadow-sm">Save Company</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
