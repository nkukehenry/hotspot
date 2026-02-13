<!-- resources/views/customer/voucher.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="flex flex-col items-center justify-start md:justify-center min-h-screen px-4 pt-8 md:pt-0 pb-20">
        <div class="w-full max-w-xs bg-white/95 dark:bg-gray-800/95 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl overflow-hidden backdrop-blur-sm">
            
            <!-- Site Branding Header (Recovered from Cache) -->
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-center">
                <div class="flex flex-col items-center">
                    <img src="{{ asset('images/logo.png') }}" class="h-8 dynamic-site-logo hidden mb-1.5" alt="Site Logo">
                    <h5 class="text-sm font-black uppercase tracking-widest text-blue-600 dark:text-blue-400 dynamic-site-name">Your Voucher</h5>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter mt-0.5">Payment Successful</p>
                </div>
            </div>

            <div class="p-5 text-center flex flex-col items-center">
                <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mb-3 border border-green-200 dark:border-green-800">
                    <i class="fas fa-check-circle text-green-500 text-lg"></i>
                </div>

                <div class="w-full bg-gray-50 dark:bg-gray-900/50 rounded-lg p-3 border border-dashed border-gray-200 dark:border-gray-700 mb-4 group transition-all hover:bg-white dark:hover:bg-gray-900 shadow-inner">
                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">Voucher Code</span>
                    <h2 id="voucher-code" class="text-xl font-black text-gray-900 dark:text-white tracking-[0.15em] font-mono select-all">
                        {{ $voucher->code }}
                    </h2>
                </div>

                <button onclick="copyVoucherCode()" 
                    class="w-full bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest py-2.5 rounded-lg hover:bg-blue-700 transition-all duration-200 transform hover:scale-[1.02] active:scale-95 shadow-lg shadow-blue-500/25 flex items-center justify-center space-x-2">
                    <i class="fas fa-copy"></i>
                    <span>Copy Voucher</span>
                </button>

                <p class="mt-3 text-[9px] font-bold text-gray-400 uppercase tracking-tight max-w-[200px]">
                    Enter this code on the Wi-Fi login portal to start browsing.
                </p>
            </div>

            <div class="p-3 bg-gray-50/50 dark:bg-gray-900/50 text-center border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('customer.transactions') }}" class="text-[9px] font-black uppercase tracking-widest text-gray-500 hover:text-blue-600 transition-colors">
                    View Transaction History
                </a>
            </div>
        </div>
    </div>

    <script>
        function copyVoucherCode() {
            const voucherCode = document.getElementById('voucher-code').textContent.trim();
            navigator.clipboard.writeText(voucherCode).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Copied!',
                    text: 'Voucher code copied to clipboard',
                    position: 'center',
                    showConfirmButton: false,
                    timer: 1500,
                    background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                    color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#111827',
                    customClass: {
                        popup: 'rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl backdrop-blur-md',
                        title: 'text-sm font-black uppercase tracking-widest',
                        htmlContainer: 'text-[11px] font-bold uppercase text-gray-400 tracking-tight'
                    }
                });
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
        }
    </script>
@endsection
