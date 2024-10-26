<!-- resources/views/customer/voucher.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container mx-auto mt-8 flex justify-center">
        <div class="w-full max-w-md bg-green-100 border border-green-400 text-green-700 px-4 py-6 rounded-lg shadow-md flex flex-col items-center"
            role="alert">
            <h1 class="text-2xl font-bold mb-4 text-center">Your Voucher</h1>
            <div class="flex justify-center mb-4">
                <img src="{{ asset('images/success.png') }}" alt="Success Icon" class="w-20 h-20">
            </div>
            <div class="text-center mb-4">
                <span class="font-medium">Voucher Code:</span>
                <span id="voucher-code" class="block sm:inline mx-2 font-bold mt-3">{{ $voucher->code }}</span>
            </div>
            <button onclick="copyVoucherCode()" class="mt-4 bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">
                Copy
            </button>
            <p class="mt-4 text-center">Use this code to access the WiFi service.</p>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-md hidden">
        Voucher code copied!
    </div>

    <script>
        function copyVoucherCode() {
            const voucherCode = document.getElementById('voucher-code').textContent;
            navigator.clipboard.writeText(voucherCode).then(() => {
                showToast();
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
        }

        function showToast() {
            const toast = document.getElementById('toast');
            toast.classList.remove('hidden');
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 3000); // Hide the toast after 3 seconds
        }
    </script>
@endsection
