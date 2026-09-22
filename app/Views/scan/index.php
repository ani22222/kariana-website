<?php
/**
 * QR Book Scanner Gateway View
 */
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';
?>
<section class="max-w-2xl mx-auto px-4 py-12">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-emerald-950 mb-2">কিউআর কোড স্ক্যানার ও পাঠ নির্দেশিকা</h1>
        <p class="text-slate-600 text-sm">ক্বারীয়ানা কায়েদা বা আমপারার নির্দিষ্ট পৃষ্ঠার ভিডিও ক্লাস দেখতে কোড স্ক্যান বা প্রবেশ করুন।</p>
    </div>

    <div class="islamic-card p-8 border-t-4 border-amber-600 text-center">
        <div class="w-64 h-64 mx-auto mb-6 rounded-2xl bg-emerald-950/90 border-2 border-dashed border-amber-400/80 flex flex-col items-center justify-center text-emerald-200">
            <span class="text-4xl mb-2">📷</span>
            <span class="text-xs">ক্যামেরার সামনে কিউআর কোডটি ধরুন</span>
        </div>

        <form action="<?= $baseUrl ?>/scan" method="GET" class="max-w-md mx-auto flex gap-2">
            <input type="text" name="code" value="<?= htmlspecialchars($code ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="পৃষ্ঠা নম্বর বা লেসন কোড (উদা: KQ-C01-P12)" class="flex-grow px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-600">
            <button type="submit" class="px-5 py-2.5 rounded-lg bg-emerald-800 hover:bg-emerald-700 text-white font-semibold text-sm">খুঁজুন</button>
        </form>
    </div>
</section>
