     <!-- resources/views/customer/transactions.blade.php -->
     @extends('layouts.app')

     @section('content')
         <div class="container mx-auto mt-8">
             <h1 class="text-2xl font-bold mb-4">Your Transactions</h1>
             <table class="min-w-full bg-white">
                 <thead>
                     <tr>
                         <th class="py-2">Transaction ID</th>
                         <th class="py-2">Voucher Code</th>
                         <th class="py-2">Mobile Number</th>
                         <th class="py-2">Status</th>
                         <th class="py-2">Date</th>
                     </tr>
                 </thead>
                 <tbody>
                     @foreach ($transactions as $transaction)
                         <tr class="text-gray-700">
                             <td class="py-2">{{ $transaction->transaction_id }}</td>
                             <td class="py-2">{{ $transaction->voucher->code }}</td>
                             <td class="py-2">{{ $transaction->mobile_number }}</td>
                             <td class="py-2">{{ $transaction->status }}</td>
                             <td class="py-2">{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                         </tr>
                     @endforeach
                 </tbody>
             </table>
         </div>
     @endsection
