<!-- resources/views/admin/settings.blade.php -->
@extends('layouts.admin')

@section('content')
    <div class="container mx-auto mt-8">
        <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white"><i class="fas fa-cog mr-2 text-blue-500"></i>System Settings</h1>
        <form action="{{ route('admin.updateSettings') }}" method="POST" enctype="multipart/form-data"
            class="bg-white dark:bg-gray-800 shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf
            <div class="mb-4">
                <label for="systemName" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">System
                    Name</label>
                <input type="text" id="systemName" name="systemName" value="{{ $settings->system_name }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200">
            </div>
            <div class="mb-4">
                <label for="logo" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Logo</label>
                <input type="file" id="logo" name="logo"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:text-gray-200">
            </div>
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-lg transition duration-200">
                <i class="fas fa-save mr-2"></i>Save Settings
            </button>
        </form>
    </div>
@endsection
