<?php
/**
 * Course Single Detail View
 */
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';
?>
<section class="max-w-4xl mx-auto px-4 py-12">
    <div class="islamic-card p-8 border-t-4 border-amber-600">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold"><?= htmlspecialchars($course['course_code'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
            <span class="text-2xl font-bold text-amber-600"><?= htmlspecialchars($course['fee'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
        </div>

        <h1 class="text-2xl sm:text-4xl font-bold text-emerald-950 mb-4"><?= htmlspecialchars($course['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="text-slate-600 text-base leading-relaxed mb-8"><?= htmlspecialchars($course['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>

        <?php if (!empty($course['syllabus'])): ?>
            <div class="mb-8 p-6 rounded-xl bg-emerald-50/60 border border-emerald-100">
                <h2 class="text-lg font-bold text-emerald-950 mb-3">কোর্স সিলেবাস ও পাঠ্যসূচি:</h2>
                <pre class="font-bengali whitespace-pre-wrap text-sm text-slate-700 leading-relaxed"><?= htmlspecialchars($course['syllabus'], ENT_QUOTES, 'UTF-8') ?></pre>
            </div>
        <?php endif; ?>

        <div class="flex items-center justify-between pt-6 border-t border-slate-200">
            <a href="<?= $baseUrl ?>/courses" class="text-emerald-700 font-semibold text-sm hover:underline">← সকল কোর্সে ফিরে যান</a>
            <a href="#admission" class="px-6 py-3 rounded-lg bg-amber-600 hover:bg-amber-500 text-emerald-950 font-bold text-sm shadow">ভর্তি আবেদন করুন</a>
        </div>
    </div>
</section>
