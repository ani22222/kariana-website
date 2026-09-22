<?php
/**
 * Courses Catalog View
 */
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';
?>
<section class="max-w-7xl mx-auto px-4 py-12">
    <div class="text-center mb-12">
        <h1 class="text-3xl sm:text-4xl font-bold text-emerald-950 mb-3">ক্বারীয়ানা শিক্ষাক্রম ও কোর্সসমূহ</h1>
        <p class="text-slate-600 max-w-2xl mx-auto text-sm sm:text-base">সহজ ও সহীহ পদ্ধতিতে সকল বয়সের শিক্ষার্থীদের জন্য কুরআন শিক্ষার বিভিন্ন মেয়াদী কোর্স।</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php if (!empty($courses)): ?>
            <?php foreach ($courses as $c): ?>
                <div class="islamic-card p-6 flex flex-col justify-between border-t-4 border-amber-500">
                    <div>
                        <div class="flex items-center justify-between text-xs text-slate-500 mb-3">
                            <span class="px-2.5 py-1 rounded bg-emerald-50 text-emerald-700 font-semibold"><?= htmlspecialchars($c['course_code'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                            <span>⏱️ <?= htmlspecialchars($c['duration'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <h2 class="text-xl font-bold text-emerald-950 mb-3">
                            <a href="<?= $baseUrl ?>/courses/<?= urlencode($c['slug']) ?>" class="hover:text-amber-600 transition">
                                <?= htmlspecialchars($c['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </h2>
                        <p class="text-sm text-slate-600 mb-4 line-clamp-3">
                            <?= htmlspecialchars($c['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-lg font-bold text-amber-600"><?= htmlspecialchars($c['fee'] ?? 'বিনামূল্যে', ENT_QUOTES, 'UTF-8') ?></span>
                        <a href="<?= $baseUrl ?>/courses/<?= urlencode($c['slug']) ?>" class="px-4 py-2 rounded-lg bg-emerald-800 text-white text-xs font-semibold hover:bg-emerald-700 transition">
                            বিস্তারিত দেখুন →
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
