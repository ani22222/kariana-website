<?php
/**
 * Dedicated Teacher / Muallim Dashboard View - Kariana Quran
 * Re-Architected as an Ultra-Simple, Zero-Confusion Mobile App UI
 */
$csrfToken = \Core\Csrf::getToken();
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';
$success = \Core\Session::flash('success');
$error = \Core\Session::flash('error');
?>
<div class="container mx-auto px-3 sm:px-4 py-4 sm:py-6 max-w-4xl" x-data="{ activeTab: 'sabak', showStudentModal: false }">

    <!-- Teacher App Header Card -->
    <div class="bg-gradient-to-br from-emerald-night via-emerald-deep to-emerald-night rounded-3xl p-5 sm:p-7 text-white shadow-xl mb-6 border border-gold-rich/40 relative overflow-hidden">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 relative z-10">
            <div class="flex flex-col sm:flex-row items-center gap-4 text-center sm:text-left">
                <!-- Avatar -->
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-tr from-gold-rich to-amber-300 text-emerald-night flex items-center justify-center text-3xl font-extrabold shadow-lg border-2 border-white/40 shrink-0">
                    <i class="fas fa-chalkboard-user"></i>
                </div>

                <div>
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-1.5 mb-1.5">
                        <span class="px-2.5 py-0.5 bg-gold-rich text-white text-[11px] font-black rounded-full shadow-sm">
                            মুয়াল্লিমুল কুরআন
                        </span>
                        <span class="px-2.5 py-0.5 bg-emerald-800 text-emerald-100 text-[11px] font-bold rounded-full border border-emerald-600">
                            <i class="fas fa-mosque text-amber-300 mr-1"></i> <?= htmlspecialchars($teacher['area_name']) ?>
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                        <?= htmlspecialchars($teacher['name']) ?>
                    </h1>
                    <p class="text-xs text-emerald-100/80 mt-1 flex flex-wrap items-center justify-center sm:justify-start gap-2">
                        <span><i class="fas fa-phone-alt text-amber-300 mr-1"></i> <?= htmlspecialchars($teacher['phone']) ?></span>
                        <span class="opacity-40">|</span>
                        <span><i class="fas fa-id-badge text-amber-300 mr-1"></i> আইডি: <?= htmlspecialchars($teacher['username'] ?? '') ?></span>
                    </p>
                </div>
            </div>

            <!-- Logout -->
            <a href="<?= $baseUrl ?>/teacher/logout" class="bg-red-500/20 hover:bg-red-600/40 border border-red-400/40 text-red-200 px-4 py-2 rounded-xl text-xs font-bold transition flex items-center shadow-sm">
                <i class="fas fa-sign-out-alt mr-1.5"></i> প্রস্থান
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if ($success): ?>
    <div class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 font-bold flex items-center shadow-sm text-sm">
        <i class="fas fa-check-circle text-xl text-emerald-600 mr-3 shrink-0"></i>
        <span><?= htmlspecialchars($success) ?></span>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="mb-5 p-4 rounded-2xl bg-red-50 border border-red-300 text-red-900 font-bold flex items-center shadow-sm text-sm">
        <i class="fas fa-exclamation-circle text-xl text-red-600 mr-3 shrink-0"></i>
        <span><?= htmlspecialchars($error) ?></span>
    </div>
    <?php endif; ?>

    <!-- Prominent Assigned District Director Contact Card -->
    <div class="bg-gradient-to-r from-[#fefbf3] to-[#f7f2e4] border-2 border-gold-rich rounded-3xl p-5 sm:p-6 mb-6 shadow-md">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-3.5">
                <div class="w-14 h-14 rounded-2xl bg-emerald-night text-gold-shimmer flex items-center justify-center text-2xl font-bold border-2 border-gold-rich/40 shadow shrink-0">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div>
                    <span class="text-[11px] text-gold-deep font-black uppercase tracking-wider block">আপনার দায়িত্বপ্রাপ্ত জেলা পরিচালক</span>
                    <h3 class="text-lg sm:text-xl font-black text-emerald-night leading-tight">
                        <?= htmlspecialchars($teacher['director_name']) ?>
                    </h3>
                    <p class="text-xs text-slate-600 mt-0.5">
                        <i class="fas fa-map-marker-alt text-emerald-600 mr-1"></i> জেলা: <strong><?= htmlspecialchars($teacher['district_name']) ?></strong>
                    </p>
                </div>
            </div>

            <!-- 1-Tap Touch Call & WhatsApp Buttons -->
            <div class="flex items-center gap-2">
                <a href="tel:<?= htmlspecialchars($teacher['director_phone']) ?>" class="flex-1 sm:flex-none px-4 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white rounded-xl text-xs sm:text-sm font-black shadow-md transition flex items-center justify-center gap-1.5">
                    <i class="fas fa-phone-alt"></i> সরাসরি কল
                </a>
                <?php if (!empty($teacher['director_whatsapp'])): ?>
                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $teacher['director_whatsapp']) ?>" target="_blank" class="flex-1 sm:flex-none px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs sm:text-sm font-black shadow-md transition flex items-center justify-center gap-1.5">
                    <i class="fab fa-whatsapp text-base"></i> হোয়াটসঅ্যাপ
                </a>
                <?php endif; ?>
            </div>
        </div>

        <p class="mt-3 pt-3 border-t border-[#e8deca] text-[11px] sm:text-xs text-slate-600 leading-relaxed flex items-center">
            <i class="fas fa-info-circle text-gold-deep mr-1.5 shrink-0"></i>
            <span>যেকোনো কিতাব রিকুইজিশন বা সমাপনী সবক ক্লাসের শিডিউলের জন্য জেলা পরিচালকের পরামর্শ গ্রহণ করুন।</span>
        </p>
    </div>

    <!-- 2 Touch KPI Cards: Students & Requests -->
    <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-6">
        <div @click="showStudentModal = true" class="bg-[#fffefb] p-4 rounded-2xl border-2 border-emerald-600/30 hover:border-gold-rich shadow-sm cursor-pointer transition transform active:scale-95 text-center">
            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">আমার শিক্ষার্থী</p>
            <h3 class="text-2xl sm:text-3xl font-black text-emerald-night mt-0.5">
                <?= \Core\BengaliHelper::toBengaliNumber($teacher['total_students']) ?> <span class="text-xs font-bold text-slate-500">জন</span>
            </h3>
            <span class="inline-block mt-1 text-[11px] text-gold-deep font-bold hover:underline">
                <i class="fas fa-edit mr-0.5"></i> সংখ্যা পরিবর্তন করুন
            </span>
        </div>

        <div class="bg-[#fffefb] p-4 rounded-2xl border-2 border-emerald-600/30 shadow-sm text-center">
            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">মোট প্রেরিত আবেদন</p>
            <h3 class="text-2xl sm:text-3xl font-black text-gold-deep mt-0.5">
                <?= \Core\BengaliHelper::toBengaliNumber(count($activities ?? [])) ?> <span class="text-xs font-bold text-slate-500">টি</span>
            </h3>
            <span class="inline-block mt-1 text-[11px] text-slate-400 font-bold">
                কার্যক্রম হিস্ট্রি
            </span>
        </div>
    </div>

    <!-- Student Count Modal (Alpine.js) -->
    <div x-show="showStudentModal" x-cloak class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-[#fffefb] rounded-3xl border-2 border-gold-rich p-6 max-w-sm w-full shadow-2xl" @click.away="showStudentModal = false">
            <h3 class="font-black text-base text-emerald-night mb-1 flex items-center">
                <i class="fas fa-user-graduate text-gold-rich mr-2"></i> শিক্ষার্থী সংখ্যা আপডেট
            </h3>
            <p class="text-xs text-slate-500 mb-4">আপনার মাদরাসা বা মক্তবের বর্তমান শিক্ষার্থীর সংখ্যা লিখুন।</p>

            <form action="<?= $baseUrl ?>/teacher/students/update" method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">মোট শিক্ষার্থীর সংখ্যা</label>
                    <input type="number" min="0" name="total_students" value="<?= htmlspecialchars((string)$teacher['total_students']) ?>" required class="w-full px-4 py-2.5 border-2 border-[#d6ccb9] rounded-xl text-base font-black text-center bg-[#fdfbf7] outline-none focus:border-gold-rich">
                </div>

                <div class="flex gap-2">
                    <button type="button" @click="showStudentModal = false" class="flex-1 py-2.5 border border-slate-300 rounded-xl text-xs font-bold text-slate-600">বাতিল</button>
                    <button type="submit" class="flex-1 py-2.5 bg-emerald-night text-amber-300 hover:text-white rounded-xl text-xs font-black shadow transition">
                        সংরক্ষণ করুন
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3 Big Action Navigation Tabs for Muallim -->
    <div class="flex items-center space-x-2 bg-[#ece4d0] p-1.5 rounded-2xl mb-6 border border-[#dfd4bd]">
        <button @click="activeTab = 'sabak'" 
                :class="activeTab === 'sabak' ? 'bg-[#fffefb] text-emerald-night font-black shadow-md' : 'text-slate-600 font-bold hover:text-emerald-night'" 
                class="flex-1 py-2.5 px-2 rounded-xl text-xs sm:text-sm transition flex items-center justify-center gap-1.5">
            <i class="fas fa-graduation-cap text-gold-deep"></i>
            <span>সবক ক্লাস আবেদন</span>
        </button>

        <button @click="activeTab = 'books'" 
                :class="activeTab === 'books' ? 'bg-[#fffefb] text-emerald-night font-black shadow-md' : 'text-slate-600 font-bold hover:text-emerald-night'" 
                class="flex-1 py-2.5 px-2 rounded-xl text-xs sm:text-sm transition flex items-center justify-center gap-1.5">
            <i class="fas fa-boxes-stacked text-gold-deep"></i>
            <span>কিতাব চাহিদা</span>
        </button>

        <button @click="activeTab = 'history'" 
                :class="activeTab === 'history' ? 'bg-[#fffefb] text-emerald-night font-black shadow-md' : 'text-slate-600 font-bold hover:text-emerald-night'" 
                class="flex-1 py-2.5 px-2 rounded-xl text-xs sm:text-sm transition flex items-center justify-center gap-1.5">
            <i class="fas fa-clock-rotate-left text-gold-deep"></i>
            <span>আবেদনের অবস্থা</span>
        </button>
    </div>

    <!-- Tab 1: Sabak Class Booking Form -->
    <div x-show="activeTab === 'sabak'" x-cloak class="space-y-4">
        <div class="bg-[#fffefb] rounded-3xl border-2 border-emerald-600/30 p-5 sm:p-6 shadow-sm">
            <div class="flex items-center space-x-3 mb-4 pb-3 border-b border-[#e5ddcb]">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-900 flex items-center justify-center text-lg shrink-0">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div>
                    <h3 class="font-black text-base text-emerald-night">সমাপনী সবক ক্লাস ও দোয়া মাহফিলের আবেদন</h3>
                    <p class="text-xs text-slate-500">শিক্ষার্থীরা কুরআন সম্পন্ন করলে জেলা পরিচালকের উপস্থিতিতে সমাপনী ক্লাসের তারিখ আবেদন করুন।</p>
                </div>
            </div>

            <form action="<?= $baseUrl ?>/teacher/activity" method="POST" class="space-y-3.5">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="activity_type" value="sabak_class">

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">সমাপনী ক্লাসের শিরোনাম <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="কুরআন সমাপ্তি ও চূড়ান্ত সবক ক্লাস আবেদন" required class="w-full px-3.5 py-2.5 rounded-xl border border-[#d6ccb9] text-xs font-bold bg-[#fdfbf7] outline-none focus:ring-2 focus:ring-gold-rich">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">সমাপ্তকারী শিক্ষার্থীর সংখ্যা <span class="text-red-500">*</span></label>
                        <input type="number" min="1" name="quantity" value="20" required class="w-full px-3.5 py-2.5 rounded-xl border border-[#d6ccb9] text-xs font-black bg-[#fdfbf7] outline-none focus:ring-2 focus:ring-gold-rich">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">কাঙ্ক্ষিত তারিখ</label>
                        <input type="date" name="preferred_date" class="w-full px-3.5 py-2.5 rounded-xl border border-[#d6ccb9] text-xs bg-[#fdfbf7] outline-none focus:ring-2 focus:ring-gold-rich">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">বিস্তারিত বিবরণ / বিশেষ মন্তব্য <span class="text-red-500">*</span></label>
                    <textarea name="details" rows="3" required placeholder="শ্রদ্ধেয় পরিচালক মহোদয়, আমাদের মাদরাসায় ২০ জন শিক্ষার্থী নাজেরা সম্পন্ন করেছে..." class="w-full px-3.5 py-2.5 rounded-xl border border-[#d6ccb9] text-xs bg-[#fdfbf7] outline-none focus:ring-2 focus:ring-gold-rich">শ্রদ্ধেয় পরিচালক মহোদয়, আমাদের মাদরাসায় শিক্ষার্থীরা কুরআন শিক্ষা সমাপ্ত করেছে। তাদের সমাপনী সবক ও দোয়া মাহফিলে আপনার উপস্থিতি কামনা করছি।</textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-emerald-night to-emerald-deep text-amber-300 hover:text-white rounded-2xl text-xs font-black shadow-lg transition">
                        <i class="fas fa-paper-plane mr-1.5"></i> সবক ক্লাসের আবেদন পাঠান
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tab 2: Book Requisition Form -->
    <div x-show="activeTab === 'books'" x-cloak class="space-y-4">
        <div class="bg-[#fffefb] rounded-3xl border-2 border-emerald-600/30 p-5 sm:p-6 shadow-sm">
            <div class="flex items-center space-x-3 mb-4 pb-3 border-b border-[#e5ddcb]">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-gold-deep flex items-center justify-center text-lg shrink-0">
                    <i class="fas fa-book-quran"></i>
                </div>
                <div>
                    <h3 class="font-black text-base text-emerald-night">কিতাব ও প্রকাশনা চাহিদা প্রেরণ</h3>
                    <p class="text-xs text-slate-500">আপনার শিক্ষার্থীদের জন্য জেলা পরিচালকের মাধ্যমে কারিয়ানা কিতাব সংগ্রহ করুন।</p>
                </div>
            </div>

            <form action="<?= $baseUrl ?>/teacher/activity" method="POST" class="space-y-3.5">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="activity_type" value="book_order">

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">কিতাবের নাম নির্বাচন করুন <span class="text-red-500">*</span></label>
                    <select name="title" required class="w-full px-3.5 py-2.5 rounded-xl border border-[#d6ccb9] text-xs font-black bg-[#fdfbf7] outline-none focus:ring-2 focus:ring-gold-rich">
                        <?php if (!empty($books)): ?>
                            <?php foreach ($books as $b): ?>
                                <option value="<?= htmlspecialchars($b['title']) ?>"><?= htmlspecialchars($b['title']) ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="কারিয়ানা নূরানী কুরআন">কারিয়ানা নূরানী কুরআন</option>
                            <option value="কারিয়ানা সহজ কায়দা">কারিয়ানা সহজ কায়দা</option>
                            <option value="৩০ দিনে কুরআন শিক্ষা">৩০ দিনে কুরআন শিক্ষা</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">প্রয়োজনীয় কপির সংখ্যা <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-2">
                        <select name="quantity" class="flex-1 px-3.5 py-2.5 rounded-xl border border-[#d6ccb9] text-xs font-black bg-[#fdfbf7] outline-none">
                            <option value="10">১০ কপি</option>
                            <option value="20" selected>২০ কপি</option>
                            <option value="30">৩০ কপি</option>
                            <option value="50">৫০ কপি</option>
                            <option value="100">১০০ কপি</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">চাহিদার বিস্তারিত বিবরণ <span class="text-red-500">*</span></label>
                    <textarea name="details" rows="3" required class="w-full px-3.5 py-2.5 rounded-xl border border-[#d6ccb9] text-xs bg-[#fdfbf7] outline-none focus:ring-2 focus:ring-gold-rich">শ্রদ্ধেয় পরিচালক মহোদয়, আমাদের নতুন ব্যাচের শিক্ষার্থীদের জন্য উল্লিখিত কিতাবের জরুরি প্রয়োজন।</textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-emerald-night to-emerald-deep text-amber-300 hover:text-white rounded-2xl text-xs font-black shadow-lg transition">
                        <i class="fas fa-boxes-stacked mr-1.5"></i> কিতাবের চাহিদা পাঠান
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tab 3: Activities & Status Tracker History -->
    <div x-show="activeTab === 'history'" x-cloak class="space-y-4">
        <h3 class="text-base font-black text-emerald-night flex items-center">
            <i class="fas fa-clock-rotate-left text-gold-deep mr-2"></i> আমার প্রেরিত আবেদন ও কার্যক্রমের তালিকা
        </h3>

        <?php if (!empty($activities)): ?>
            <div class="space-y-3">
                <?php foreach ($activities as $act): ?>
                    <div class="bg-[#fffefb] rounded-2xl border-2 <?= $act['status'] === 'approved' ? 'border-emerald-500/40' : 'border-[#e4dccb]' ?> p-4 shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black <?= $act['status'] === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($act['status'] === 'scheduled' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-900') ?>">
                                        <?= $act['status'] === 'approved' ? '✓ অনুমোদিত' : ($act['status'] === 'scheduled' ? '📅 তারিখ নির্ধারিত' : '⏳ অপেক্ষমান (পেন্ডিং)') ?>
                                    </span>
                                    <span class="text-[11px] text-slate-400">
                                        <?= date('d M Y', strtotime($act['created_at'])) ?>
                                    </span>
                                </div>
                                <h4 class="font-black text-sm text-emerald-night"><?= htmlspecialchars($act['title']) ?></h4>
                                <p class="text-xs text-slate-600 mt-0.5"><?= htmlspecialchars($act['details']) ?></p>
                                
                                <?php if (!empty($act['director_notes'])): ?>
                                    <div class="mt-2 p-2 bg-amber-50/80 rounded-xl border border-amber-200 text-xs text-amber-900 font-bold">
                                        <i class="fas fa-comment-dots text-gold-deep mr-1"></i> পরিচালকের মন্তব্য: <?= htmlspecialchars($act['director_notes']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="text-right shrink-0">
                                <span class="text-xs font-bold text-slate-500">
                                    পরিমাণ: <strong><?= \Core\BengaliHelper::toBengaliNumber($act['quantity'] ?? 0) ?></strong>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="bg-[#fffefb] rounded-3xl border-2 border-dashed border-[#dfd4bd] p-8 text-center text-xs text-slate-500">
                আপনি এখনো কোনো আবেদন করেননি।
            </div>
        <?php endif; ?>
    </div>
</div>
