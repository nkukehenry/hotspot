<!-- resources/views/admin/users.blade.php -->
@extends('layouts.admin')

@section('content')
    <div class="container mx-auto mt-8">
        <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white"><i class="fas fa-users-cog mr-2 text-blue-500"></i>Manage Users</h1>

        <!-- Add User Button -->
        @can('create_users')
        <button data-modal-target="add-user-modal" data-modal-toggle="add-user-modal"
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 shadow-lg transition duration-200">
            <i class="fas fa-user-plus mr-2"></i>Add User
        </button>
        @endcan

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white dark:bg-gray-800">
            <thead>
                <tr class="bg-gray-200 dark:bg-gray-700">
                    <th class="py-2 text-gray-800 dark:text-gray-200">Name</th>
                    <th class="py-2 text-gray-800 dark:text-gray-200">Email</th>
                    <th class="py-2 text-gray-800 dark:text-gray-200">Role</th>
                    <th class="py-2 text-gray-800 dark:text-gray-200">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="text-gray-700 dark:text-gray-300">
                        <td class="py-2 px-2 whitespace-nowrap">{{ $user->name }}</td>
                        <td class="py-2 whitespace-nowrap">{{ $user->email }}</td>
                        <td class="py-2 whitespace-nowrap">{{ $user->roles->pluck('name')->join(', ') }}</td>
                        <td class="py-2 text-center whitespace-nowrap">
                            @can('edit_users')
                            <button data-modal-target="edit-modal-{{ $user->id }}"
                                data-modal-toggle="edit-modal-{{ $user->id }}"
                                class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 rounded">Edit</button>
                            @endcan
                            @can('delete_users')
                            <button data-modal-target="delete-modal-{{ $user->id }}"
                                data-modal-toggle="delete-modal-{{ $user->id }}"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">Delete</button>
                            @endcan
                        </td>
                    </tr>

                    <!-- Edit User Modal -->
                    <div id="edit-modal-{{ $user->id }}" tabindex="-1" aria-hidden="true"
                        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative p-4 w-full max-w-2xl max-h-full">
                            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                <div
                                    class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        Edit User
                                    </h3>
                                    <button type="button"
                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                        data-modal-hide="edit-modal-{{ $user->id }}">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                    </button>
                                </div>
                                <div class="p-4 md:p-5 space-y-4">
                                    <form action="{{ route('admin.updateUser', $user->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="mb-4">
                                            <label for="name"
                                                class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Name</label>
                                            <input type="text" id="name" name="name" value="{{ $user->name }}"
                                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200"
                                                required>
                                        </div>
                                        <div class="mb-4">
                                            <label for="email"
                                                class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Email</label>
                                            <input type="email" id="email" name="email" value="{{ $user->email }}"
                                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200"
                                                required>
                                        </div>
                                        <!-- Add more fields as necessary -->
                                        <button type="submit"
                                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Save
                                            Changes</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Confirmation Modal -->
                    <div id="delete-modal-{{ $user->id }}" tabindex="-1"
                        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative p-4 w-full max-w-md max-h-full">
                            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                <button type="button"
                                    class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                    data-modal-hide="delete-modal-{{ $user->id }}">
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                                <div class="p-4 md:p-5 text-center">
                                    <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you
                                        want to delete this user?</h3>
                                    <form action="{{ route('admin.deleteUser', $user->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                                            Yes, I'm sure
                                        </button>
                                    </form>
                                    <button data-modal-hide="delete-modal-{{ $user->id }}" type="button"
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

        <!-- Add User Modal -->
        <div id="add-user-modal" tabindex="-1" aria-hidden="true"
            class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-2xl max-h-full">
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Add User
                        </h3>
                        <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-hide="add-user-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <div class="p-4 md:p-5 space-y-4">
                        <form action="{{ route('admin.addUser') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="name"
                                    class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Name</label>
                                <input type="text" id="name" name="name"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200"
                                    required>
                            </div>
                            <div class="mb-4">
                                <label for="email"
                                    class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Email</label>
                                <input type="email" id="email" name="email"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200"
                                    required>
                            </div>
                            <div class="mb-4">
                                <label for="role"
                                    class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Role</label>
                                <select id="role" name="role"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200"
                                    required>
                                    @role('Owner')
                                        <option value="Owner">Platform Owner</option>
                                        <option value="Manager">Site Manager</option>
                                    @endrole
                                    @hasanyrole('Owner|Manager')
                                        <option value="Supervisor">Site Supervisor</option>
                                    @endhasanyrole
                                    <option value="Agent">Sales Agent</option>
                                </select>
                            </div>

                            @role('Owner')
                            <div class="mb-4">
                                <label for="site_id"
                                    class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Site</label>
                                <select id="site_id" name="site_id"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200">
                                    <option value="">None (Platform Level)</option>
                                    @foreach($sites as $site)
                                        <option value="{{ $site->id }}">{{ $site->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endrole
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Add
                                User</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
