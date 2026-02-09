@extends('layouts.admin')

@section('content')
    <div class="container mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                <i class="fas fa-plus-circle mr-2 text-blue-500"></i>Create New Role
            </h1>
        </div>

        <form action="{{ route('admin.roles.store') }}" method="POST" class="max-w-4xl">
            @csrf
            
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <div class="mb-6">
                    <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wide">
                        Role Name
                    </label>
                    <input type="text" name="name" id="name" required value="{{ old('name') }}"
                        placeholder="e.g. Content Moderator"
                        class="block w-full text-sm p-3 border border-gray-300 rounded-lg dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 uppercase tracking-wide">
                        Assign Permissions
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($permissions as $group => $groupPermissions)
                            <div class="bg-gray-50 dark:bg-gray-900/40 p-4 rounded-lg border border-gray-100 dark:border-gray-700">
                                <h4 class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase mb-3 border-b border-blue-100 dark:border-blue-900/50 pb-1">
                                    {{ ucfirst($group) }} Management
                                </h4>
                                <div class="space-y-2">
                                    @foreach($groupPermissions as $permission)
                                        <label class="flex items-center group cursor-pointer">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">
                                                {{ str_replace('_', ' ', str_replace($group, '', $permission->name)) ?: $permission->name }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-4 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg text-sm transition-colors">
                        Create Role
                    </button>
                    <a href="{{ route('admin.roles.index') }}" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-bold py-2.5 px-6 rounded-lg text-sm transition-colors">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
@endsection
