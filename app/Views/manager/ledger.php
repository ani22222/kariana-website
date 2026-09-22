<?php
/**
 * Manager Accounts Ledger (আয়-ব্যয় হিসাব খাতা)
 * Kariana Quran Islamic Educational Portal & CMS
 */
$csrfToken = \Core\Csrf::getToken();
?>
<div class="container mx-auto px-4 py-8">
    <!-- Top Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between pb-6 border-b border-[#e5ddcb] mb-8">
        <div>
            <div class="flex items-center space-x-2">
                <a href="<?= $baseUrl ?>/manager/dashboard" class="text-xs text-emerald-deep font-bold hover:underline">
                    <i class="fas fa-arrow-left mr-1"></i> ড্যাশবোর্ড
                </a>
                <span class="text-xs text-slate-400">/</span>
                <span class="text-xs text-slate-500">হিসাব খাতা</span>
            </div>
            <h1 class="text-3xl font-extrabold text-emerald-night mt-1">আয়-ব্যয় হিসাব খাতা ও ভাউচার</h1>
            <p class="text-sm text-slate-600">
                কেন্দ্রীয় তহবিল, প্রকাশনা বিক্রি, ওয়াকফ অনুদান ও পরিবহন খরচের সমন্বিত হিসাব খাতা
            </p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-2.5">
            <button type="button" onclick="document.getElementById('new-voucher-modal').classList.remove('hidden')" 
                    class="bg-emerald-deep hover:bg-emerald-night text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow transition flex items-center">
                <i class="fas fa-plus-circle mr-2 text-gold-shimmer"></i> নতুন ভাউচার যোগ করুন
            </button>
            <button type="button" onclick="window.print()" class="bg-[#f4efe4] hover:bg-[#ede3d0] border border-[#d6ccb9] text-emerald-night px-4 py-2.5 rounded-xl text-sm font-bold transition flex items-center">
                <i class="fas fa-print mr-2"></i> প্রিন্ট রিপোর্ট
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if ($success = \Core\Session::flash('success')): ?>
    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-900 font-bold flex items-center shadow-sm">
        <i class="fas fa-check-circle text-xl text-emerald-600 mr-3"></i> <?= htmlspecialchars($success) ?>
    </div>
    <?php endif; ?>
    <?php if ($error = \Core\Session::flash('error')): ?>
    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-300 text-red-900 font-bold flex items-center shadow-sm">
        <i class="fas fa-exclamation-circle text-xl text-red-600 mr-3"></i> <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <!-- Summary Bar -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-[#fffefb] p-5 rounded-2xl border-2 border-emerald-500/40 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-500 font-bold uppercase block">সর্বমোট প্রাপ্তি / আয়</span>
                <h3 class="text-2xl font-black text-emerald-800 font-mono mt-1">
                    ৳<?= \Core\BengaliHelper::toBengaliNumber(number_format($totalIncome, 2)) ?>
                </h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-xl">
                <i class="fas fa-arrow-trend-up"></i>
            </div>
        </div>

        <div class="bg-[#fffefb] p-5 rounded-2xl border-2 border-red-500/30 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-500 font-bold uppercase block">সর্বমোট খরচ / ব্যয়</span>
                <h3 class="text-2xl font-black text-red-700 font-mono mt-1">
                    ৳<?= \Core\BengaliHelper::toBengaliNumber(number_format($totalExpense, 2)) ?>
                </h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-red-100 text-red-700 flex items-center justify-center text-xl">
                <i class="fas fa-arrow-trend-down"></i>
            </div>
        </div>

        <div class="bg-[#fffefb] p-5 rounded-2xl border-2 border-gold-rich/50 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-gold-deep font-bold uppercase block">বর্তমান তহবিল স্থিতি (Balance)</span>
                <h3 class="text-2xl font-black text-emerald-night font-mono mt-1">
                    ৳<?= \Core\BengaliHelper::toBengaliNumber(number_format($balance, 2)) ?>
                </h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-gold-deep flex items-center justify-center text-xl">
                <i class="fas fa-vault"></i>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-[#fffefb] p-4 rounded-2xl border border-[#e4dcce] shadow-sm mb-6 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center space-x-2">
            <span class="text-xs font-bold text-slate-600">ধরন ফিল্টার:</span>
            <a href="<?= $baseUrl ?>/manager/ledger?type=all" class="px-3 py-1 rounded-lg text-xs font-bold <?= $filterType === 'all' ? 'bg-emerald-deep text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?>">
                সকল
            </a>
            <a href="<?= $baseUrl ?>/manager/ledger?type=income" class="px-3 py-1 rounded-lg text-xs font-bold <?= $filterType === 'income' ? 'bg-emerald-deep text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?>">
                শুধুমাত্র আয়
            </a>
            <a href="<?= $baseUrl ?>/manager/ledger?type=expense" class="px-3 py-1 rounded-lg text-xs font-bold <?= $filterType === 'expense' ? 'bg-emerald-deep text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?>">
                শুধুমাত্র ব্যয়
            </a>
        </div>
        <div class="text-xs text-slate-400">
            সর্বমোট ভাউচার রেকর্ড: <?= count($records) ?> টি
        </div>
    </div>

    <!-- Ledger Table -->
    <div class="bg-[#fffefb] rounded-2xl border border-[#e4dcce] shadow-sm overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="bg-[#f4efe4] border-b border-[#e5ddcd] text-xs font-extrabold text-emerald-night uppercase tracking-wider">
                        <th class="py-3.5 px-4">ভাউচার নম্বর</th>
                        <th class="py-3.5 px-4">তারিখ</th>
                        <th class="py-3.5 px-4">ধরন</th>
                        <th class="py-3.5 px-4">খাত (Category)</th>
                        <th class="py-3.5 px-4">বিবরণ / সূত্র</th>
                        <th class="py-3.5 px-4">সংশ্লিষ্ট পরিচালক</th>
                        <th class="py-3.5 px-4 text-right">টাকা (পরিমাণ)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#ede5d6]">
                    <?php if (empty($records)): ?>
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">
                            কোনো হিসাব রেকর্ড পাওয়া যায়নি
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($records as $r): ?>
                    <tr class="hover:bg-[#faf7f0] transition">
                        <td class="py-3 px-4 font-mono font-bold text-slate-700 whitespace-nowrap">
                            <span class="px-2 py-0.5 bg-slate-100 rounded text-xs border border-slate-200">
                                <?= htmlspecialchars($r['voucher_no'] ?? '-') ?>
                            </span>
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap text-slate-600 font-mono text-xs">
                            <?= htmlspecialchars($r['transaction_date']) ?>
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            <?php if ($r['type'] === 'income'): ?>
                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold">
                                <i class="fas fa-plus text-[10px] mr-1"></i> আয়
                            </span>
                            <?php else: ?>
                            <span class="px-2.5 py-0.5 rounded-full bg-red-100 text-red-800 text-xs font-bold">
                                <i class="fas fa-minus text-[10px] mr-1"></i> ব্যয়
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4 font-bold text-slate-800 whitespace-nowrap">
                            <?= htmlspecialchars($r['category']) ?>
                        </td>
                        <td class="py-3 px-4 text-slate-600 text-xs max-w-xs">
                            <?= htmlspecialchars($r['description']) ?>
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap text-xs">
                            <?php if (!empty($r['director_name'])): ?>
                            <span class="font-bold text-emerald-night"><?= htmlspecialchars($r['director_name']) ?></span>
                            <span class="text-slate-400 block text-[11px]">(<?= htmlspecialchars($r['district_name']) ?>)</span>
                            <?php else: ?>
                            <span class="text-slate-400">সরাসরি কেন্দ্রীয়</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-bold whitespace-nowrap">
                            <?php if ($r['type'] === 'income'): ?>
                            <span class="text-emerald-700 text-base">+৳<?= \Core\BengaliHelper::toBengaliNumber(number_format((float)$r['amount'], 2)) ?></span>
                            <?php else: ?>
                            <span class="text-red-600 text-base">-৳<?= \Core\BengaliHelper::toBengaliNumber(number_format((float)$r['amount'], 2)) ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- New Voucher Modal -->
    <div id="new-voucher-modal" class="hidden fixed inset-0 z-[60] overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-[#fffefb] border-2 border-gold-rich/50 rounded-3xl max-w-lg w-full p-6 md:p-8 shadow-2xl relative">
            <div class="flex items-center justify-between pb-4 border-b border-[#ede4d4] mb-5">
                <div class="flex items-center space-x-2">
                    <div class="w-9 h-9 bg-emerald-deep text-gold-shimmer rounded-lg flex items-center justify-center text-lg">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <h3 class="font-bold text-lg text-emerald-night">নতুন আয় / ব্যয় ভাউচার এন্ট্রি</h3>
                </div>
                <button type="button" onclick="document.getElementById('new-voucher-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="<?= $baseUrl ?>/manager/ledger" method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">ভাউচারের ধরন</label>
                        <select name="type" required class="w-full px-3 py-2 border border-[#d6ccb9] rounded-xl text-sm font-bold bg-[#fdfbf7] focus:ring-2 focus:ring-emerald-deep focus:outline-none">
                            <option value="income">প্রাপ্তি / আয় (Income)</option>
                            <option value="expense">পরিশোধ / ব্যয় (Expense)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">ভাউচার নম্বর</label>
                        <input type="text" name="voucher_no" value="VCH-<?= date('Ymd-His') ?>" required 
                               class="w-full px-3 py-2 border border-[#d6ccb9] rounded-xl text-sm font-mono bg-[#fdfbf7] focus:ring-2 focus:ring-emerald-deep focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">খাত (Category)</label>
                        <select name="category" required class="w-full px-3 py-2 border border-[#d6ccb9] rounded-xl text-sm font-semibold bg-[#fdfbf7] focus:ring-2 focus:ring-emerald-deep focus:outline-none">
                            <option value="বই বিক্রি">বই বিক্রি</option>
                            <option value="কুরআন বিতরণ অনুদান">কুরআন বিতরণ অনুদান</option>
                            <option value="কুরিয়ার পরিবহন খরচ">কুরিয়ার পরিবহন খরচ</option>
                            <option value="মুদ্রণ ও প্রিন্টিং বিল">মুদ্রণ ও প্রিন্টিং বিল</option>
                            <option value="অফিস ও পরিচালন ব্যয়">অফিস ও পরিচালন ব্যয়</option>
                            <option value="মুয়াল্লিম প্রশিক্ষণ সম্মানী">মুয়াল্লিম প্রশিক্ষণ সম্মানী</option>
                            <option value="বিবিধ">বিবিধ</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">টাকার পরিমাণ (৳)</label>
                        <input type="number" step="0.01" name="amount" required placeholder="0.00" 
                               class="w-full px-3 py-2 border border-[#d6ccb9] rounded-xl text-sm font-mono font-bold bg-[#fdfbf7] focus:ring-2 focus:ring-emerald-deep focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">লেনদেনের তারিখ</label>
                        <input type="date" name="transaction_date" value="<?= date('Y-m-d') ?>" required 
                               class="w-full px-3 py-2 border border-[#d6ccb9] rounded-xl text-sm bg-[#fdfbf7] focus:ring-2 focus:ring-emerald-deep focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">সংশ্লিষ্ট জেলা পরিচালক (ঐচ্ছিক)</label>
                        <select name="related_director_id" class="w-full px-3 py-2 border border-[#d6ccb9] rounded-xl text-xs bg-[#fdfbf7] focus:ring-2 focus:ring-emerald-deep focus:outline-none">
                            <option value="">-- প্রযোজ্য নয় --</option>
                            <?php foreach ($directors as $d): ?>
                            <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?> (<?= htmlspecialchars($d['district_name']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">বিস্তারিত বিবরণ ও তথ্যসূত্র</label>
                    <textarea name="description" rows="3" required placeholder="ভাউচারের বিবরণ বিস্তারিত লিখুন..." 
                              class="w-full px-3 py-2 border border-[#d6ccb9] rounded-xl text-sm bg-[#fdfbf7] focus:ring-2 focus:ring-emerald-deep focus:outline-none"></textarea>
                </div>

                <div class="pt-3 border-t border-[#ede4d4] flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('new-voucher-modal').classList.add('hidden')" 
                            class="px-5 py-2.5 border border-slate-300 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">
                        বাতিল
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-emerald-deep hover:bg-emerald-night text-white rounded-xl text-xs font-bold shadow-md transition flex items-center">
                        <i class="fas fa-check mr-1.5 text-gold-shimmer"></i> ভাউচার সংরক্ষণ করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
