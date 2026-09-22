<?php
/**
 * Blog Index View
 */
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';
?>
<section class="max-w-7xl mx-auto px-4 py-12">
    <div class="text-center mb-12">
        <h1 class="text-3xl sm:text-4xl font-bold text-emerald-950 mb-3">ইসলামী ব্লগ ও একাডেমি সংবাদ</h1>
        <p class="text-slate-600 max-w-2xl mx-auto text-sm sm:text-base">কুরআন শিক্ষা, তাজবীদ, হিফজ ও দৈনন্দিন ইসলামী জীবন সংক্রান্ত প্রবন্ধসমূহ।</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <?php if (!empty($posts)): ?>
            <?php foreach ($posts as $p): ?>
                <div class="islamic-card p-6 flex flex-col justify-between border-t-4 border-emerald-800">
                    <div>
                        <div class="text-xs text-slate-500 mb-2">📅 <?= htmlspecialchars(substr($p['created_at'] ?? '', 0, 10), ENT_QUOTES, 'UTF-8') ?></div>
                        <h2 class="text-xl font-bold text-emerald-950 mb-3">
                            <a href="<?= $baseUrl ?>/blog/<?= urlencode($p['slug']) ?>" class="hover:text-amber-600 transition">
                                <?= htmlspecialchars($p['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </h2>
                        <p class="text-sm text-slate-600 mb-4"><?= htmlspecialchars($p['excerpt'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="pt-4 border-t border-slate-100">
                        <a href="<?= $baseUrl ?>/blog/<?= urlencode($p['slug']) ?>" class="text-xs font-bold text-emerald-800 hover:text-emerald-950">
                            সম্পূর্ণ পড়ুন →
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
