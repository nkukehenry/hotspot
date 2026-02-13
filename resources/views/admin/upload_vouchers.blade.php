<!-- resources/views/admin/upload_vouchers.blade.php -->
@extends('layouts.admin')

@section('content')
    <div class="container mx-auto mt-4 px-4">
        <h1 class="text-xl font-black mb-4 text-gray-900 dark:text-white uppercase tracking-tight">
            <i class="fas fa-ticket-alt mr-2 text-blue-500"></i> Upload Vouchers
        </h1>

        <div class="max-w-2xl mx-auto mb-4">
            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    <div class="font-bold mb-1"><i class="fas fa-exclamation-triangle mr-2"></i> Please fix the following errors:</div>
                    <ul class="mt-1.5 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="max-w-2xl mx-auto">
            <form action="{{ route('admin.uploadVouchers') }}" method="POST" enctype="multipart/form-data" 
                class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700">
                @csrf
                <div class="p-6 space-y-4">
                    @if(Auth::user()->site_id)
                    <div>
                        <label class="block text-[9px] font-black uppercase text-gray-400 mb-1">Site</label>
                        <input type="text" value="{{ Auth::user()->site->name }}" readonly 
                            class="bg-gray-100 dark:bg-gray-700 border-none text-gray-500 dark:text-gray-400 text-xs rounded-lg block w-full p-2.5 cursor-not-allowed select-none">
                    </div>
                    @endif
                    <div>
                        <label for="package" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Select Package</label>
                        <select name="package_id" id="package"
                            class="bg-gray-100 dark:bg-gray-700 border-none text-gray-900 dark:text-white text-xs rounded-lg block w-full p-2.5 focus:ring-2 focus:ring-blue-500 transition"
                            required>
                            @foreach ($packages as $package)
                                <option value="{{ $package->id }}">{{ $package->name }} ({{ $package->site->name ?? 'Unknown Site' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="file" class="block text-[9px] font-black uppercase text-gray-400 mb-1">Upload Format</label>
                        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800/50 mb-3 text-center">
                            <div class="flex justify-center font-mono text-[11px] text-blue-600 dark:text-blue-400">
                                <span class="px-3 py-1 bg-white dark:bg-gray-800 rounded border italic text-gray-400 text-xs">Vouchers must be in the FIRST column</span>
                            </div>
                        </div>

                        <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-100 dark:border-gray-700 border-dashed rounded-xl hover:border-blue-400 transition-colors group">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-cloud-upload-alt text-2xl text-gray-300 group-hover:text-blue-400 transition-colors mb-2"></i>
                                <div class="flex text-xs text-gray-600 dark:text-gray-400">
                                    <label for="file" class="relative cursor-pointer bg-transparent rounded-md font-black text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                        <span>Upload a file</span>
                                        <input id="file" name="file" type="file" class="sr-only" required>
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-[9px] text-gray-400 uppercase font-bold">XLSX, CSV up to 10MB</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-t dark:border-gray-600 flex justify-end">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-widest py-2.5 px-6 rounded-lg shadow-sm transition active:scale-95">
                        <i class="fas fa-upload mr-2"></i> Start Import
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
