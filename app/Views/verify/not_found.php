<?php
/**
 * Verification Not Found View
 */
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';
?>
<section class="max-w-md mx-auto px-4 py-16 text-center">
    <div class="w-20 h-20 mx-auto rounded-full bg-rose-100 text-rose-600 flex items-center justify-center text-3xl mb-4 shadow">
        ⚠️
    </div>
    <h1 class="text-2xl font-bold text-slate-800 mb-2">সনদ বা পরিচিতি পাওয়া যায়নি</h1>
    <p class="text-sm text-slate-600 mb-6">
        আপনার প্রদত্ত কিউআর কোড বা ভেরিফিকেশন আইডি (<span class="font-mono text-rose-700 font-bold"><?= htmlspecialchars($uuid ?? '') ?></span>) ডাটাবেজে নথিবদ্ধ নেই বা এটি এখনো অনুমোদিত হয়নি।
    </p>
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="<?= $baseUrl ?>/verify/kyc" class="px-5 py-2.5 rounded-xl bg-emerald-800 text-white font-bold text-sm hover:bg-emerald-700">
            নতুন ভেরিফিকেশন শুরু করুন
        </a>
        <a href="<?= $baseUrl ?>/" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-semibold text-sm hover:bg-slate-200">
            প্রধান পাতায় ফিরে যান
        </a>
    </div>
</section>
