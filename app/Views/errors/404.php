<?php
/**
 * 404 Error Page View
 */
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';
?>

<div class="max-w-3xl mx-auto px-4 py-20 text-center">
    <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-emerald-50 text-emerald-700 flex items-center justify-center text-4xl font-bold border-2 border-amber-500">
        ৪০৪
    </div>
    <h1 class="text-3xl sm:text-4xl font-bold text-emerald-950 mb-4">পাতাটি খুঁজে পাওয়া যায়নি</h1>
    <p class="text-slate-600 mb-8 max-w-md mx-auto">
        দুঃখিত! আপনি যে ঠিকানাটি খুঁজছেন তা মুছে ফেলা হয়েছে অথবা ঠিকানাটি ভুল লেখা হয়েছে।
    </p>
    <div class="flex items-center justify-center gap-4">
        <a href="<?= $baseUrl ?>/" class="px-6 py-3 rounded-lg bg-emerald-800 text-white font-medium hover:bg-emerald-700 transition">
            মূল পাতায় ফিরে যান
        </a>
        <a href="<?= $baseUrl ?>/courses" class="px-6 py-3 rounded-lg bg-amber-600 text-emerald-950 font-bold hover:bg-amber-500 transition">
            কোর্সসমূহ দেখুন
        </a>
    </div>
</div>
