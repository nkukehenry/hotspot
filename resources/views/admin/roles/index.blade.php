@extends('layouts.admin')

@section('content')
    <div class="container mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                <i class="fas fa-user-shield mr-2 text-blue-500"></i>Roles & Permissions
            </h1>
            <a href="{{ route('admin.roles.create') }}" 
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 text-sm">
                <i class="fas fa-plus mr-2"></i>Create New Role
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400 font-bold border-b border-gray-200 dark:border-gray-700">
                            <th class="py-3 px-4 text-left">Role Name</th>
                            <th class="py-3 px-4 text-left">Permissions</th>
                            <th class="py-3 px-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        @foreach ($roles as $role)
                            <tr class="text-sm hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="py-3 px-4">
                                    <span class="font-bold text-gray-900 dark:text-white">{{ $role->name }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($role->permissions->take(10) as $permission)
                                            <span class="inline-block bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-[10px] px-2 py-0.5 rounded-full border border-gray-200 dark:border-gray-600">
                                                {{ $permission->name }}
                                            </span>
                                        @endforeach
                                        @if($role->permissions->count() > 10)
                                            <span class="text-[10px] text-gray-500 italic">+{{ $role->permissions->count() - 10 }} more</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        @if($role->name !== 'Owner')
                                            <a href="{{ route('admin.roles.edit', $role) }}" 
                                                class="text-blue-500 hover:text-blue-700 p-1 transition-colors" title="Edit Permissions">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if(!in_array($role->name, ['Manager', 'Supervisor', 'Agent']))
                                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" 
                                                    onsubmit="return confirm('Are you sure you want to delete this role?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700 p-1 transition-colors" title="Delete Role">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
