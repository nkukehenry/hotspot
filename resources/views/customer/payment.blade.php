<!-- resources/views/customer/payment.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container mx-auto mt-8 flex justify-center">
        <div class="w-full max-w-md">
            <h1 class="text-2xl font-bold mb-4 text-center">Complete Payment</h1>
            <form action="{{ route('customer.processPayment', $package->id) }}" method="POST"
                class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                @csrf
                <div class="flex justify-center mb-4">
                    <img src="{{ asset('images/pay.png') }}" alt="Payment Icon" class="w-12 h-12">
                </div>
                <div class="text-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $package->name }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-white">{{ Str::limit($package->description, 60) }}</p>
                    <p class="text-base font-semibold text-green-600">UGX
                        {{ number_format($package->cost, 0, '.', ',') }}</p>
                </div>
                <div class="mb-4">
                    <label for="mobileNumber" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Mobile
                        Number</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 top-0 flex items-center ps-3.5 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 19 18">
                                <path
                                    d="M18 13.446a3.02 3.02 0 0 0-.946-1.985l-1.4-1.4a3.054 3.054 0 0 0-4.218 0l-.7.7a.983.983 0 0 1-1.39 0l-2.1-2.1a.983.983 0 0 1 0-1.389l.7-.7a2.98 2.98 0 0 0 0-4.217l-1.4-1.4a2.824 2.824 0 0 0-4.218 0c-3.619 3.619-3 8.229 1.752 12.979C6.785 16.639 9.45 18 11.912 18a7.175 7.175 0 0 0 5.139-2.325A2.9 2.9 0 0 0 18 13.446Z" />
                            </svg>
                        </div>
                        <input type="text" id="mobileNumber" name="mobileNumber"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            pattern="0[0-9]{9}" placeholder="0777000000" required>
                    </div>
                    <p id="helper-text-explanation" class="mt-2 text-sm text-gray-500 dark:text-gray-400">Enter a valid
                        mobile money number and make sure you approve the payment on your phone.
                    </p>
                </div>
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                    Pay
                </button>
            </form>
        </div>
    </div>
@endsection
