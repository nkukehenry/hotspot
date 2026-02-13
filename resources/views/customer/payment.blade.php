<!-- resources/views/customer/payment.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container mx-auto mt-6 flex justify-center">
        <div class="w-full max-w-md">
            
            <form id="payment-form" action="{{ route('customer.processPayment', $package->code) }}" method="POST"
                class="relative bg-white shadow-md rounded-xl px-6 pt-5 pb-6 mb-4 dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                @csrf
                <h3 class="text-xl font-bold mb-3 text-center">Complete Payment</h3>
                <div class="flex justify-center mb-3">
                    <img src="{{ asset('images/pay.png') }}" alt="Payment Icon" class="w-10 h-10">
                </div>
                <div class="text-center mb-3">
                    <h2 class="text-base font-bold text-gray-900 dark:text-white">{{ $package->name }}</h2>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">
                        {{ Str::limit($package->description, 60) }}</p>
                    <p class="text-lg font-black text-green-600 mt-1">UGX
                        {{ number_format($package->cost, 0, '.', ',') }}</p>
                </div>
                <div class="mb-3">
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
                    <p id="helper-text-explanation" class="mt-1 text-[11px] text-gray-500 dark:text-gray-400 text-center">Enter
                        a valid
                        mobile money number and make sure you approve the payment on your phone.
                    </p>

                    <div class="flex justify-center w-full pt-1">
                        <img class="img-responsive center-block rounded text-center opacity-80" src="{{ asset('images/momo.png') }}"
                            width="100px" alt="Mobile Money">
                    </div>
                </div>
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1.5 px-4 rounded-lg focus:outline-none focus:shadow-outline w-full text-sm">
                    Pay
                </button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<style>
    .swal2-lean-popup {
        padding: 1rem !important;
        border-radius: 1rem !important;
    }
    .swal2-lean-title {
        font-size: 0.875rem !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        margin: 0.5rem 0 !important;
    }
    .swal2-lean-content {
        font-size: 0.75rem !important;
        font-weight: 500 !important;
        margin: 0 !important;
    }
    .swal2-lean-confirm-button {
        padding: 0.6rem 3.5rem !important;
        font-size: 0.75rem !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        border-radius: 0.75rem !important;
        background-color: #2563eb !important;
        color: white !important;
        border: none !important;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1) !important;
    }
    .swal2-lean-icon {
        transform: scale(0.6);
        margin: 0 auto !important;
    }
</style>
<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false,
            width: '280px',
            customClass: {
                popup: 'swal2-lean-popup',
                title: 'swal2-lean-title',
                htmlContainer: 'swal2-lean-content',
                icon: 'swal2-lean-icon'
            }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Payment Failed',
            text: "{{ session('error') }}",
            confirmButtonText: 'OK',
            confirmButtonColor: '#3B82F6',
            width: '280px',
            customClass: {
                popup: 'swal2-lean-popup',
                title: 'swal2-lean-title',
                htmlContainer: 'swal2-lean-content',
                confirmButton: 'swal2-lean-confirm-button',
                icon: 'swal2-lean-icon'
            }
        });
    @endif

    document.getElementById('payment-form').addEventListener('submit', function(e) {
        Swal.fire({
            title: 'Processing...',
            text: 'Approve prompt on your phone',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            width: '260px',
            padding: '1.5rem',
            customClass: {
                popup: 'swal2-lean-popup',
                title: 'swal2-lean-title',
                htmlContainer: 'swal2-lean-content'
            },
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });
</script>
@endsection
