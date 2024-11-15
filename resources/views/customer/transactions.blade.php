     <!-- resources/views/customer/transactions.blade.php -->
     @extends('layouts.app')

     @section('content')
         <div class="flex justify-center min-h-screen py-5">
             <div
                 class="w-full max-w-4xl p-6 bg-gray-800 bg-opacity-90 border border-gray-700 rounded-lg shadow-lg dark:bg-gray-900 light:bg-white">
                 <h1 class="text-2xl font-bold text-white mb-4 text-center dark:text-white light:text-black">Your
                     Transactions</h1>
                 @if ($transactions->isEmpty())
                     <div class="flex flex-col items-center justify-center h-64">
                         <svg class="w-16 h-16 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                 d="M3 3h18M3 12h18M3 21h18" />
                         </svg>
                         <p class="text-gray-500 dark:text-gray-400">No Records Found</p>
                     </div>
                 @else
                     <table class="min-w-full rounded-lg overflow-hidden dark:bg-gray-800 light:bg-white">
                         <thead class="bg-gray-700 text-white dark:bg-gray-900 light:bg-gray-200">
                             <tr>
                                 <th class="py-2 px-4">Transaction ID</th>
                                 <th class="py-2 px-4">Voucher Code</th>
                                 <th class="py-2 px-4">Mobile Number</th>
                                 <th class="py-2 px-4">Status</th>
                                 <th class="py-2 px-4">Date</th>
                             </tr>
                         </thead>
                         <tbody>
                             @foreach ($transactions as $transaction)
                                 <tr class="text-gray-700 dark:text-gray-300">
                                     <td class="py-2 px-4">{{ $transaction->transaction_id }}</td>
                                     <td class="py-2 px-4">{{ $transaction->voucher->code }}</td>
                                     <td class="py-2 px-4">{{ $transaction->mobile_number }}</td>
                                     <td class="py-2 px-4">{{ $transaction->status }}</td>
                                     <td class="py-2 px-4">{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                 </tr>
                             @endforeach
                         </tbody>
                     </table>
                 @endif
             </div>
         </div>
     @endsection
