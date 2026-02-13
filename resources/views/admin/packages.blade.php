<!-- resources/views/admin/packages.blade.php -->
@extends('layouts.admin')

@section('content')
    <div class="container mx-auto mt-4 px-4">
        <h1 class="text-xl font-black mb-4 text-gray-900 dark:text-white uppercase tracking-tight">
            <i class="fas fa-box-open mr-2 text-blue-500"></i> Manage Packages
        </h1>



        <!-- Add Package Button -->
        @can('create_packages')
        <button data-modal-target="add-package-modal" data-modal-toggle="add-package-modal"
            class="bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-widest py-2 px-4 rounded-lg mb-4 shadow-sm transition">
            <i class="fas fa-plus mr-2"></i> Add Package
        </button>
        @endcan

        <!-- Filter by Site -->
        <div class="bg-white dark:bg-gray-800 p-3 rounded-lg shadow-sm mb-4 border border-gray-100 dark:border-gray-700 max-w-sm">
            <form method="GET" action="{{ route('admin.packages') }}">
                <label for="site_id" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Filter by Site</label>
                <div class="flex gap-2">
                    <select id="site_id" name="site_id"
                        class="bg-gray-100 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2">
                        <option value="">All Sites</option>
                        @foreach ($sites as $site)
                            <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>
                                {{ $site->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-gray-800 dark:bg-gray-700 text-white text-[10px] font-black uppercase tracking-widest px-4 py-2 rounded-lg transition">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-[10px] font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 border-b dark:border-gray-700">
                        <th class="py-2 px-3 text-left">Name</th>
                        <th class="py-2 px-3 text-left">Cost</th>
                        <th class="py-2 px-3 text-left">Description</th>
                        <th class="py-2 px-3 text-left">Site</th>
                        <th class="py-2 px-3 text-right">Actions</th>
                    </tr>
                </thead>
            <tbody>
                @foreach ($packages as $package)
                    <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                        <td class="py-2 px-3 text-xs font-bold text-gray-900 dark:text-white whitespace-nowrap">{{ $package->name }}</td>
                        <td class="py-2 px-3 text-xs font-black text-blue-600 dark:text-blue-400 whitespace-nowrap">UGX {{ number_format($package->cost) }}</td>
                        <td class="py-2 px-3 text-xs text-gray-500 dark:text-gray-400">{{ $package->description }}</td>
                        <td class="py-2 px-3 text-xs text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $package->site->name ?? 'N/A' }}</td>
                        <td class="py-2 px-3 text-right whitespace-nowrap">
                            @can('edit_packages')
                            <button data-modal-target="edit-modal-{{ $package->id }}"
                                data-modal-toggle="edit-modal-{{ $package->id }}"
                                class="text-xs font-black uppercase text-blue-600 hover:text-blue-800 px-2 py-1 transition">Edit</button>
                            @endcan
                            @can('delete_packages')
                            <button data-modal-target="delete-modal-{{ $package->id }}"
                                data-modal-toggle="delete-modal-{{ $package->id }}"
                                class="text-xs font-black uppercase text-red-600 hover:text-red-800 px-2 py-1 transition">Delete</button>
                            @endcan
                        </td>
                    </tr>

                    <!-- Edit Package Modal -->
                    <div id="edit-modal-{{ $package->id }}" tabindex="-1" aria-hidden="true"
                        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative p-4 w-full max-w-2xl max-h-full">
                            <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                                <div
                                    class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        Edit Package
                                    </h3>
                                    <button type="button"
                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                        data-modal-hide="edit-modal-{{ $package->id }}">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                    </button>
                                </div>
                                <div class="p-4 space-y-3">
                                    <form action="{{ route('admin.updatePackage', $package->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="mb-3">
                                            <label for="name" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Name</label>
                                            <input type="text" id="name" name="name" value="{{ $package->name }}"
                                                class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" required shadow-sm>
                                        </div>
                                        <div class="mb-3">
                                            <label for="cost" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Cost (UGX)</label>
                                            <input type="number" step="1" id="cost" name="cost" value="{{ $package->cost }}"
                                                class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" required shadow-sm>
                                        </div>
                                        <div class="mb-3">
                                            <label for="description" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Description</label>
                                            <textarea id="description" name="description" rows="2"
                                                class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" shadow-sm>{{ $package->description }}</textarea>
                                        </div>
                                        <div class="mb-4">
                                            <label for="site_id" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Site</label>
                                            <select id="site_id" name="site_id"
                                                class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" required shadow-sm>
                                                @foreach ($sites as $site)
                                                    <option value="{{ $site->id }}" {{ $package->site_id == $site->id ? 'selected' : '' }}>
                                                        {{ $site->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="submit"
                                            class="w-full bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-widest py-2.5 rounded-lg transition shadow-sm">
                                            Save Changes
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Confirmation Modal -->
                    <div id="delete-modal-{{ $package->id }}" tabindex="-1"
                        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative p-4 w-full max-w-md max-h-full">
                            <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                                <button type="button"
                                    class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                    data-modal-hide="delete-modal-{{ $package->id }}">
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
                                            stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you
                                        want to delete this package?</h3>
                                    <form action="{{ route('admin.deletePackage', $package->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                                            Yes, I'm sure
                                        </button>
                                    </form>
                                    <button data-modal-hide="delete-modal-{{ $package->id }}" type="button"
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

        <!-- Pagination Links -->
        {{ $packages->links() }}

        <!-- Add Package Modal -->
        <div id="add-package-modal" tabindex="-1" aria-hidden="true"
            class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-2xl max-h-full">
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Add Package
                        </h3>
                        <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-hide="add-package-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <div class="p-4 space-y-3">
                        <form action="{{ route('admin.addPackage') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="add_name" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Name</label>
                                <input type="text" id="add_name" name="name"
                                    class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" required shadow-sm>
                            </div>
                            <div class="mb-3">
                                <label for="add_cost" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Cost (UGX)</label>
                                <input type="number" step="1" id="add_cost" name="cost"
                                    class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" required shadow-sm>
                            </div>
                            <div class="mb-3">
                                <label for="add_description" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Description</label>
                                <textarea id="add_description" name="description" rows="2"
                                    class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" shadow-sm></textarea>
                            </div>
                            <div class="mb-4">
                                <label for="add_site_id" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Site</label>
                                <select id="add_site_id" name="site_id"
                                    class="bg-gray-50 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2" required shadow-sm>
                                    @foreach ($sites as $site)
                                        <option value="{{ $site->id }}">{{ $site->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-widest py-2.5 rounded-lg transition shadow-sm">
                                Add Package
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
