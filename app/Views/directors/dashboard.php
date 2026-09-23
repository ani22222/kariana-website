<?php
/**
 * Dedicated District Director Dashboard - Kariana Quran
 * Re-Architected as a Modern Islamic Native Mobile App Experience
 */
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';
$csrfToken = \Core\Csrf::getToken();
$success = \Core\Session::getFlash('success');
$error = \Core\Session::getFlash('error');

$totalStudents = array_sum(array_column($teachers ?? [], 'total_students'));
$pendingActivities = array_filter($teacherActivities ?? [], fn($a) => ($a['status'] ?? '') === 'pending');
?>
<div class="container mx-auto px-3 sm:px-4 py-4 sm:py-6 max-w-5xl" x-data="{ activeSection: 'overview', showAddTeacher: false, showBookModal: false }">

    <?php if (\Core\Session::get('admin_impersonating')): ?>
        <!-- Super Admin Impersonation Notice -->
        <div class="bg-gradient-to-r from-amber-600 to-amber-700 text-white p-3.5 sm:p-4 rounded-2xl shadow-lg mb-5 flex flex-col sm:flex-row items-center justify-between gap-3 border border-amber-300">
            <div class="flex items-center space-x-3">
                <span class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center text-lg shrink-0">🛡️</span>
                <div class="text-center sm:text-left">
                    <p class="font-black text-xs sm:text-sm text-white">সুপার এডমিন ভিউ মোড সক্রিয়</p>
                    <p class="text-[11px] sm:text-xs text-amber-100">আপনি বর্তমানে <strong><?= htmlspecialchars($director['name']) ?></strong> (<?= htmlspecialchars($director['district_name']) ?> জেলা)-এর ড্যাশবোর্ডে আছেন।</p>
                </div>
            </div>
            <a href="<?= $baseUrl ?>/admin/directors/exit-impersonation" class="bg-white text-emerald-night hover:bg-amber-50 px-4 py-2 rounded-xl text-xs font-black transition shadow shrink-0 flex items-center">
                <i class="fas fa-arrow-left mr-1.5"></i> মূল অ্যাডমিনে ফিরুন
            </a>
        </div>
    <?php endif; ?>

    <!-- Director App Shell Header -->
    <div class="bg-gradient-to-br from-emerald-night via-emerald-deep to-emerald-night rounded-3xl p-5 sm:p-7 text-white shadow-xl mb-6 border border-gold-rich/40 relative overflow-hidden">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 relative z-10">
            <div class="flex flex-col sm:flex-row items-center gap-4 text-center sm:text-left">
                <!-- Avatar -->
                <div class="w-18 h-18 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-tr from-gold-rich to-amber-300 text-emerald-night flex items-center justify-center text-3xl font-extrabold shadow-lg border-2 border-white/40 shrink-0">
                    <i class="fas fa-user-tie"></i>
                </div>

                <div>
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-1.5 mb-1.5">
                        <span class="px-2.5 py-0.5 bg-gold-rich text-white text-[11px] font-black rounded-full shadow-sm">
                            <?= htmlspecialchars($director['designation']) ?>
                        </span>
                        <span class="px-2.5 py-0.5 bg-emerald-800 text-emerald-100 text-[11px] font-bold rounded-full border border-emerald-600">
                            <i class="fas fa-map-marker-alt text-amber-300 mr-1"></i> <?= htmlspecialchars($director['district_name']) ?> জেলা (<?= htmlspecialchars($director['division_name']) ?> বিভাগ)
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                        <?= htmlspecialchars($director['name']) ?>
                    </h1>
                    <p class="text-xs text-emerald-100/80 mt-1 flex flex-wrap items-center justify-center sm:justify-start gap-2">
                        <span><i class="fas fa-phone-alt text-amber-300 mr-1"></i> <?= htmlspecialchars($director['phone']) ?></span>
                        <span class="opacity-40">|</span>
                        <span><i class="fas fa-id-badge text-amber-300 mr-1"></i> আইডি: <?= htmlspecialchars($director['username']) ?></span>
                    </p>
                </div>
            </div>

            <!-- Founder Contact & Actions -->
            <div class="flex flex-wrap items-center justify-center gap-2">
                <a href="tel:01717056816" class="bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white px-3.5 py-2 rounded-xl text-xs font-black transition shadow flex items-center">
                    <i class="fas fa-phone mr-1.5 text-white animate-pulse"></i> প্রতিষ্ঠাতা হেল্পলাইন
                </a>
                <a href="<?= $baseUrl ?>/directors/<?= $director['slug'] ?>" target="_blank" class="bg-white/10 hover:bg-white/20 border border-white/20 text-white px-3 py-2 rounded-xl text-xs font-bold transition flex items-center">
                    <i class="fas fa-eye mr-1"></i> পাবলিক প্রোফাইল
                </a>
                <a href="<?= $baseUrl ?>/director/logout" class="bg-red-500/20 hover:bg-red-600/40 border border-red-400/40 text-red-200 px-3 py-2 rounded-xl text-xs font-bold transition flex items-center">
                    <i class="fas fa-sign-out-alt mr-1"></i> প্রস্থান
                </a>
            </div>
        </div>
    </div>

    <!-- Flash Alerts -->
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

    <!-- 4 Mobile KPI Counter Cards (Touch Grid) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div @click="activeSection = 'teachers'" class="bg-[#fffefb] p-4 rounded-2xl border-2 border-emerald-600/20 hover:border-gold-rich shadow-sm cursor-pointer transition transform active:scale-95 text-center">
            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">আমার শিক্ষক</p>
            <h3 class="text-2xl sm:text-3xl font-black text-emerald-night mt-0.5">
                <?= \Core\BengaliHelper::toBengaliNumber(count($teachers ?? [])) ?> <span class="text-xs font-bold text-slate-500">জন</span>
            </h3>
        </div>

        <div class="bg-[#fffefb] p-4 rounded-2xl border-2 border-emerald-600/20 shadow-sm text-center">
            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">জেলায় শিক্ষার্থী</p>
            <h3 class="text-2xl sm:text-3xl font-black text-gold-deep mt-0.5">
                <?= \Core\BengaliHelper::toBengaliNumber($totalStudents) ?> <span class="text-xs font-bold text-slate-500">জন</span>
            </h3>
        </div>

        <div @click="activeSection = 'books'" class="bg-[#fffefb] p-4 rounded-2xl border-2 border-emerald-600/20 hover:border-gold-rich shadow-sm cursor-pointer transition transform active:scale-95 text-center">
            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">বিতরণকৃত কিতাব</p>
            <h3 class="text-2xl sm:text-3xl font-black text-emerald-night mt-0.5">
                <?= \Core\BengaliHelper::toBengaliNumber($director['total_books_ordered'] ?? 0) ?> <span class="text-xs font-bold text-slate-500">কপি</span>
            </h3>
        </div>

        <div @click="activeSection = 'sabak'" class="bg-[#fffefb] p-4 rounded-2xl border-2 border-emerald-600/20 hover:border-gold-rich shadow-sm cursor-pointer transition transform active:scale-95 text-center">
            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">সবক ক্লাস আবেদন</p>
            <h3 class="text-2xl sm:text-3xl font-black text-amber-600 mt-0.5 flex items-center justify-center gap-1">
                <?= \Core\BengaliHelper::toBengaliNumber(count($teacherActivities ?? [])) ?>
                <?php if (count($pendingActivities) > 0): ?>
                <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-ping"></span>
                <?php endif; ?>
            </h3>
        </div>
    </div>

    <!-- 4 Big Touch App Tiles for Instant Mobile Navigation -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <!-- Tile 1: Teachers -->
        <button @click="activeSection = 'teachers'" 
                :class="activeSection === 'teachers' ? 'border-gold-rich bg-amber-50/60 shadow-md ring-2 ring-gold-rich' : 'border-[#dfd4bd] bg-[#fffefb]'"
                class="p-4 rounded-2xl border-2 text-center transition flex flex-col items-center justify-center group active:scale-95">
            <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-900 flex items-center justify-center text-xl mb-2 group-hover:scale-110 transition shadow-inner">
                <i class="fas fa-chalkboard-user"></i>
            </div>
            <h4 class="font-black text-xs sm:text-sm text-emerald-night">শিক্ষক ম্যানেজমেন্ট</h4>
            <p class="text-[10px] text-slate-500 mt-0.5">তালিকা ও নতুন শিক্ষক</p>
        </button>

        <!-- Tile 2: Book Orders -->
        <button @click="activeSection = 'books'" 
                :class="activeSection === 'books' ? 'border-gold-rich bg-amber-50/60 shadow-md ring-2 ring-gold-rich' : 'border-[#dfd4bd] bg-[#fffefb]'"
                class="p-4 rounded-2xl border-2 text-center transition flex flex-col items-center justify-center group active:scale-95">
            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-gold-deep flex items-center justify-center text-xl mb-2 group-hover:scale-110 transition shadow-inner">
                <i class="fas fa-boxes-stacked"></i>
            </div>
            <h4 class="font-black text-xs sm:text-sm text-emerald-night">কিতাব অর্ডার</h4>
            <p class="text-[10px] text-slate-500 mt-0.5">কেন্দ্রীয় রিকুইজিশন</p>
        </button>

        <!-- Tile 3: Sabak Classes -->
        <button @click="activeSection = 'sabak'" 
                :class="activeSection === 'sabak' ? 'border-gold-rich bg-amber-50/60 shadow-md ring-2 ring-gold-rich' : 'border-[#dfd4bd] bg-[#fffefb]'"
                class="p-4 rounded-2xl border-2 text-center transition flex flex-col items-center justify-center group active:scale-95 relative">
            <?php if (count($pendingActivities) > 0): ?>
            <span class="absolute top-2 right-2 px-1.5 py-0.2 bg-red-600 text-white text-[9px] font-black rounded-full animate-bounce">
                <?= count($pendingActivities) ?>
            </span>
            <?php endif; ?>
            <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-900 flex items-center justify-center text-xl mb-2 group-hover:scale-110 transition shadow-inner">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h4 class="font-black text-xs sm:text-sm text-emerald-night">সবক ক্লাস ও দোয়া</h4>
            <p class="text-[10px] text-slate-500 mt-0.5">অনুমোদন ও তারিখ</p>
        </button>

        <!-- Tile 4: Message to Saddam Hossain -->
        <button @click="activeSection = 'message'" 
                :class="activeSection === 'message' ? 'border-gold-rich bg-amber-50/60 shadow-md ring-2 ring-gold-rich' : 'border-[#dfd4bd] bg-[#fffefb]'"
                class="p-4 rounded-2xl border-2 text-center transition flex flex-col items-center justify-center group active:scale-95">
            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-gold-deep flex items-center justify-center text-xl mb-2 group-hover:scale-110 transition shadow-inner">
                <i class="fas fa-paper-plane"></i>
            </div>
            <h4 class="font-black text-xs sm:text-sm text-emerald-night">বার্তা ও চিঠি</h4>
            <p class="text-[10px] text-slate-500 mt-0.5">প্রধান কার্যালয়ে যোগাযোগ</p>
        </button>
    </div>

    <!-- ==================== SECTION 1: TEACHERS MANAGEMENT ==================== -->
    <div x-show="activeSection === 'teachers' || activeSection === 'overview'" class="space-y-4 mb-8" id="teachers-section">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-[#e4dccb]">
            <div>
                <h2 class="text-lg sm:text-xl font-black text-emerald-night flex items-center">
                    <i class="fas fa-users text-gold-deep mr-2"></i> আমার জেলাধীন শিক্ষক ও মুয়াল্লিমবৃন্দ (<?= count($teachers ?? []) ?> জন)
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">আপনার জেলায় কর্মরত সকল শিক্ষকের তালিকা ও সরাসরি যোগাযোগের ডিরেক্টরি।</p>
            </div>
            <button @click="showAddTeacher = !showAddTeacher" class="bg-gradient-to-r from-emerald-night to-emerald-deep text-amber-300 hover:text-white px-4 py-2.5 rounded-xl text-xs font-black shadow transition flex items-center justify-center shrink-0">
                <i class="fas fa-user-plus mr-1.5"></i> ➕ নতুন শিক্ষক যুক্ত করুন
            </button>
        </div>

        <!-- Add New Teacher Form (Expandable Drawer) -->
        <div x-show="showAddTeacher" x-cloak x-transition class="bg-[#fffefb] rounded-3xl border-2 border-gold-rich p-5 sm:p-6 shadow-lg mb-6">
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-[#e5ddcb]">
                <h3 class="font-black text-sm sm:text-base text-emerald-night flex items-center">
                    <i class="fas fa-user-plus text-gold-rich mr-2"></i> নতুন শিক্ষক সংযোজন ফর্ম
                </h3>
                <button @click="showAddTeacher = false" class="text-slate-400 hover:text-red-500 text-sm font-bold">✕ বন্ধ করুন</button>
            </div>

            <form action="<?= $baseUrl ?>/director/teachers/create" method="POST" class="space-y-3.5">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">শিক্ষকের পূর্ণ নাম <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required placeholder="উদাঃ হাফেজ মাওলানা আব্দুর রহমান" class="w-full px-3.5 py-2.5 rounded-xl border border-[#d6ccb9] text-xs font-bold bg-[#fdfbf7] outline-none focus:ring-2 focus:ring-gold-rich">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">মোবাইল নম্বর <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" required placeholder="উদাঃ 017XXXXXXXX" class="w-full px-3.5 py-2.5 rounded-xl border border-[#d6ccb9] text-xs font-bold bg-[#fdfbf7] outline-none focus:ring-2 focus:ring-gold-rich">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">এলাকা / মাদরাসার নাম</label>
                        <input type="text" name="area_name" placeholder="উদাঃ সদর বাজার জামে মসজিদ" class="w-full px-3.5 py-2.5 rounded-xl border border-[#d6ccb9] text-xs bg-[#fdfbf7] outline-none focus:ring-2 focus:ring-gold-rich">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">শিক্ষার্থীর সংখ্যা</label>
                        <input type="number" min="1" name="total_students" value="20" class="w-full px-3.5 py-2.5 rounded-xl border border-[#d6ccb9] text-xs font-bold bg-[#fdfbf7] outline-none focus:ring-2 focus:ring-gold-rich">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">শিক্ষাদানের স্থান</label>
                        <select name="location_type" class="w-full px-3.5 py-2.5 rounded-xl border border-[#d6ccb9] text-xs font-bold bg-[#fdfbf7] outline-none focus:ring-2 focus:ring-gold-rich">
                            <option value="madrasa">মাদরাসা</option>
                            <option value="mosque">মসজিদ / মক্তব</option>
                            <option value="home">বাড়ি / হোম টিউশন</option>
                            <option value="institution">প্রতিষ্ঠান</option>
                        </select>
                    </div>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" @click="showAddTeacher = false" class="px-4 py-2 border border-slate-300 rounded-xl text-xs font-bold text-slate-600">বাতিল</button>
                    <button type="submit" class="px-6 py-2.5 bg-emerald-night text-amber-300 hover:text-white rounded-xl text-xs font-black shadow transition">
                        <i class="fas fa-check mr-1.5"></i> শিক্ষক নিশ্চিত করুন
                    </button>
                </div>
            </form>
        </div>

        <!-- Touch-First Mobile Teacher Cards Grid (Replaces bulky tables on Mobile!) -->
        <?php if (!empty($teachers)): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                <?php foreach ($teachers as $idx => $t): ?>
                    <div class="bg-[#fffefb] rounded-2xl border-2 border-[#e6dcce] hover:border-gold-rich p-4 shadow-sm transition flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <div class="flex items-center space-x-2.5">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-800 flex items-center justify-center font-bold text-sm border border-emerald-200 shrink-0">
                                        <?= \Core\BengaliHelper::toBengaliNumber($idx + 1) ?>
                                    </div>
                                    <div>
                                        <h4 class="font-black text-sm text-emerald-night leading-tight">
                                            <?= htmlspecialchars($t['name']) ?>
                                        </h4>
                                        <p class="text-[11px] text-slate-500 mt-0.5">
                                            <i class="fas fa-map-marker-alt text-gold-deep mr-1"></i> <?= htmlspecialchars($t['area_name']) ?>
                                        </p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-black rounded-full shrink-0">
                                    <?= htmlspecialchars($t['status'] ?? 'active') === 'active' ? 'সক্রিয়' : 'প্রশিক্ষণ' ?>
                                </span>
                            </div>

                            <div class="bg-[#fcfaf5] p-2.5 rounded-xl border border-[#efe6d5] my-2.5 text-xs flex justify-between items-center text-slate-700">
                                <span><i class="fas fa-graduation-cap text-gold-deep mr-1"></i> শিক্ষার্থী: <strong><?= \Core\BengaliHelper::toBengaliNumber($t['total_students']) ?> জন</strong></span>
                                <span class="text-[10px] text-slate-400">আইডি: <?= htmlspecialchars($t['username'] ?? '') ?></span>
                            </div>
                        </div>

                        <!-- 1-Tap Touch Contact Action Buttons -->
                        <div class="pt-2 border-t border-[#ede5d6] flex items-center justify-between gap-2">
                            <div class="flex items-center gap-1.5 flex-1">
                                <a href="tel:<?= htmlspecialchars($t['phone']) ?>" class="flex-1 py-1.5 px-2 bg-emerald-50 hover:bg-emerald-night hover:text-white border border-emerald-300 text-emerald-900 rounded-xl text-[11px] font-black transition text-center flex items-center justify-center gap-1">
                                    <i class="fas fa-phone-alt text-emerald-600"></i> কল
                                </a>
                                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $t['phone']) ?>" target="_blank" class="flex-1 py-1.5 px-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-[11px] font-black transition text-center flex items-center justify-center gap-1">
                                    <i class="fab fa-whatsapp"></i> হোয়াটসঅ্যাপ
                                </a>
                            </div>

                            <!-- Delete Teacher Form -->
                            <form action="<?= $baseUrl ?>/director/teachers/delete/<?= $t['id'] ?>" method="POST" onsubmit="return confirm('আপনি কি নিশ্চিতভাবে এই শিক্ষকের তথ্য মুছে ফেলতে চান?');">
                                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                <button type="submit" class="w-8 h-8 rounded-xl bg-red-50 hover:bg-red-600 hover:text-white text-red-500 border border-red-200 transition flex items-center justify-center text-xs" title="শিক্ষক মুছে ফেলুন">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="bg-[#fffefb] rounded-3xl border-2 border-dashed border-[#dfd4bd] p-8 text-center">
                <i class="fas fa-user-tie text-3xl text-gold-rich mb-2"></i>
                <h4 class="font-black text-sm text-emerald-night">আপনার জেলায় এখনো কোনো শিক্ষক যুক্ত করা হয়নি</h4>
                <p class="text-xs text-slate-500 mt-1 mb-4">উপরের "➕ নতুন শিক্ষক যুক্ত করুন" বাটনে ক্লিক করে শিক্ষকদের তালিকায় অন্তর্ভুক্ত করুন।</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- ==================== SECTION 2: BOOK ORDERS & REQUISITIONS ==================== -->
    <div x-show="activeSection === 'books'" x-cloak class="space-y-4 mb-8">
        <div class="pb-3 border-b border-[#e4dccb]">
            <h2 class="text-lg sm:text-xl font-black text-emerald-night flex items-center">
                <i class="fas fa-book-quran text-gold-deep mr-2"></i> কেন্দ্রীয় কিতাব ও প্রকাশনা রিকুইজিশন
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">আপনার জেলার শিক্ষার্থীদের জন্য সরাসরি কেন্দ্রীয় প্রকাশনা থেকে কিতাব অর্ডার করুন।</p>
        </div>

        <!-- Official Books Grid with 1-Tap Requisition -->
        <?php if (!empty($books)): ?>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <?php foreach ($books as $b): ?>
                    <div class="bg-[#fffefb] rounded-2xl border-2 border-emerald-600/30 p-4 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                        <div>
                            <div class="aspect-[4/3] bg-amber-50 rounded-xl overflow-hidden mb-3 border border-[#e4dccb] flex items-center justify-center">
                                <?php if (!empty($b['cover_image'])): ?>
                                    <img src="<?= $baseUrl ?>/<?= htmlspecialchars($b['cover_image']) ?>" alt="<?= htmlspecialchars($b['title']) ?>" class="h-full object-contain">
                                <?php else: ?>
                                    <i class="fas fa-book-open text-4xl text-gold-rich"></i>
                                <?php endif; ?>
                            </div>
                            <h4 class="font-black text-sm text-emerald-night"><?= htmlspecialchars($b['title']) ?></h4>
                            <p class="text-xs font-bold text-gold-deep mt-1">মূল্য: ৳<?= \Core\BengaliHelper::toBengaliNumber($b['price']) ?></p>
                        </div>

                        <!-- 1-Tap Order Form with Preset Stepper -->
                        <form action="<?= $baseUrl ?>/director/request" method="POST" class="mt-4 pt-3 border-t border-[#ede5d6] space-y-2">
                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                            <input type="hidden" name="request_type" value="book_order">
                            <input type="hidden" name="details" value="<?= htmlspecialchars($b['title']) ?> - জেলা কিতাব রিকুইজিশন">

                            <label class="block text-[11px] font-bold text-slate-600">কপির সংখ্যা নির্বাচন করুন:</label>
                            <div class="flex items-center gap-1.5">
                                <select name="quantity" class="flex-1 px-3 py-2 border border-[#d6ccb9] rounded-xl text-xs font-black bg-[#fdfbf7] outline-none">
                                    <option value="50">৫০ কপি</option>
                                    <option value="100" selected>১০০ কপি</option>
                                    <option value="200">২০০ কপি</option>
                                    <option value="500">৫০০ কপি</option>
                                    <option value="1000">১,০০০ কপি</option>
                                </select>
                                <button type="submit" class="px-4 py-2 bg-emerald-night text-amber-300 hover:text-white rounded-xl text-xs font-black transition shadow">
                                    অর্ডার পাঠান
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Requisition History Table / Cards -->
        <h3 class="font-black text-sm text-slate-700 mt-6 mb-2">আমার প্রেরিত রিকুইজিশন ইতিহাস</h3>
        <?php if (!empty($requests)): ?>
            <div class="space-y-2.5">
                <?php foreach ($requests as $req): ?>
                    <div class="bg-[#fffefb] p-3.5 rounded-xl border border-[#e4dccb] flex items-center justify-between gap-3 text-xs">
                        <div>
                            <span class="font-bold text-emerald-night"><?= htmlspecialchars($req['details']) ?></span>
                            <span class="text-slate-500 ml-2">(<?= \Core\BengaliHelper::toBengaliNumber($req['quantity']) ?> কপি)</span>
                            <p class="text-[10px] text-slate-400 mt-0.5"><?= date('d M Y, h:i A', strtotime($req['created_at'])) ?></p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full font-black text-[10px] <?= $req['status'] === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-900' ?>">
                            <?= $req['status'] === 'approved' ? 'অনুমোদিত' : 'যাচাইাধীন' ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-xs text-slate-400">এখনো কোনো রিকুইজিশন ইতিহাস নেই।</p>
        <?php endif; ?>
    </div>

    <!-- ==================== SECTION 3: SABAK CEREMONIES ==================== -->
    <div x-show="activeSection === 'sabak'" x-cloak class="space-y-4 mb-8">
        <div class="pb-3 border-b border-[#e4dccb]">
            <h2 class="text-lg sm:text-xl font-black text-emerald-night flex items-center">
                <i class="fas fa-graduation-cap text-gold-deep mr-2"></i> শিক্ষকদের সবক ক্লাস ও সমাপনী মাহফিল অনুমোদন
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">আপনার জেলার শিক্ষকরা যেসকল সবক ক্লাসের আবেদন করেছেন তা অনুমোদন বা তারিখ নির্ধারণ করুন।</p>
        </div>

        <?php if (!empty($teacherActivities)): ?>
            <div class="space-y-3.5">
                <?php foreach ($teacherActivities as $act): ?>
                    <div class="bg-[#fffefb] rounded-2xl border-2 <?= $act['status'] === 'pending' ? 'border-amber-400' : 'border-[#e4dccb]' ?> p-4 shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-black rounded">
                                        <?= htmlspecialchars($act['teacher_name']) ?>
                                    </span>
                                    <span class="text-xs text-slate-500"><i class="fas fa-map-pin text-gold-deep mr-1"></i> <?= htmlspecialchars($act['area_name']) ?></span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black <?= $act['status'] === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($act['status'] === 'scheduled' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-900') ?>">
                                        <?= $act['status'] === 'approved' ? 'অনুমোদিত' : ($act['status'] === 'scheduled' ? 'তারিখ নির্ধারিত' : 'অপেক্ষমান') ?>
                                    </span>
                                </div>
                                <h4 class="font-black text-sm text-emerald-night"><?= htmlspecialchars($act['title']) ?></h4>
                                <p class="text-xs text-slate-600 mt-0.5"><?= htmlspecialchars($act['description'] ?? '') ?> (শিক্ষার্থী: <strong><?= \Core\BengaliHelper::toBengaliNumber($act['quantity'] ?? 0) ?> জন</strong>)</p>
                            </div>

                            <!-- 1-Click Action Form -->
                            <form action="<?= $baseUrl ?>/director/activity/update" method="POST" class="flex flex-wrap items-center gap-2 sm:justify-end">
                                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                <input type="hidden" name="activity_id" value="<?= $act['id'] ?>">

                                <input type="text" name="director_notes" value="<?= htmlspecialchars($act['director_notes'] ?? '') ?>" placeholder="পরিচালকের নোট / তারিখ লিখুন..." class="px-3 py-1.5 border border-[#d6ccb9] rounded-xl text-xs bg-[#fdfbf7] outline-none">

                                <select name="status" class="px-2 py-1.5 border border-[#d6ccb9] rounded-xl text-xs font-bold bg-[#fdfbf7] outline-none">
                                    <option value="approved" <?= $act['status'] === 'approved' ? 'selected' : '' ?>>অনুমোদন</option>
                                    <option value="scheduled" <?= $act['status'] === 'scheduled' ? 'selected' : '' ?>>তারিখ ধার্য</option>
                                    <option value="completed" <?= $act['status'] === 'completed' ? 'selected' : '' ?>>সম্পন্ন</option>
                                </select>

                                <button type="submit" class="px-3 py-1.5 bg-emerald-night text-amber-300 hover:text-white rounded-xl text-xs font-black shadow transition">
                                    সেভ
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="bg-[#fffefb] rounded-3xl border-2 border-dashed border-[#dfd4bd] p-8 text-center text-xs text-slate-500">
                শিক্ষকদের পক্ষ থেকে এখনো কোনো সবক ক্লাসের আবেদন আসেনি।
            </div>
        <?php endif; ?>
    </div>

    <!-- ==================== SECTION 4: MESSAGE TO MAULANA SADDAM HOSSAIN ==================== -->
    <div x-show="activeSection === 'message'" x-cloak class="space-y-4 mb-8">
        <div class="bg-[#fffefb] rounded-3xl border-2 border-gold-rich p-6 shadow-md max-w-2xl mx-auto">
            <div class="flex items-center space-x-3 mb-4 pb-3 border-b border-[#e5ddcb]">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-gold-deep flex items-center justify-center text-xl shrink-0">
                    <i class="fas fa-envelope-open-text"></i>
                </div>
                <div>
                    <h3 class="font-black text-base text-emerald-night">কেন্দ্রীয় প্রতিষ্ঠাতা বরাবর বার্তা প্রেরণ</h3>
                    <p class="text-xs text-slate-500">মাওলানা সাদ্দাম হোসেন ও প্রধান কার্যালয়ে সরাসরি সাংগঠনিক বার্তা বা পরামর্শ পাঠান।</p>
                </div>
            </div>

            <form action="<?= $baseUrl ?>/director/request" method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <input type="hidden" name="request_type" value="general">

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">বার্তার বিষয়</label>
                    <input type="text" name="details" required placeholder="উদাঃ জেলা সম্মেলন / নতুন শিক্ষক প্রশিক্ষণ সংক্রান্ত পরামর্শ..." class="w-full px-4 py-2.5 rounded-xl border border-[#d6ccb9] text-xs font-bold bg-[#fdfbf7] outline-none focus:ring-2 focus:ring-gold-rich">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">বিস্তারিত বার্তা / নিবেদন</label>
                    <textarea name="details_extra" rows="4" class="w-full px-4 py-2.5 rounded-xl border border-[#d6ccb9] text-xs bg-[#fdfbf7] outline-none focus:ring-2 focus:ring-gold-rich" placeholder="শ্রদ্ধেয় হযরত, আমাদের জেলার সাংগঠনিক কাজের অগ্রগতি..."></textarea>
                </div>

                <div class="pt-2 flex justify-between items-center">
                    <a href="tel:01717056816" class="text-xs text-gold-deep hover:text-emerald-night font-bold flex items-center">
                        <i class="fas fa-phone mr-1"></i> জরুরি ফোন: ০১৭১৭০৫৬৮১৬
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-emerald-night to-emerald-deep text-amber-300 hover:text-white rounded-xl text-xs font-black shadow-lg transition">
                        <i class="fas fa-paper-plane mr-1.5"></i> বার্তা পাঠান
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
