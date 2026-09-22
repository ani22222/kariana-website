<?php
/**
 * Teacher Dashboard View - Kariana Quran
 */
$csrfToken = \Core\Csrf::getToken();
?>
<div class="container mx-auto px-4 py-8">
    <!-- Top Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between pb-6 border-b border-[#e5ddcb] mb-8">
        <div>
            <div class="inline-flex items-center px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-full text-xs font-bold mb-2">
                <i class="fas fa-chalkboard-user mr-1.5 text-gold-deep"></i> কারিয়ানা মুয়াল্লিম পোর্টাল
            </div>
            <h1 class="text-3xl font-extrabold text-emerald-night">শিক্ষক ড্যাশবোর্ড</h1>
            <p class="text-sm text-slate-600 mt-0.5">
                স্বাগতম, <strong><?= htmlspecialchars($teacher['name']) ?></strong> | এলাকা/মাদ্রাসা: <?= htmlspecialchars($teacher['area_name']) ?>
            </p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-2.5">
            <a href="tel:<?= htmlspecialchars($teacher['director_phone']) ?>" class="bg-emerald-deep hover:bg-emerald-night text-white px-4 py-2.5 rounded-xl text-xs md:text-sm font-bold shadow transition flex items-center">
                <i class="fas fa-phone mr-2 text-gold-shimmer"></i> জেলা পরিচালককে কল করুন
            </a>
            <a href="<?= $baseUrl ?>/teacher/logout" class="bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 px-3.5 py-2.5 rounded-xl text-xs md:text-sm font-bold transition flex items-center">
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
    <?php if ($error = \Core\Session::flash('error')): ?>
    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-300 text-red-900 font-bold flex items-center shadow-sm">
        <i class="fas fa-exclamation-circle text-xl text-red-600 mr-3"></i> <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <!-- Assigned District Director Card -->
    <div class="bg-[#f7f3e8] border-2 border-gold-rich/50 rounded-2xl p-6 mb-8 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 rounded-2xl bg-emerald-deep text-gold-shimmer flex items-center justify-center text-2xl font-bold border-2 border-gold-rich/40 shadow-inner shrink-0">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div>
                    <span class="text-xs text-gold-deep font-bold uppercase tracking-wider block">আপনার দায়িত্বপ্রাপ্ত জেলা পরিচালক</span>
                    <h3 class="text-xl font-extrabold text-emerald-night">
                        <?= htmlspecialchars($teacher['director_name']) ?>
                    </h3>
                    <p class="text-xs text-slate-600 mt-0.5">
                        <i class="fas fa-map-marker-alt text-emerald-vibrant mr-1"></i> জেলা: <strong><?= htmlspecialchars($teacher['district_name']) ?></strong> | পদবী: <?= htmlspecialchars($teacher['director_designation']) ?>
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="tel:<?= htmlspecialchars($teacher['director_phone']) ?>" class="bg-white hover:bg-slate-50 border border-[#d6ccb9] text-emerald-night px-4 py-2 rounded-xl text-xs font-bold shadow-sm transition flex items-center">
                    <i class="fas fa-phone-alt text-emerald-vibrant mr-2"></i> <?= htmlspecialchars($teacher['director_phone']) ?>
                </a>
                <?php if (!empty($teacher['director_whatsapp'])): ?>
                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $teacher['director_whatsapp']) ?>" target="_blank" class="bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-sm transition flex items-center">
                    <i class="fab fa-whatsapp mr-1.5 text-base"></i> হোয়াটসঅ্যাপ
                </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-4 pt-3 border-t border-[#e8deca] text-xs text-slate-700 leading-relaxed">
            <i class="fas fa-info-circle text-gold-deep mr-1"></i> <strong>ক্বারীয়ানা সাংগঠনিক নীতি:</strong> সম্মানিত শিক্ষক মহোদয় যেকোনো বই, কায়দা বা প্রকাশনার জন্য সরাসরি নিজ জেলা পরিচালকের নিকট আবেদন করবেন। শিক্ষার্থীরা কুরআন মাজীদ খতম বা নাজেরা সম্পন্ন করলে পরিচালকের উপস্থিতিতে <strong>সবক ক্লাস (Sabak Class)</strong> ও দোয়া মাহফিলের তারিখ নির্ধারণ করবেন।
        </div>
    </div>

    <!-- 3 Action Forms Grid: 1. Sabak Class, 2. Book Order, 3. Problem Escalation -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- 1. Sabak Class Booking -->
        <div class="bg-[#fffefb] rounded-2xl border-2 border-emerald-600/40 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-lg mb-3">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h3 class="font-bold text-lg text-emerald-night mb-1">সবক ক্লাস শিডিউল আবেদন</h3>
                <p class="text-xs text-slate-500 mb-4">
                    শিক্ষার্থীরা কুরআন সম্পন্ন করলে জেলা পরিচালকের উপস্থিতিতে সমাপনী সবক ক্লাসের তারিখ আবেদন করুন।
                </p>

                <form action="<?= $baseUrl ?>/teacher/activity" method="POST" class="space-y-3">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    <input type="hidden" name="activity_type" value="sabak_class">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">সমাপনী ক্লাসের বিষয়</label>
                        <input type="text" name="title" value="কুরআন সমাপ্তি ও চূড়ান্ত সবক ক্লাস আবেদন" required class="w-full px-3 py-2 border border-[#d6ccb9] rounded-lg text-xs bg-[#fdfbf7]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">সমাপ্তকারী শিক্ষার্থীর সংখ্যা</label>
                        <input type="number" min="1" name="quantity" value="20" required class="w-full px-3 py-2 border border-[#d6ccb9] rounded-lg text-xs font-bold bg-[#fdfbf7]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">প্রস্তাবিত তারিখ</label>
                        <input type="date" name="preferred_date" value="<?= date('Y-m-d', strtotime('+7 days')) ?>" required class="w-full px-3 py-2 border border-[#d6ccb9] rounded-lg text-xs bg-[#fdfbf7]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">বিস্তারিত বিবরণ ও স্থান</label>
                        <textarea name="details" rows="2" required placeholder="স্থান ও সময়ের প্রস্তাবনা..." class="w-full px-3 py-2 border border-[#d6ccb9] rounded-lg text-xs bg-[#fdfbf7]"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-emerald-deep hover:bg-emerald-night text-white font-bold py-2.5 rounded-xl text-xs shadow transition flex items-center justify-center">
                        <i class="fas fa-calendar-check mr-1.5 text-gold-shimmer"></i> সবক ক্লাসের তারিখ পাঠান
                    </button>
                </form>
            </div>
        </div>

        <!-- 2. Book Order Requisition to District Director -->
        <div class="bg-[#fffefb] rounded-2xl border-2 border-gold-rich/40 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-gold-deep flex items-center justify-center text-lg mb-3">
                    <i class="fas fa-book-bookmark"></i>
                </div>
                <h3 class="font-bold text-lg text-emerald-night mb-1">পরিচালকের নিকট বই অর্ডার</h3>
                <p class="text-xs text-slate-500 mb-4">
                    নতুন ব্যাচ বা ক্লাসের শিক্ষার্থীদের জন্য প্রয়োজনীয় কারিয়ানা কায়দা, আমপারা ও কুরআন অর্ডার করুন।
                </p>

                <form action="<?= $baseUrl ?>/teacher/activity" method="POST" class="space-y-3">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    <input type="hidden" name="activity_type" value="book_order">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">অর্ডারের শিরোনাম</label>
                        <input type="text" name="title" value="নতুন ব্যাচের শিক্ষার্থীদের জন্য বই প্রয়োজন" required class="w-full px-3 py-2 border border-[#d6ccb9] rounded-lg text-xs bg-[#fdfbf7]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">বই নির্বাচন করুন</label>
                        <select name="details" required class="w-full px-3 py-2 border border-[#d6ccb9] rounded-lg text-xs bg-[#fdfbf7]">
                            <?php foreach ($books as $b): ?>
                            <option value="<?= htmlspecialchars($b['title']) ?>"><?= htmlspecialchars($b['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">প্রয়োজনীয় সংখ্যা (কপি)</label>
                        <input type="number" min="1" name="quantity" value="25" required class="w-full px-3 py-2 border border-[#d6ccb9] rounded-lg text-xs font-bold bg-[#fdfbf7]">
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full bg-gold-rich hover:bg-gold-deep text-white font-bold py-2.5 rounded-xl text-xs shadow transition flex items-center justify-center">
                            <i class="fas fa-cart-shopping mr-1.5"></i> পরিচালকের কাছে অর্ডার পাঠান
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 3. Problem Escalation to District Director -->
        <div class="bg-[#fffefb] rounded-2xl border-2 border-slate-300 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-lg mb-3">
                    <i class="fas fa-circle-question"></i>
                </div>
                <h3 class="font-bold text-lg text-emerald-night mb-1">শিক্ষাদান ও স্থানীয় সমস্যা রিপোর্ট</h3>
                <p class="text-xs text-slate-500 mb-4">
                    মাখরাজ উচ্চারণ, স্থানীয় মাদ্রাসার সমস্যা বা ক্লাসরুম ব্যবস্থাপনায় পরিচালকের পরামর্শ চান।
                </p>

                <form action="<?= $baseUrl ?>/teacher/activity" method="POST" class="space-y-3">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    <input type="hidden" name="activity_type" value="problem_report">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">সমস্যার বিষয়</label>
                        <input type="text" name="title" placeholder="যেমন: মাখরাজ দুর্বল শিক্ষার্থীদের বিশেষ কৌশল..." required class="w-full px-3 py-2 border border-[#d6ccb9] rounded-lg text-xs bg-[#fdfbf7]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">বিস্তারিত বিবরণ</label>
                        <textarea name="details" rows="4" required placeholder="সমস্যার বিস্তারিত লিখুন যাতে জেলা পরিচালক প্রয়োজনীয় দিকনির্দেশনা দিতে পারেন..." class="w-full px-3 py-2 border border-[#d6ccb9] rounded-lg text-xs bg-[#fdfbf7]"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-2.5 rounded-xl text-xs shadow transition flex items-center justify-center">
                        <i class="fas fa-paper-plane mr-1.5"></i> পরামর্শ চেয়ে পাঠান
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Activities & Submissions History Table -->
    <div class="bg-[#fffefb] rounded-2xl border border-[#e4dcce] shadow-sm overflow-hidden mb-8">
        <div class="p-5 border-b border-[#ece4d6] bg-[#fbf9f4] flex items-center justify-between">
            <h3 class="font-bold text-emerald-night text-base flex items-center">
                <i class="fas fa-clock-rotate-left text-gold-rich mr-2"></i> প্রেরিত আবেদন ও জেলা পরিচালকের সিদ্ধান্তের ইতিহাস
            </h3>
            <span class="text-xs text-slate-500">মোট আবেদন: <?= count($activities) ?> টি</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-[#f4efe4] border-b border-[#e5ddcd] text-slate-600 font-extrabold uppercase">
                        <th class="py-3 px-4">তারিখ</th>
                        <th class="py-3 px-4">ধরন</th>
                        <th class="py-3 px-4">শিরোনাম ও বিবরণ</th>
                        <th class="py-3 px-4">সংখ্যা / প্রস্তাবিত তারিখ</th>
                        <th class="py-3 px-4">জেলা পরিচালকের মন্তব্য</th>
                        <th class="py-3 px-4 text-right">অবস্থা (Status)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($activities)): ?>
                    <tr>
                        <td colspan="6" class="py-6 text-center text-slate-400">
                            এখনো কোনো আবেদন পাঠানো হয়নি
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($activities as $act): ?>
                    <tr class="hover:bg-slate-50">
                        <td class="py-3 px-4 text-slate-500 font-mono whitespace-nowrap">
                            <?= date('d M, Y', strtotime($act['created_at'])) ?>
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            <?php if ($act['activity_type'] === 'sabak_class'): ?>
                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">সবক ক্লাস</span>
                            <?php elseif ($act['activity_type'] === 'book_order'): ?>
                            <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold text-[10px]">বই অর্ডার</span>
                            <?php else: ?>
                            <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 font-bold text-[10px]">পরামর্শ / রিপোর্ট</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4">
                            <h5 class="font-bold text-slate-900 text-xs"><?= htmlspecialchars($act['title']) ?></h5>
                            <p class="text-slate-500 text-[11px] line-clamp-2 mt-0.5"><?= htmlspecialchars($act['details']) ?></p>
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap font-mono text-slate-700">
                            <?php if (!empty($act['quantity'])): ?>
                            <span class="font-bold"><?= \Core\BengaliHelper::toBengaliNumber($act['quantity']) ?> টি/জন</span>
                            <?php endif; ?>
                            <?php if (!empty($act['preferred_date'])): ?>
                            <span class="block text-[11px] text-slate-500">তারিখ: <?= htmlspecialchars($act['preferred_date']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4 text-slate-600 text-xs">
                            <?= !empty($act['director_notes']) ? htmlspecialchars($act['director_notes']) : '<span class="text-slate-400">অপেক্ষমাণ</span>' ?>
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <?php if ($act['status'] === 'approved' || $act['status'] === 'scheduled'): ?>
                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">অনুমোদিত</span>
                            <?php elseif ($act['status'] === 'completed'): ?>
                            <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 font-bold text-[10px]">সম্পন্ন</span>
                            <?php else: ?>
                            <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold text-[10px]">বিবেচনাধীন</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
