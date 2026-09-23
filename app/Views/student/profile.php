<?php
/**
 * Student & General User Profile Portal View - Kariana Quran
 * Designed with Mobile-First Native App Experience
 */
$csrfToken = \Core\Csrf::getToken();
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';
?>
<div class="container mx-auto px-3 sm:px-4 py-6 max-w-4xl">
    <!-- Top Mobile App Header Card -->
    <div class="bg-gradient-to-br from-emerald-night via-emerald-deep to-emerald-night rounded-3xl p-5 sm:p-7 text-white shadow-xl mb-6 border border-gold-rich/40 relative overflow-hidden">
        <!-- Background Subtle Islamic Ornament -->
        <div class="absolute -right-8 -bottom-8 w-40 h-40 opacity-10 pointer-events-none">
            <svg viewBox="0 0 100 100" fill="currentColor"><path d="M50 0 L100 50 L50 100 L0 50 Z"/></svg>
        </div>

        <div class="flex flex-col sm:flex-row items-center sm:items-start justify-between gap-4 relative z-10">
            <div class="flex flex-col sm:flex-row items-center sm:items-center gap-4 text-center sm:text-left">
                <!-- Avatar with Initials -->
                <div class="w-18 h-18 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-tr from-gold-rich to-amber-300 text-emerald-night flex items-center justify-center text-3xl font-extrabold shadow-lg border-2 border-white/50 shrink-0">
                    <?= mb_substr($user['name'] ?: 'শ', 0, 1, 'UTF-8') ?>
                </div>

                <div>
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mb-1.5">
                        <span class="px-2.5 py-0.5 bg-gold-rich text-white text-[11px] font-bold rounded-full shadow-sm">
                            <i class="fas fa-user-graduate mr-1"></i> শিক্ষার্থী পোর্টাল
                        </span>
                        <?php if (!empty($user['district'])): ?>
                        <span class="px-2.5 py-0.5 bg-emerald-800 text-emerald-100 text-[11px] font-bold rounded-full border border-emerald-600">
                            <i class="fas fa-map-marker-alt text-amber-300 mr-1"></i> <?= htmlspecialchars($user['district']) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                        <?= htmlspecialchars($user['name'] ?: 'সম্মানিত শিক্ষার্থী') ?>
                    </h1>
                    <p class="text-xs sm:text-sm text-emerald-100/80 mt-0.5">
                        <i class="fas fa-phone-alt text-amber-300 mr-1"></i> <?= htmlspecialchars($user['phone'] ?? 'নম্বর যুক্ত নেই') ?>
                        <?php if (!empty($user['email'])): ?>
                        <span class="mx-1.5 opacity-40">|</span> <i class="fas fa-envelope text-amber-300 mr-1"></i> <?= htmlspecialchars($user['email']) ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <!-- Header Quick Action: Logout -->
            <div class="flex items-center gap-2 mt-2 sm:mt-0">
                <a href="<?= $baseUrl ?>/logout" class="bg-red-500/20 hover:bg-red-600/40 border border-red-400/40 text-red-200 px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center shadow-sm">
                    <i class="fas fa-sign-out-alt mr-1.5 text-xs"></i> প্রস্থান
                </a>
            </div>
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

    <!-- 4 App Touch Shortcuts (Grid: 2x2 on Mobile, 4x1 on Desktop) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <!-- Shortcut 1: Quran Reader -->
        <a href="<?= $baseUrl ?>/quran" class="bg-[#fffefb] hover:bg-amber-50/50 p-4 rounded-2xl border-2 border-emerald-600/20 hover:border-gold-rich shadow-sm transition transform active:scale-95 flex flex-col items-center text-center group">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-deep group-hover:bg-gold-rich group-hover:text-white flex items-center justify-center text-xl mb-2 transition shadow-inner">
                <i class="fas fa-quran"></i>
            </div>
            <h3 class="font-black text-sm text-emerald-night">কুরআন রিডার</h3>
            <p class="text-[11px] text-slate-500 mt-0.5">সহীহ তেলাওয়াত ও অর্থ</p>
        </a>

        <!-- Shortcut 2: QR Scanner -->
        <a href="<?= $baseUrl ?>/scan" class="bg-[#fffefb] hover:bg-amber-50/50 p-4 rounded-2xl border-2 border-emerald-600/20 hover:border-gold-rich shadow-sm transition transform active:scale-95 flex flex-col items-center text-center group">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-gold-deep group-hover:bg-gold-rich group-hover:text-white flex items-center justify-center text-xl mb-2 transition shadow-inner">
                <i class="fas fa-qrcode"></i>
            </div>
            <h3 class="font-black text-sm text-emerald-night">কিউআর স্ক্যানার</h3>
            <p class="text-[11px] text-slate-500 mt-0.5">বইয়ের ভিডিও ক্লাস</p>
        </a>

        <!-- Shortcut 3: Digital Tasbeeh -->
        <a href="<?= $baseUrl ?>/tasbeeh" class="bg-[#fffefb] hover:bg-amber-50/50 p-4 rounded-2xl border-2 border-emerald-600/20 hover:border-gold-rich shadow-sm transition transform active:scale-95 flex flex-col items-center text-center group">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-deep group-hover:bg-gold-rich group-hover:text-white flex items-center justify-center text-xl mb-2 transition shadow-inner">
                <i class="fas fa-fingerprint"></i>
            </div>
            <h3 class="font-black text-sm text-emerald-night">ডিজিটাল তাসবীহ</h3>
            <p class="text-[11px] text-slate-500 mt-0.5">দৈনিক জিকির ট্র্যাকার</p>
        </a>

        <!-- Shortcut 4: Prayer Times -->
        <a href="<?= $baseUrl ?>/prayer-times" class="bg-[#fffefb] hover:bg-amber-50/50 p-4 rounded-2xl border-2 border-emerald-600/20 hover:border-gold-rich shadow-sm transition transform active:scale-95 flex flex-col items-center text-center group">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-gold-deep group-hover:bg-gold-rich group-hover:text-white flex items-center justify-center text-xl mb-2 transition shadow-inner">
                <i class="fas fa-clock"></i>
            </div>
            <h3 class="font-black text-sm text-emerald-night">নামাজের সময়</h3>
            <p class="text-[11px] text-slate-500 mt-0.5"><?= htmlspecialchars($districtData['name_bn'] ?? 'ঢাকা') ?> জেলা</p>
        </a>
    </div>

    <!-- Mobile Tabs (Alpine.js) for Smooth Section Navigation -->
    <div x-data="{ activeTab: 'courses' }" class="mb-8">
        <!-- Tab Navigation Bar -->
        <div class="flex items-center space-x-2 bg-[#ece4d0] p-1.5 rounded-2xl mb-6 border border-[#dfd4bd]">
            <button @click="activeTab = 'courses'" 
                    :class="activeTab === 'courses' ? 'bg-[#fffefb] text-emerald-night font-bold shadow-md' : 'text-slate-600 font-medium hover:text-emerald-night'" 
                    class="flex-1 py-2.5 px-3 rounded-xl text-xs sm:text-sm transition flex items-center justify-center gap-1.5">
                <i class="fas fa-graduation-cap text-gold-deep"></i>
                <span>আমার কোর্সসমূহ</span>
                <span class="ml-1 px-1.5 py-0.2 bg-emerald-100 text-emerald-800 text-[10px] rounded-full font-bold">
                    <?= count($enrolledCourses) ?>
                </span>
            </button>

            <button @click="activeTab = 'profile'" 
                    :class="activeTab === 'profile' ? 'bg-[#fffefb] text-emerald-night font-bold shadow-md' : 'text-slate-600 font-medium hover:text-emerald-night'" 
                    class="flex-1 py-2.5 px-3 rounded-xl text-xs sm:text-sm transition flex items-center justify-center gap-1.5">
                <i class="fas fa-user-gear text-gold-deep"></i>
                <span>প্রোফাইল তথ্য</span>
            </button>

            <button @click="activeTab = 'security'" 
                    :class="activeTab === 'security' ? 'bg-[#fffefb] text-emerald-night font-bold shadow-md' : 'text-slate-600 font-medium hover:text-emerald-night'" 
                    class="flex-1 py-2.5 px-3 rounded-xl text-xs sm:text-sm transition flex items-center justify-center gap-1.5">
                <i class="fas fa-shield-halved text-gold-deep"></i>
                <span>নিরাপত্তা</span>
            </button>
        </div>

        <!-- Tab 1: Enrolled Courses & Application Tracker -->
        <div x-show="activeTab === 'courses'" x-cloak class="space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-[#e5ddcb]">
                <h2 class="text-lg font-black text-emerald-night flex items-center">
                    <i class="fas fa-book-open-reader text-gold-deep mr-2"></i> আমার ভর্তিকৃত কোর্স তালিকা
                </h2>
                <a href="<?= $baseUrl ?>/courses" class="text-xs text-gold-deep hover:text-emerald-night font-bold transition flex items-center">
                    <span>নতুন কোর্স দেখুন</span> <i class="fas fa-arrow-left ml-1 rotate-180 text-[10px]"></i>
                </a>
            </div>

            <?php if (!empty($enrolledCourses)): ?>
                <div class="grid grid-cols-1 gap-4">
                    <?php foreach ($enrolledCourses as $course): ?>
                        <div class="bg-[#fffefb] rounded-2xl border-2 border-emerald-600/30 p-5 shadow-sm hover:shadow-md transition">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-start space-x-3.5">
                                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-800 flex items-center justify-center text-xl shrink-0 border border-emerald-200 shadow-inner">
                                        <i class="fas fa-quran"></i>
                                    </div>
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2 mb-1">
                                            <span class="px-2 py-0.5 bg-[#f0ebd9] text-slate-700 text-[10px] font-bold rounded">
                                                কোড: <?= htmlspecialchars($course['course_code']) ?>
                                            </span>
                                            
                                            <!-- Status Badges -->
                                            <?php if ($course['status'] === 'enrolled'): ?>
                                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[11px] font-black rounded-full border border-emerald-300 flex items-center">
                                                    <i class="fas fa-check-circle mr-1 text-emerald-600"></i> ভর্তি অনুমোদিত ও সক্রিয়
                                                </span>
                                            <?php elseif ($course['status'] === 'contacted'): ?>
                                                <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-[11px] font-black rounded-full border border-blue-300 flex items-center">
                                                    <i class="fas fa-comments mr-1 text-blue-600"></i> যোগাযোগ করা হয়েছে
                                                </span>
                                            <?php elseif ($course['status'] === 'cancelled'): ?>
                                                <span class="px-2 py-0.5 bg-red-100 text-red-800 text-[11px] font-black rounded-full border border-red-300 flex items-center">
                                                    <i class="fas fa-times-circle mr-1 text-red-600"></i> বাতিল
                                                </span>
                                            <?php else: ?>
                                                <span class="px-2 py-0.5 bg-amber-100 text-amber-900 text-[11px] font-black rounded-full border border-amber-300 flex items-center animate-pulse">
                                                    <i class="fas fa-clock mr-1 text-amber-600"></i> আবেদন যাচাইাধীন (পেন্ডিং)
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <h3 class="text-base sm:text-lg font-black text-emerald-night">
                                            <?= htmlspecialchars($course['course_title']) ?>
                                        </h3>
                                        <p class="text-xs text-slate-600 mt-1 flex flex-wrap items-center gap-x-4 gap-y-1">
                                            <span><i class="fas fa-calendar-alt text-gold-deep mr-1"></i> সময়কাল: <?= htmlspecialchars($course['duration']) ?></span>
                                            <span><i class="fas fa-clock text-gold-deep mr-1"></i> সূচি: <?= htmlspecialchars($course['class_schedule'] ?: 'নির্ধারিত হবে') ?></span>
                                            <span><i class="fas fa-money-bill-wave text-emerald-600 mr-1"></i> ফি: <?= htmlspecialchars($course['fee']) ?></span>
                                        </p>
                                    </div>
                                </div>

                                <div class="sm:text-right shrink-0">
                                    <a href="<?= $baseUrl ?>/courses/<?= $course['course_slug'] ?>" class="inline-flex items-center px-4 py-2 bg-emerald-night text-amber-300 hover:bg-emerald-deep rounded-xl text-xs font-bold transition shadow">
                                        <i class="fas fa-info-circle mr-1.5"></i> কোর্স বিস্তারিত
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- No Course Banner -->
                <div class="bg-[#fffefb] rounded-3xl border-2 border-dashed border-[#d8cdb7] p-8 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-amber-50 text-gold-rich flex items-center justify-center text-3xl mx-auto mb-3 shadow-inner">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3 class="text-lg font-black text-emerald-night mb-1">আপনি এখনো কোনো কোর্সে আবেদন করেননি</h3>
                    <p class="text-xs text-slate-500 max-w-md mx-auto mb-5 leading-relaxed">
                        সহজ পদ্ধতিতে শুদ্ধভাবে কুরআন ও তাজবীদ শিখতে কারিয়ানা কুরআন শিক্ষা সোসাইটির উন্মুক্ত কোর্সসমূহে এখনই ভর্তি হোন।
                    </p>
                    <a href="<?= $baseUrl ?>/courses" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-night to-emerald-deep text-amber-300 hover:text-white rounded-2xl text-xs sm:text-sm font-bold shadow-lg transition transform hover:-translate-y-0.5">
                        <i class="fas fa-book-open mr-2"></i> সকল কোর্স দেখুন ও আবেদন করুন
                    </a>
                </div>

                <!-- Featured Available Courses -->
                <?php if (!empty($availableCourses)): ?>
                <div class="mt-6">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">জনপ্রিয় কোর্সসমূহ</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <?php foreach ($availableCourses as $ac): ?>
                        <div class="bg-[#fffefb] p-4 rounded-2xl border border-[#e4dccb] flex items-center justify-between gap-3 shadow-sm">
                            <div>
                                <h5 class="font-bold text-xs text-emerald-night"><?= htmlspecialchars($ac['title']) ?></h5>
                                <p class="text-[11px] text-slate-500 mt-0.5"><?= htmlspecialchars($ac['duration']) ?> • ফি: <?= htmlspecialchars($ac['fee']) ?></p>
                            </div>
                            <a href="<?= $baseUrl ?>/courses/<?= $ac['slug'] ?>" class="px-3 py-1.5 bg-emerald-50 text-emerald-800 hover:bg-emerald-night hover:text-white rounded-xl text-xs font-bold transition shrink-0">
                                ভর্তি হোন
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Tab 2: Profile Settings Form -->
        <div x-show="activeTab === 'profile'" x-cloak class="space-y-4">
            <div class="bg-[#fffefb] rounded-3xl border-2 border-emerald-600/30 p-6 shadow-sm">
                <h2 class="text-lg font-black text-emerald-night mb-1 flex items-center">
                    <i class="fas fa-user-edit text-gold-deep mr-2"></i> প্রোফাইল তথ্য হালনাগাদ
                </h2>
                <p class="text-xs text-slate-500 mb-6">আপনার ব্যক্তিগত ও যোগাযোগের তথ্য নির্ভুলভাবে আপডেট রাখুন।</p>

                <form action="<?= $baseUrl ?>/profile/update" method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">পূর্ণ নাম <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required 
                                   class="w-full px-4 py-2.5 rounded-xl border border-[#d6ccb9] text-xs font-bold bg-[#fdfbf7] focus:ring-2 focus:ring-gold-rich focus:border-gold-rich outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">মোবাইল নম্বর <span class="text-red-500">*</span></label>
                            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required 
                                   class="w-full px-4 py-2.5 rounded-xl border border-[#d6ccb9] text-xs font-bold bg-[#fdfbf7] focus:ring-2 focus:ring-gold-rich focus:border-gold-rich outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">নিজ জেলা</label>
                            <select name="district" class="w-full px-4 py-2.5 rounded-xl border border-[#d6ccb9] text-xs font-bold bg-[#fdfbf7] focus:ring-2 focus:ring-gold-rich focus:border-gold-rich outline-none">
                                <option value="">জেলা নির্বাচন করুন</option>
                                <?php foreach ($allDistricts as $d): ?>
                                <option value="<?= htmlspecialchars($d) ?>" <?= ($user['district'] ?? '') === $d ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($d) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">লিঙ্গ (Gender)</label>
                            <select name="gender" class="w-full px-4 py-2.5 rounded-xl border border-[#d6ccb9] text-xs font-bold bg-[#fdfbf7] focus:ring-2 focus:ring-gold-rich focus:border-gold-rich outline-none">
                                <option value="male" <?= ($user['gender'] ?? 'male') === 'male' ? 'selected' : '' ?>>পুরুষ</option>
                                <option value="female" <?= ($user['gender'] ?? 'male') === 'female' ? 'selected' : '' ?>>নারী</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">ঠিকানা / এলাকা</label>
                        <textarea name="address" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-[#d6ccb9] text-xs bg-[#fdfbf7] focus:ring-2 focus:ring-gold-rich focus:border-gold-rich outline-none" placeholder="আপনার বর্তমান ঠিকানা বা মাদরাসার ঠিকানা লিখুন..."><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-emerald-night via-emerald-deep to-emerald-night text-amber-300 hover:text-white rounded-2xl text-xs font-bold shadow-lg transition">
                            <i class="fas fa-save mr-1.5"></i> তথ্য সংরক্ষণ করুন
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tab 3: Security & Password Change -->
        <div x-show="activeTab === 'security'" x-cloak class="space-y-4">
            <div class="bg-[#fffefb] rounded-3xl border-2 border-emerald-600/30 p-6 shadow-sm max-w-lg">
                <h2 class="text-lg font-black text-emerald-night mb-1 flex items-center">
                    <i class="fas fa-lock text-gold-deep mr-2"></i> পাসওয়ার্ড পরিবর্তন
                </h2>
                <p class="text-xs text-slate-500 mb-6">আপনার অ্যাকাউন্টের সুরক্ষার জন্য শক্তিশালী পাসওয়ার্ড ব্যবহার করুন।</p>

                <form action="<?= $baseUrl ?>/profile/password" method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">বর্তমান পাসওয়ার্ড</label>
                        <input type="password" name="current_password" required 
                               class="w-full px-4 py-2.5 rounded-xl border border-[#d6ccb9] text-xs bg-[#fdfbf7] focus:ring-2 focus:ring-gold-rich focus:border-gold-rich outline-none" placeholder="••••••••">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">নতুন পাসওয়ার্ড (কমপক্ষে ৬ অক্ষর)</label>
                        <input type="password" name="new_password" required minlength="6"
                               class="w-full px-4 py-2.5 rounded-xl border border-[#d6ccb9] text-xs bg-[#fdfbf7] focus:ring-2 focus:ring-gold-rich focus:border-gold-rich outline-none" placeholder="••••••••">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">নতুন পাসওয়ার্ড নিশ্চিত করুন</label>
                        <input type="password" name="confirm_password" required minlength="6"
                               class="w-full px-4 py-2.5 rounded-xl border border-[#d6ccb9] text-xs bg-[#fdfbf7] focus:ring-2 focus:ring-gold-rich focus:border-gold-rich outline-none" placeholder="••••••••">
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full px-8 py-3 bg-emerald-night text-amber-300 hover:text-white rounded-2xl text-xs font-bold shadow-lg transition">
                            <i class="fas fa-key mr-1.5"></i> পাসওয়ার্ড আপডেট করুন
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
