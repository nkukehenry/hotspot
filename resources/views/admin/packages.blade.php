<!-- resources/views/admin/packages.blade.php -->
@extends('layouts.admin')

@section('content')
    <div class="container mx-auto mt-8">
        <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Manage Packages</h1>



        <!-- Add Package Button -->
        <button data-modal-target="add-package-modal" data-modal-toggle="add-package-modal"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4">
            Add Package
        </button>

        <!-- Filter by Location -->
        <form method="GET" action="{{ route('admin.packages') }}" class="mb-4">
            <label for="location_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Filter by
                Location</label>
            <select id="location_id" name="location_id"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200">
                <option value="">All Locations</option>
                @foreach ($locations as $location)
                    <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                        {{ $location->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Filter
            </button>
        </form>

        <table class="min-w-full bg-white dark:bg-gray-800">
            <thead>
                <tr class="bg-gray-200 dark:bg-gray-700">
                    <th class="py-2 text-gray-800 dark:text-gray-200">Name</th>
                    <th class="py-2 text-gray-800 dark:text-gray-200">Cost</th>
                    <th class="py-2 text-gray-800 dark:text-gray-200">Description</th>
                    <th class="py-2 text-gray-800 dark:text-gray-200">Location</th>
                    <th class="py-2 text-gray-800 dark:text-gray-200">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($packages as $package)
                    <tr class="text-gray-700 dark:text-gray-300">
                        <td class="py-2 px-2">{{ $package->name }}</td>
                        <td class="py-2">UGX {{ number_format($package->cost) }}</td>
                        <td class="py-2">{{ $package->description }}</td>
                        <td class="py-2">{{ $package->location->name }}</td>
                        <td class="py-2">
                            <button data-modal-target="edit-modal-{{ $package->id }}"
                                data-modal-toggle="edit-modal-{{ $package->id }}"
                                class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 rounded">Edit</button>
                            <button data-modal-target="delete-modal-{{ $package->id }}"
                                data-modal-toggle="delete-modal-{{ $package->id }}"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">Delete</button>
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
                                <div class="p-4 md:p-5 space-y-4">
                                    <form action="{{ route('admin.updatePackage', $package->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="mb-4">
                                            <label for="name"
                                                class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Name</label>
                                            <input type="text" id="name" name="name"
                                                value="{{ $package->name }}"
                                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200"
                                                required>
                                        </div>
                                        <div class="mb-4">
                                            <label for="cost"
                                                class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Cost</label>
                                            <input type="number" step="0.01" id="cost" name="cost"
                                                value="{{ $package->cost }}"
                                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200"
                                                required>
                                        </div>
                                        <div class="mb-4">
                                            <label for="description"
                                                class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Description</label>
                                            <textarea id="description" name="description"
                                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200">{{ $package->description }}</textarea>
                                        </div>
                                        <div class="mb-4">
                                            <label for="location_id"
                                                class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Location</label>
                                            <select id="location_id" name="location_id"
                                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200"
                                                required>
                                                @foreach ($locations as $location)
                                                    <option value="{{ $location->id }}"
                                                        {{ $package->location_id == $location->id ? 'selected' : '' }}>
                                                        {{ $location->name }}
                                                    </option>
                                                @endforeach
                                            </select>
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
                    <div class="p-4 md:p-5 space-y-4">
                        <form action="{{ route('admin.addPackage') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="name"
                                    class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Name</label>
                                <input type="text" id="name" name="name"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200"
                                    required>
                            </div>
                            <div class="mb-4">
                                <label for="cost"
                                    class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Cost</label>
                                <input type="number" step="0.01" id="cost" name="cost"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200"
                                    required>
                            </div>
                            <div class="mb-4">
                                <label for="description"
                                    class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Description</label>
                                <textarea id="description" name="description"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200"></textarea>
                            </div>
                            <div class="mb-4">
                                <label for="location_id"
                                    class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Location</label>
                                <select id="location_id" name="location_id"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200"
                                    required>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Add
                                Package</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
