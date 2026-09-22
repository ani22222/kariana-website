<?php
/**
 * Books & Publications Catalog View
 */
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';
?>
<section class="max-w-7xl mx-auto px-4 py-12">
    <div class="text-center mb-12">
        <h1 class="text-3xl sm:text-4xl font-bold text-emerald-950 mb-3">ক্বারীয়ানা প্রকাশনা ও গ্রন্থতালিকা</h1>
        <p class="text-slate-600 max-w-2xl mx-auto text-sm sm:text-base">১২টি বিশেষ সংকেতযুক্ত নূরানী কায়েদা, আমপারা ও পূর্ণাঙ্গ কুরআনুল কারীম।</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <?php if (!empty($books)): ?>
            <?php foreach ($books as $b): ?>
                <div class="islamic-card p-6 flex flex-col justify-between border-t-4 border-emerald-700">
                    <div>
                        <div class="text-xs text-amber-600 font-bold mb-2">পৃষ্ঠা সংখ্যা: <?= (int)($b['pages_count'] ?? 0) ?></div>
                        <h2 class="text-xl font-bold text-emerald-950 mb-2"><?= htmlspecialchars($b['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
                        <p class="text-xs text-slate-500 mb-3">লেখক: <?= htmlspecialchars($b['author'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="text-sm text-slate-600 mb-4"><?= htmlspecialchars($b['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-lg font-bold text-emerald-800">৳ <?= (int)($b['price'] ?? 0) ?></span>
                            <?php if (!empty($b['discount_price'])): ?>
                                <span class="text-xs text-slate-400 line-through ml-1">৳ <?= (int)$b['price'] ?></span>
                            <?php endif; ?>
                        </div>
                        <a href="<?= htmlspecialchars($b['buy_link'] ?? '#', ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="px-4 py-2 rounded-lg bg-amber-600 text-emerald-950 text-xs font-bold hover:bg-amber-500 transition">
                            অর্ডার করুন ↗
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
