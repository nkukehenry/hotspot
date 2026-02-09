@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Agent Cash Reconciliation</h1>
            <p class="text-sm text-gray-500">Manage and reconcile cash collected by site agents.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm" role="alert">
        <p>{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm" role="alert">
        <p>{{ session('error') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($agents as $agent)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center font-bold text-gray-600 dark:text-gray-300">
                        {{ substr($agent->name, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white">{{ $agent->name }}</h3>
                        <p class="text-[10px] text-gray-500 uppercase">{{ $agent->email }}</p>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-xl mb-6">
                    <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mb-1">Unreconciled Cash</p>
                    <p class="text-2xl font-black text-blue-600 dark:text-blue-400 font-mono">
                        {{ number_format($agent->cash_balance) }} <span class="text-xs font-normal opacity-70">UGX</span>
                    </p>
                </div>

                <form action="{{ route('admin.reconcile') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="agent_id" value="{{ $agent->id }}">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Amount to Collect</label>
                        <input type="number" name="amount" value="{{ $agent->cash_balance }}" step="0.01" max="{{ $agent->cash_balance }}"
                               class="w-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-2 text-sm font-bold focus:ring-2 focus:ring-blue-500">
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-lg transition-colors shadow-lg active:scale-95 @if($agent->cash_balance <= 0) opacity-50 cursor-not-allowed @endif" 
                            @if($agent->cash_balance <= 0) disabled @endif>
                        <i class="fas fa-handshake mr-2"></i>Confirm Collection
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full p-12 text-center bg-gray-50 dark:bg-gray-800 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700">
            <p class="text-gray-500 italic">No agents found for this site.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
