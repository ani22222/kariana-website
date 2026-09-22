<?php
/**
 * Manager Dashboard View - Kariana Quran
 */
$csrfToken = \Core\Csrf::getToken();
?>
<div class="container mx-auto px-4 py-8">
    <!-- Top Bar -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between pb-6 border-b border-[#e5ddcb] mb-8">
        <div>
            <div class="inline-flex items-center px-3 py-1 bg-amber-50 border border-amber-200 text-gold-deep rounded-full text-xs font-bold mb-2">
                <i class="fas fa-calculator mr-1.5"></i> সেন্ট্রাল অপারেশনস ও ফিন্যান্স ডেস্ক
            </div>
            <h1 class="text-3xl font-extrabold text-emerald-night">অপারেশনস ও হিসাব ড্যাশবোর্ড</h1>
            <p class="text-sm text-slate-600 mt-0.5">
                স্বাগতম, <strong><?= htmlspecialchars($manager['name'] ?? 'ব্যবস্থাপক') ?></strong> | আয়-ব্যয় হিসাব খাতা, চালান ও কুরিয়ার বিতরণ মনিটরিং
            </p>
        </div>
        <div class="mt-4 md:mt-0 flex flex-wrap gap-2.5">
            <a href="<?= $baseUrl ?>/manager/ledger" class="bg-emerald-deep hover:bg-emerald-night text-white px-4 py-2.5 rounded-xl text-xs md:text-sm font-bold shadow transition flex items-center">
                <i class="fas fa-file-invoice-dollar mr-2 text-gold-shimmer"></i> নতুন ভাউচার এন্ট্রি
            </a>
            <a href="<?= $baseUrl ?>/manager/distributions" class="bg-gold-rich hover:bg-gold-deep text-white px-4 py-2.5 rounded-xl text-xs md:text-sm font-bold shadow transition flex items-center">
                <i class="fas fa-truck-fast mr-2"></i> নতুন বই চালান
            </a>
            <a href="<?= $baseUrl ?>/manager/logout" class="bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 px-3.5 py-2.5 rounded-xl text-xs md:text-sm font-bold transition flex items-center">
                <i class="fas fa-sign-out-alt mr-1"></i> প্রস্থান
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if ($success = \Core\Session::flash('success')): ?>
    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-900 font-bold flex items-center shadow-sm">
        <i class="fas fa-check-circle text-xl text-emerald-600 mr-3"></i> <?= htmlspecialchars($success) ?>
    </div>
    <?php endif; ?>

    <!-- Summary Metrics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <!-- Total Income -->
        <div class="bg-[#fffefb] p-4 rounded-2xl border border-emerald-200 shadow-sm">
            <span class="text-xs font-semibold text-emerald-700 block mb-1">মোট আয় (সর্বমোট)</span>
            <h3 class="text-xl font-black text-emerald-800 font-mono">
                ৳<?= \Core\BengaliHelper::toBengaliNumber(number_format($stats['totalIncome'], 0)) ?>
            </h3>
            <span class="text-[11px] text-slate-400 mt-1 block">বই বিক্রি ও অনুদান</span>
        </div>

        <!-- Total Expense -->
        <div class="bg-[#fffefb] p-4 rounded-2xl border border-red-200 shadow-sm">
            <span class="text-xs font-semibold text-red-700 block mb-1">মোট ব্যয়</span>
            <h3 class="text-xl font-black text-red-800 font-mono">
                ৳<?= \Core\BengaliHelper::toBengaliNumber(number_format($stats['totalExpense'], 0)) ?>
            </h3>
            <span class="text-[11px] text-slate-400 mt-1 block">মুদ্রণ ও কুরিয়ার খরচ</span>
        </div>

        <!-- Net Balance -->
        <div class="bg-[#fffefb] p-4 rounded-2xl border border-gold-rich/40 shadow-sm">
            <span class="text-xs font-semibold text-gold-deep block mb-1">বর্তমান ফান্ড স্থিতি</span>
            <h3 class="text-xl font-black text-emerald-night font-mono">
                ৳<?= \Core\BengaliHelper::toBengaliNumber(number_format($stats['balance'], 0)) ?>
            </h3>
            <span class="text-[11px] text-slate-400 mt-1 block">নেট তহবিল</span>
        </div>

        <!-- Total Books Dispatched -->
        <div class="bg-[#fffefb] p-4 rounded-2xl border border-[#e4dcce] shadow-sm">
            <span class="text-xs font-semibold text-slate-600 block mb-1">বিতরণকৃত বই</span>
            <h3 class="text-xl font-black text-slate-800 font-mono">
                <?= \Core\BengaliHelper::toBengaliNumber($stats['totalDispatched']) ?> কপি
            </h3>
            <span class="text-[11px] text-slate-400 mt-1 block">৬৪ জেলায় সরবরাহ</span>
        </div>

        <!-- Pending Dispatches -->
        <div class="bg-[#fffefb] p-4 rounded-2xl border border-amber-200 shadow-sm">
            <span class="text-xs font-semibold text-amber-700 block mb-1">অপেক্ষমাণ চালান</span>
            <h3 class="text-xl font-black text-amber-800 font-mono">
                <?= \Core\BengaliHelper::toBengaliNumber($stats['pendingDispatches']) ?> টি
            </h3>
            <span class="text-[11px] text-slate-400 mt-1 block">বুকিং বাকি</span>
        </div>

        <!-- Due Amount -->
        <div class="bg-[#fffefb] p-4 rounded-2xl border border-[#e4dcce] shadow-sm">
            <span class="text-xs font-semibold text-slate-600 block mb-1">বকেয়া বিল</span>
            <h3 class="text-xl font-black text-slate-700 font-mono">
                ৳<?= \Core\BengaliHelper::toBengaliNumber(number_format($stats['dueAmount'], 0)) ?>
            </h3>
            <span class="text-[11px] text-slate-400 mt-1 block">সংগ্রহযোগ্য পাওনা</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Recent Ledger Vouchers -->
        <div class="bg-[#fffefb] rounded-2xl border border-[#e4dcce] shadow-sm overflow-hidden flex flex-col justify-between">
            <div class="p-5 border-b border-[#ece4d6] bg-[#fbf9f4] flex items-center justify-between">
                <h3 class="font-bold text-emerald-night text-base flex items-center">
                    <i class="fas fa-receipt text-gold-rich mr-2"></i> সাম্প্রতিক হিসাব খাতা ভাউচার
                </h3>
                <a href="<?= $baseUrl ?>/manager/ledger" class="text-xs text-emerald-deep hover:underline font-bold">
                    সকল হিসাব দেখুন <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="p-4 overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500 font-bold">
                            <th class="py-2.5 px-3">ভাউচার</th>
                            <th class="py-2.5 px-3">তারিখ</th>
                            <th class="py-2.5 px-3">খাত / বিবরণ</th>
                            <th class="py-2.5 px-3 text-right">পরিমাণ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($recentLedger)): ?>
                        <tr><td colspan="4" class="py-4 text-center text-slate-400">কোনো এন্ট্রি নেই</td></tr>
                        <?php else: ?>
                        <?php foreach ($recentLedger as $row): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="py-2.5 px-3 font-mono font-bold text-slate-700 whitespace-nowrap">
                                <?= htmlspecialchars($row['voucher_no'] ?? '-') ?>
                            </td>
                            <td class="py-2.5 px-3 whitespace-nowrap text-slate-500">
                                <?= htmlspecialchars($row['transaction_date']) ?>
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="font-bold text-slate-800 block"><?= htmlspecialchars($row['category']) ?></span>
                                <span class="text-slate-500 text-[11px] line-clamp-1"><?= htmlspecialchars($row['description']) ?></span>
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold whitespace-nowrap">
                                <?php if ($row['type'] === 'income'): ?>
                                <span class="text-emerald-700">+৳<?= \Core\BengaliHelper::toBengaliNumber((int)$row['amount']) ?></span>
                                <?php else: ?>
                                <span class="text-red-600">-৳<?= \Core\BengaliHelper::toBengaliNumber((int)$row['amount']) ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="p-3 bg-[#fbf9f4] border-t border-[#ece4d6] text-right">
                <a href="<?= $baseUrl ?>/manager/ledger" class="inline-flex items-center text-xs font-bold text-emerald-deep hover:text-emerald-night">
                    <i class="fas fa-plus-circle mr-1"></i> নতুন আয় / ব্যয় এন্ট্রি করুন
                </a>
            </div>
        </div>

        <!-- Recent Book Dispatches & Courier Trackings -->
        <div class="bg-[#fffefb] rounded-2xl border border-[#e4dcce] shadow-sm overflow-hidden flex flex-col justify-between">
            <div class="p-5 border-b border-[#ece4d6] bg-[#fbf9f4] flex items-center justify-between">
                <h3 class="font-bold text-emerald-night text-base flex items-center">
                    <i class="fas fa-boxes-packing text-gold-rich mr-2"></i> সাম্প্রতিক বই চালান ও কুরিয়ার ট্র্যাকিং
                </h3>
                <a href="<?= $baseUrl ?>/manager/distributions" class="text-xs text-emerald-deep hover:underline font-bold">
                    সকল চালান দেখুন <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="p-4 overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500 font-bold">
                            <th class="py-2.5 px-3">প্রাপক / জেলা</th>
                            <th class="py-2.5 px-3">বই ও সংখ্যা</th>
                            <th class="py-2.5 px-3">কুরিয়ার ও ট্র্যাকিং</th>
                            <th class="py-2.5 px-3 text-right">স্ট্যাটাস</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($recentDistributions)): ?>
                        <tr><td colspan="4" class="py-4 text-center text-slate-400">কোনো চালান নেই</td></tr>
                        <?php else: ?>
                        <?php foreach ($recentDistributions as $dist): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="py-2.5 px-3">
                                <?php if ($dist['recipient_type'] === 'director'): ?>
                                <span class="font-bold text-emerald-night block line-clamp-1"><?= htmlspecialchars($dist['director_name'] ?? 'পরিচালক') ?></span>
                                <span class="text-[11px] text-gold-deep font-semibold"><?= htmlspecialchars($dist['district_name'] ?? 'জেলা') ?></span>
                                <?php else: ?>
                                <span class="font-bold text-slate-800 block line-clamp-1"><?= htmlspecialchars($dist['customer_name'] ?? 'গ্রাহক') ?></span>
                                <span class="text-[11px] text-slate-500"><?= htmlspecialchars($dist['customer_phone'] ?? '') ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="font-bold text-slate-800 block line-clamp-1"><?= htmlspecialchars($dist['book_title'] ?? 'বই') ?></span>
                                <span class="font-mono text-emerald-700 font-bold"><?= \Core\BengaliHelper::toBengaliNumber($dist['quantity']) ?> কপি</span>
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="font-semibold text-slate-700 block"><?= htmlspecialchars($dist['courier_name'] ?? 'কুরিয়ার') ?></span>
                                <span class="font-mono text-[11px] text-slate-400"><?= htmlspecialchars($dist['tracking_number'] ?? 'N/A') ?></span>
                            </td>
                            <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                <?php if ($dist['delivery_status'] === 'delivered'): ?>
                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">পৌঁছেছে</span>
                                <?php elseif ($dist['delivery_status'] === 'in_transit'): ?>
                                <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-bold">পথে আছে</span>
                                <?php else: ?>
                                <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold">বুকিংকৃত</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="p-3 bg-[#fbf9f4] border-t border-[#ece4d6] text-right">
                <a href="<?= $baseUrl ?>/manager/distributions" class="inline-flex items-center text-xs font-bold text-emerald-deep hover:text-emerald-night">
                    <i class="fas fa-plus-circle mr-1"></i> নতুন পার্সেল বুকিং করুন
                </a>
            </div>
        </div>
    </div>
</div>
