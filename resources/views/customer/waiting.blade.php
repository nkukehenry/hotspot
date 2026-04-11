@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-10 flex justify-center px-4">
    <div class="w-full max-w-md bg-white dark:bg-gray-800 shadow-2xl rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
        <div class="p-8 text-center">
            <!-- Animated Spinner -->
            <div class="flex justify-center mb-6">
                <div class="relative">
                    <div class="w-20 h-20 border-4 border-blue-100 border-t-blue-600 rounded-full animate-spin"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-mobile-alt text-2xl text-blue-600 animate-pulse"></i>
                    </div>
                </div>
            </div>

            <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white mb-2">Processing Payment</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-6 px-4">
                Please approve the payment request on your phone. Do not close this page.
            </p>

            <!-- Status Indicator -->
            <div id="status-container" class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 mb-6">
                <p id="status-text" class="text-sm font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider">
                    Waiting for approval...
                </p>
                <div class="mt-2 w-full bg-gray-200 dark:bg-gray-600 rounded-full h-1.5 overflow-hidden">
                    <div id="progress-bar" class="bg-blue-600 h-full transition-all duration-1000" style="width: 5%"></div>
                </div>
            </div>

            <div class="text-[11px] text-gray-400 dark:text-gray-500">
                Transaction ID: <span class="font-mono">{{ $transaction->transaction_id }}</span>
            </div>
        </div>
        
        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 border-t border-blue-100 dark:border-blue-900/30 text-center">
            <p class="text-xs text-blue-800 dark:text-blue-300">
                Once payment is confirmed, you will be redirected automatically.
            </p>
        </div>
    </div>
</div>

<script>
    const transactionId = "{{ $transaction->transaction_id }}";
    const statusUrl = "{{ route('customer.api.checkStatus', $transaction->transaction_id) }}";
    let attempts = 0;
    const maxAttempts = 40; // ~2 minutes (3s intervals)
    let progress = 5;

    function checkStatus() {
        fetch(statusUrl)
            .then(response => response.json())
            .then(data => {
                attempts++;
                
                // Update Progress Bar
                progress = Math.min(95, 5 + (attempts * 2.25));
                document.getElementById('progress-bar').style.width = progress + '%';

                if (data.status === 'completed') {
                    document.getElementById('status-text').textContent = 'Payment Confirmed!';
                    document.getElementById('status-text').classList.replace('text-blue-600', 'text-green-600');
                    document.getElementById('progress-bar').style.width = '100%';
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Redirecting to your voucher...',
                        timer: 1500,
                        showConfirmButton: false,
                        didClose: () => {
                            window.location.href = data.redirect_url;
                        }
                    });
                } else if (data.status === 'failed') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Payment Failed',
                        text: data.message || 'The payment was rejected or timed out.',
                        confirmButtonText: 'Try Again',
                        confirmButtonColor: '#3B82F6'
                    }).then(() => {
                        window.history.back();
                    });
                } else if (attempts < maxAttempts) {
                    setTimeout(checkStatus, 3000);
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Still Pending',
                        text: 'Your payment is taking longer than usual. If you approved it, the voucher will be sent to you via SMS.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3B82F6'
                    }).then(() => {
                        window.location.href = "{{ route('customer.transactions') }}";
                    });
                }
            })
            .catch(error => {
                console.error('Error checking status:', error);
                setTimeout(checkStatus, 5000);
            });
    }

    // Start polling
    setTimeout(checkStatus, 2000);
</script>
@endsection
