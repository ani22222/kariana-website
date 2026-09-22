<?php
/**
 * Blog Post Detail View
 */
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';
?>

<article class="max-w-4xl mx-auto px-4 py-12">
    <nav class="text-xs text-emerald-800 mb-6 flex items-center gap-2">
        <a href="<?= $baseUrl ?>/" class="hover:underline">হোম</a>
        <span>›</span>
        <a href="<?= $baseUrl ?>/blog" class="hover:underline">ব্লগ</a>
        <span>›</span>
        <span class="text-slate-600 truncate"><?= htmlspecialchars($post['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
    </nav>

    <header class="mb-8">
        <h1 class="text-2xl sm:text-4xl font-bold text-emerald-950 mb-4 leading-snug">
            <?= htmlspecialchars($post['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>
        </h1>
        <div class="flex items-center gap-4 text-xs text-slate-500 pb-4 border-b border-slate-200">
            <span>📅 <?= htmlspecialchars($post['published_at'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8') ?></span>
            <span>👁️ <?= (int)($post['views_count'] ?? 0) ?> বার পঠিত</span>
            <span>✍️ কারিয়ানা একাডেমি</span>
        </div>
    </header>

    <div class="prose max-w-none text-slate-700 leading-relaxed font-bengali text-base sm:text-lg">
        <?= $post['content'] ?? '' ?>
    </div>

    <footer class="mt-12 pt-6 border-t border-slate-200 flex items-center justify-between">
        <a href="<?= $baseUrl ?>/blog" class="text-emerald-700 hover:text-emerald-900 font-semibold text-sm">
            ← সকল ব্লগে ফিরে যান
        </a>
    </footer>
</article>
