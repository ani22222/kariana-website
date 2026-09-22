<?php
/**
 * Zakat Calculator View
 */
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';
?>
<section class="max-w-4xl mx-auto px-4 py-12">
    <div class="text-center mb-10">
        <h1 class="text-3xl sm:text-4xl font-bold text-emerald-950 mb-3">হানাফী সিলভার নিসাব যাকাত ক্যালকুলেটর</h1>
        <p class="text-slate-600 max-w-xl mx-auto text-sm sm:text-base">রৌপ্য নিসাব (৫২.৫ তোলা / ৬১২.৩৬ গ্রাম) মানদণ্ডে আপনার যাকাতযোগ্য সম্পদের হিসাব করুন।</p>
    </div>

    <div class="islamic-card p-8 border-t-4 border-amber-600">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200">
                <span class="text-xs text-amber-800 font-semibold">রূপার নিসাব দর (ভরি প্রতি):</span>
                <div class="text-xl font-bold text-amber-950">৳ <?= number_format((float)($settings['silver_rate_per_bhori'] ?? 2000), 2) ?></div>
            </div>
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200">
                <span class="text-xs text-emerald-800 font-semibold">মোট নিসাব সীমা (৫২.৫ ভরি):</span>
                <div class="text-xl font-bold text-emerald-950">৳ <?= number_format((float)($settings['silver_rate_per_bhori'] ?? 2000) * 52.5, 2) ?></div>
            </div>
        </div>

        <div class="p-4 rounded-lg bg-emerald-50/60 text-xs text-emerald-800 leading-relaxed">
            💡 <strong>নীতিমালা:</strong> যদি আপনার ঋণ বাদে মোট যাকাতযোগ্য সম্পদের মূল্য নিসাব সীমার সমান বা বেশি হয় এবং তা এক চন্দ্রবছর স্থায়ী থাকে, তবে মোট সম্পদের ২.৫% (চল্লিশ ভাগের এক ভাগ) যাকাত প্রদান করা ফরজ।
        </div>
    </div>
</section>
