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
        <div class="flex items-center gap-5 text-xs text-slate-500 pb-4 border-b border-slate-200">
            <span class="inline-flex items-center">
                <svg class="w-3.5 h-3.5 text-emerald-700 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <?= htmlspecialchars($post['published_at'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8') ?>
            </span>
            <span class="inline-flex items-center">
                <svg class="w-3.5 h-3.5 text-emerald-700 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <?= (int)($post['views_count'] ?? 0) ?> বার পঠিত
            </span>
            <span class="inline-flex items-center">
                <svg class="w-3.5 h-3.5 text-emerald-700 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                কারিয়ানা একাডেমি
            </span>
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
