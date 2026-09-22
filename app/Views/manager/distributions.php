<?php
/**
 * Manager Book Distributions & Courier Dispatches (বই ও কুরআন বিতরণ এবং কুরিয়ার চালান ট্র্যাকিং)
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
                <span class="text-xs text-slate-500">বই বিতরণ ও কুরিয়ার চালান</span>
            </div>
            <h1 class="text-3xl font-extrabold text-emerald-night mt-1">বই ও কুরআন চালান এবং কুরিয়ার ট্র্যাকিং</h1>
            <p class="text-sm text-slate-600">
                সারাদেশের জেলা পরিচালক ও গ্রাহকদের নিকট কারিয়ানা কুরআন, আমপারা ও কায়দা বিতরণ এবং পরিবহন ট্র্যাকিং
            </p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-2.5">
            <button type="button" onclick="document.getElementById('new-dispatch-modal').classList.remove('hidden')" 
                    class="bg-gold-rich hover:bg-gold-deep text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow transition flex items-center">
                <i class="fas fa-plus-circle mr-2"></i> নতুন পার্সেল বুকিং
            </button>
            <a href="<?= $baseUrl ?>/manager/ledger" class="bg-[#f4efe4] hover:bg-[#ede3d0] border border-[#d6ccb9] text-emerald-night px-4 py-2.5 rounded-xl text-sm font-bold transition flex items-center">
                <i class="fas fa-receipt mr-2 text-gold-deep"></i> হিসাব খাতা
            </a>
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

    <!-- Distributions Table -->
    <div class="bg-[#fffefb] rounded-2xl border border-[#e4dcce] shadow-sm overflow-hidden mb-8">
        <div class="p-5 border-b border-[#ece4d6] bg-[#fbf9f4] flex items-center justify-between">
            <h3 class="font-bold text-emerald-night text-lg flex items-center">
                <i class="fas fa-truck-ramp-box text-gold-rich mr-2"></i> বিতরণ ও চালান তালিকা (<?= count($distributions) ?> টি)
            </h3>
            <span class="text-xs text-slate-500 font-medium">সুন্দরবন, এসএ পরিবহন, রেডএক্স চালান রেজিস্টার</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="bg-[#f4efe4] border-b border-[#e5ddcd] text-xs font-extrabold text-emerald-night uppercase tracking-wider">
                        <th class="py-3.5 px-4">তারিখ</th>
                        <th class="py-3.5 px-4">প্রাপক</th>
                        <th class="py-3.5 px-4">বইয়ের শিরোনাম</th>
                        <th class="py-3.5 px-4 text-center">সংখ্যা</th>
                        <th class="py-3.5 px-4">মূল্য ও পেমেন্ট</th>
                        <th class="py-3.5 px-4">কুরিয়ার ও কনসাইনমেন্ট</th>
                        <th class="py-3.5 px-4 text-right">ডেলিভারি স্ট্যাটাস</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#ede5d6]">
                    <?php if (empty($distributions)): ?>
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">
                            কোনো বই বিতরণের রেকর্ড পাওয়া যায়নি
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($distributions as $d): ?>
                    <tr class="hover:bg-[#faf7f0] transition">
                        <td class="py-3 px-4 text-xs text-slate-500 font-mono whitespace-nowrap">
                            <?= date('d M, Y', strtotime($d['created_at'])) ?>
                        </td>
                        <td class="py-3 px-4">
                            <?php if ($d['recipient_type'] === 'director'): ?>
                            <div class="flex items-center space-x-2">
                                <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center text-[10px] font-bold shrink-0">প</span>
                                <div>
                                    <h5 class="font-bold text-slate-900 text-xs leading-snug">
                                        <?= htmlspecialchars($d['director_name'] ?? 'জেলা পরিচালক') ?>
                                    </h5>
                                    <span class="text-[11px] text-gold-deep font-semibold block">
                                        <?= htmlspecialchars($d['district_name'] ?? '') ?>
                                    </span>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="flex items-center space-x-2">
                                <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-800 flex items-center justify-center text-[10px] font-bold shrink-0">গ্রা</span>
                                <div>
                                    <h5 class="font-bold text-slate-900 text-xs leading-snug">
                                        <?= htmlspecialchars($d['customer_name'] ?? 'সাধারণ গ্রাহক') ?>
                                    </h5>
                                    <span class="text-[11px] text-slate-500 block">
                                        <?= htmlspecialchars($d['customer_phone'] ?? '') ?>
                                    </span>
                                </div>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4 font-bold text-slate-800 text-xs">
                            <?= htmlspecialchars($d['book_title'] ?? 'কারিয়ানা প্রকাশনা') ?>
                        </td>
                        <td class="py-3 px-4 text-center font-mono font-bold text-emerald-800 text-sm whitespace-nowrap">
                            <?= \Core\BengaliHelper::toBengaliNumber($d['quantity']) ?> কপি
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            <div class="font-mono text-xs">
                                <span class="font-bold text-slate-900">৳<?= \Core\BengaliHelper::toBengaliNumber(number_format((float)$d['total_amount'], 0)) ?></span>
                            </div>
                            <?php if ($d['payment_status'] === 'paid'): ?>
                            <span class="text-[10px] text-emerald-700 font-bold bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">পরিশোধিত</span>
                            <?php elseif ($d['payment_status'] === 'due'): ?>
                            <span class="text-[10px] text-red-600 font-bold bg-red-50 px-1.5 py-0.5 rounded border border-red-200">বকেয়া</span>
                            <?php else: ?>
                            <span class="text-[10px] text-amber-700 font-bold bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200"><?= htmlspecialchars($d['payment_status']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            <span class="font-semibold text-slate-800 text-xs block"><?= htmlspecialchars($d['courier_name'] ?? 'কুরিয়ার') ?></span>
                            <?php if (!empty($d['tracking_number'])): ?>
                            <span class="font-mono text-[11px] text-emerald-deep font-bold bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">
                                <?= htmlspecialchars($d['tracking_number']) ?>
                            </span>
                            <?php else: ?>
                            <span class="text-slate-400 text-[11px]">ট্র্যাকিং বিহীন</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <?php if ($d['delivery_status'] === 'delivered'): ?>
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <i class="fas fa-circle-check mr-1 text-[10px]"></i> ডেলিভার্ড
                            </span>
                            <?php elseif ($d['delivery_status'] === 'in_transit'): ?>
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                                <i class="fas fa-truck mr-1 text-[10px]"></i> পথে আছে
                            </span>
                            <?php else: ?>
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                                <i class="fas fa-box mr-1 text-[10px]"></i> বুকিংকৃত
                            </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- New Dispatch Booking Modal -->
    <div id="new-dispatch-modal" class="hidden fixed inset-0 z-[60] overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-[#fffefb] border-2 border-gold-rich/50 rounded-3xl max-w-lg w-full p-6 md:p-8 shadow-2xl relative">
            <div class="flex items-center justify-between pb-4 border-b border-[#ede4d4] mb-5">
                <div class="flex items-center space-x-2">
                    <div class="w-9 h-9 bg-gold-rich text-white rounded-lg flex items-center justify-center text-lg">
                        <i class="fas fa-truck-fast"></i>
                    </div>
                    <h3 class="font-bold text-lg text-emerald-night">নতুন বই চালান ও পার্সেল বুকিং</h3>
                </div>
                <button type="button" onclick="document.getElementById('new-dispatch-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="<?= $baseUrl ?>/manager/distributions" method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">প্রাপকের ধরন</label>
                    <select name="recipient_type" id="rec_type_select" onchange="toggleRecipientField(this.value)" required class="w-full px-3 py-2 border border-[#d6ccb9] rounded-xl text-sm font-semibold bg-[#fdfbf7] focus:ring-2 focus:ring-emerald-deep focus:outline-none">
                        <option value="director">জেলা পরিচালক (District Director)</option>
                        <option value="general_customer">সাধারণ গ্রাহক / মাদ্রাসা (Customer)</option>
                    </select>
                </div>

                <div id="director_field_wrap">
                    <label class="block text-xs font-bold text-slate-700 mb-1">জেলা পরিচালক নির্বাচন করুন</label>
                    <select name="director_id" class="w-full px-3 py-2 border border-[#d6ccb9] rounded-xl text-sm bg-[#fdfbf7] focus:ring-2 focus:ring-emerald-deep focus:outline-none">
                        <option value="">-- পরিচালক বাছাই করুন --</option>
                        <?php foreach ($directors as $dir): ?>
                        <option value="<?= $dir['id'] ?>"><?= htmlspecialchars($dir['name']) ?> (<?= htmlspecialchars($dir['district_name']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="customer_field_wrap" class="hidden grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">গ্রাহকের নাম</label>
                        <input type="text" name="customer_name" placeholder="নাম..." class="w-full px-3 py-2 border border-[#d6ccb9] rounded-xl text-sm bg-[#fdfbf7]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">মোবাইল নম্বর</label>
                        <input type="text" name="customer_phone" placeholder="017..." class="w-full px-3 py-2 border border-[#d6ccb9] rounded-xl text-sm bg-[#fdfbf7]">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">বই / প্রকাশনা</label>
                        <select name="book_id" required class="w-full px-3 py-2 border border-[#d6ccb9] rounded-xl text-xs font-semibold bg-[#fdfbf7] focus:ring-2 focus:ring-emerald-deep focus:outline-none">
                            <?php foreach ($books as $bk): ?>
                            <option value="<?= $bk['id'] ?>"><?= htmlspecialchars($bk['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">সংখ্যা (কপি)</label>
                        <input type="number" min="1" name="quantity" value="10" required 
                               class="w-full px-3 py-2 border border-[#d6ccb9] rounded-xl text-sm font-bold bg-[#fdfbf7] focus:ring-2 focus:ring-emerald-deep focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">কুরিয়ার সার্ভিস</label>
                        <select name="courier_name" required class="w-full px-3 py-2 border border-[#d6ccb9] rounded-xl text-sm bg-[#fdfbf7] focus:ring-2 focus:ring-emerald-deep focus:outline-none">
                            <option value="সুন্দরবন কুরিয়ার">সুন্দরবন কুরিয়ার</option>
                            <option value="এসএ পরিবহন">এসএ পরিবহন</option>
                            <option value="রেডএক্স (RedX)">রেডএক্স (RedX)</option>
                            <option value="পাঠাও কুরিয়ার">পাঠাও কুরিয়ার</option>
                            <option value="সরাসরি অফিস ডেলিভারি">সরাসরি অফিস ডেলিভারি</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">ট্র্যাকিং / বুকিং স্লিপ নম্বর</label>
                        <input type="text" name="tracking_number" placeholder="যেমন: SBN-99214" 
                               class="w-full px-3 py-2 border border-[#d6ccb9] rounded-xl text-sm font-mono bg-[#fdfbf7] focus:ring-2 focus:ring-emerald-deep focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">পেমেন্ট অবস্থা</label>
                        <select name="payment_status" required class="w-full px-3 py-2 border border-[#d6ccb9] rounded-xl text-sm bg-[#fdfbf7] focus:ring-2 focus:ring-emerald-deep focus:outline-none">
                            <option value="paid">পরিশোধিত (Paid)</option>
                            <option value="due">বকেয়া / ক্যাশ অন ডেলিভারি (Due)</option>
                            <option value="partial">আংশিক পরিশোধ</option>
                            <option value="waived">ওয়াকফ / বিনামূল্যে প্রদান</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">ডেলিভারি অবস্থা</label>
                        <select name="delivery_status" required class="w-full px-3 py-2 border border-[#d6ccb9] rounded-xl text-sm bg-[#fdfbf7] focus:ring-2 focus:ring-emerald-deep focus:outline-none">
                            <option value="dispatched">কুরিয়ারে বুকিং সম্পন্ন (Dispatched)</option>
                            <option value="in_transit">পথে রয়েছে (In Transit)</option>
                            <option value="delivered">হস্তান্তরিত হয়েছে (Delivered)</option>
                            <option value="pending">অপেক্ষমাণ (Pending)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">মন্তব্য ও প্যাকিং নোট (ঐচ্ছিক)</label>
                    <input type="text" name="notes" placeholder="যেমন: মাগুরা সদরে পৌঁছালে ফোন করবে..." 
                           class="w-full px-3 py-2 border border-[#d6ccb9] rounded-xl text-sm bg-[#fdfbf7] focus:ring-2 focus:ring-emerald-deep focus:outline-none">
                </div>

                <div class="pt-3 border-t border-[#ede4d4] flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('new-dispatch-modal').classList.add('hidden')" 
                            class="px-5 py-2.5 border border-slate-300 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100">
                        বাতিল
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-gold-rich hover:bg-gold-deep text-white rounded-xl text-xs font-bold shadow-md transition flex items-center">
                        <i class="fas fa-paper-plane mr-1.5"></i> চালান বুকিং করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleRecipientField(val) {
    var dirWrap = document.getElementById('director_field_wrap');
    var custWrap = document.getElementById('customer_field_wrap');
    if (val === 'director') {
        dirWrap.classList.remove('hidden');
        custWrap.classList.add('hidden');
    } else {
        dirWrap.classList.add('hidden');
        custWrap.classList.remove('hidden');
    }
}
</script>
