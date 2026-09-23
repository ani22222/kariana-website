<?php
/**
 * Admin Dashboard View & Executive Directors Hub - Kariana Quran
 */
$user = \Core\Session::getUser();
$allDirectors = $allDirectors ?? [];
$divisions = $divisions ?? [];
$stats = $stats ?? [];
$recentAdmissions = $recentAdmissions ?? [];
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';
$success = \Core\Session::getFlash('success');
$error = \Core\Session::getFlash('error');
?>
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between pb-6 border-b border-slate-200 mb-8 gap-4">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-night text-xs font-bold rounded-full">সেন্ট্রাল অ্যাডমিন ড্যাশবোর্ড</span>
                <span class="inline-block px-3 py-1 bg-amber-100 text-amber-900 text-xs font-bold rounded-full">৫৯ জেলা পরিচালক নেটওয়ার্ক</span>
                <span class="inline-block px-2.5 py-1 bg-gold-rich text-white text-[11px] font-extrabold rounded-full shadow-sm">মালিকানা প্যানেল</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-emerald-night">কারিয়ানা কুরআন কন্ট্রোল প্যানেল</h1>
            <div class="flex items-center gap-3 mt-2.5">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-night to-emerald-700 text-amber-300 flex items-center justify-center font-bold text-sm shadow border border-gold-rich/40 shrink-0">
                    মৌ
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-extrabold text-slate-900 text-base"><?= htmlspecialchars($user['name'] ?? 'মাওলানা সাদ্দাম হোসেন') ?></span>
                        <span class="px-2 py-0.5 rounded-full bg-gold-rich text-white text-[10px] font-black uppercase tracking-wider shadow-sm">
                            <?= htmlspecialchars($user['title'] ?? 'প্রতিষ্ঠানের মালিক ও প্রতিষ্ঠাতা') ?>
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 font-mono mt-0.5">
                        মোবাইল: <strong class="text-emerald-night"><?= htmlspecialchars($user['phone'] ?? '01717056816') ?></strong> | কেন্দ্রীয় ডাটাবেস ও সকল পরিচালকের একক ড্যাশবোর্ড কমান্ড সেন্টার
                    </p>
                </div>
            </div>
        </div>
        <div class="flex flex-wrap gap-2.5">
            <a href="#directors-hub" class="bg-emerald-night hover:bg-emerald-deep text-white border border-emerald-700 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold shadow transition flex items-center">
                <svg class="w-4 h-4 mr-2 text-gold-rich" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                সকল পরিচালক ও ড্যাশবোর্ড (৫৯)
            </a>
            <a href="<?= $baseUrl ?>/admin/settings" class="bg-gold-rich hover:bg-gold-deep text-white px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold shadow transition flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
                মার্কেটিং সেটিংস
            </a>
            <a href="<?= $baseUrl ?>/admin/logout" class="bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                লগআউট
            </a>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl text-emerald-800 text-sm mb-6 flex items-center shadow-sm">
            <svg class="w-5 h-5 mr-2.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span><?= htmlspecialchars($success) ?></span>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl text-red-800 text-sm mb-6 flex items-center shadow-sm">
            <svg class="w-5 h-5 mr-2.5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <!-- Quick Navigation Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5 mb-8">
        <a href="#directors-hub" class="bg-gradient-to-br from-emerald-night to-emerald-deep text-white p-4 rounded-2xl shadow-sm border border-emerald-700 hover:shadow-md hover:scale-[1.02] transition flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-gold-rich text-white flex items-center justify-center font-bold shrink-0 shadow">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-sm text-white">জেলা পরিচালকবৃন্দ</h4>
                <p class="text-xs text-amber-200">৫৯ একক ড্যাশবোর্ড</p>
            </div>
        </a>

        <a href="<?= $baseUrl ?>/admin/books" class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 hover:border-emerald-vibrant hover:shadow-md hover:scale-[1.02] transition flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-night flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 text-sm">বই ও প্রকাশনা</h4>
                <p class="text-xs text-slate-400">হিরো ও ক্যাটালগ</p>
            </div>
        </a>

        <a href="<?= $baseUrl ?>/admin/admissions" class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 hover:border-emerald-vibrant hover:shadow-md hover:scale-[1.02] transition flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 text-sm">ভর্তি আবেদন</h4>
                <p class="text-xs text-slate-400"><?= \Core\BengaliHelper::toBengaliNumber($stats['admissions'] ?? 0) ?> আবেদন</p>
            </div>
        </a>

        <a href="<?= $baseUrl ?>/admin/courses" class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 hover:border-emerald-vibrant hover:shadow-md hover:scale-[1.02] transition flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-night flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 text-sm">কোর্স ক্যাটালগ</h4>
                <p class="text-xs text-slate-400">কোর্স ও কারিকুলাম</p>
            </div>
        </a>

        <a href="<?= $baseUrl ?>/admin/posts" class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 hover:border-emerald-vibrant hover:shadow-md hover:scale-[1.02] transition flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 text-sm">ব্লগ ও নিউজ</h4>
                <p class="text-xs text-slate-400">ইসলামী কন্টেন্ট</p>
            </div>
        </a>
    </div>

    <!-- Stat Counters (6-Metric Central Overview) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5 mb-10">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">জেলা পরিচালক</p>
            <h3 class="text-2xl font-extrabold text-emerald-night mt-1"><?= \Core\BengaliHelper::toBengaliNumber($stats['directors'] ?? count($allDirectors)) ?> জন</h3>
            <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full inline-block mt-1.5">১০০% কর্মরত 🟢</span>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">আওতাধীন শিক্ষক</p>
            <h3 class="text-2xl font-extrabold text-emerald-night mt-1"><?= \Core\BengaliHelper::toBengaliNumber($stats['teachers'] ?? 0) ?> জন</h3>
            <span class="text-[11px] font-semibold text-slate-500 inline-block mt-1.5">তাজবীদ ও সবক</span>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">ভর্তি আবেদন</p>
            <h3 class="text-2xl font-extrabold text-gold-deep mt-1"><?= \Core\BengaliHelper::toBengaliNumber($stats['admissions'] ?? 0) ?></h3>
            <span class="text-[11px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full inline-block mt-1.5">অনলাইন লিডস</span>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">প্রকাশনা বই</p>
            <h3 class="text-2xl font-extrabold text-emerald-night mt-1"><?= \Core\BengaliHelper::toBengaliNumber($stats['books'] ?? 0) ?></h3>
            <span class="text-[11px] font-semibold text-slate-500 inline-block mt-1.5">কারিয়ানা কুরআন</span>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">মোট কোর্স</p>
            <h3 class="text-2xl font-extrabold text-emerald-night mt-1"><?= \Core\BengaliHelper::toBengaliNumber($stats['courses'] ?? 0) ?></h3>
            <span class="text-[11px] font-semibold text-slate-500 inline-block mt-1.5">হিফজ ও তাজবীদ</span>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">ব্লগ ও প্রবন্ধ</p>
            <h3 class="text-2xl font-extrabold text-emerald-night mt-1"><?= \Core\BengaliHelper::toBengaliNumber($stats['posts'] ?? 0) ?></h3>
            <span class="text-[11px] font-semibold text-slate-500 inline-block mt-1.5">প্রকাশিত পোস্ট</span>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- DEDICATED DIRECTORS HUB (সকল পরিচালকের লিস্ট ও যাচাই-বাছাই কমান্ড সেন্টার) -->
    <!-- ========================================================================= -->
    <section id="directors-hub" class="bg-[#fffefb] rounded-3xl shadow-sm border-2 border-[#e6dece] overflow-hidden mb-12">
        <!-- Section Header -->
        <div class="bg-gradient-to-r from-emerald-night via-emerald-deep to-emerald-night px-6 py-6 text-white flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                    <span class="px-2.5 py-0.5 rounded-full bg-gold-rich text-white text-xs font-black uppercase tracking-wider shadow">মালিকানা যাচাই হাব</span>
                    <span class="text-xs text-emerald-100/90 font-medium">বাংলাদেশব্যাপী ৫৯ জেলা নেতৃত্ব তালিকা</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-white flex items-center">
                    <svg class="w-6 h-6 mr-2.5 text-gold-rich shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    পরিচালক তালিকা যাচাই, অনুমোদন ও স্ট্যাটাস ম্যানেজমেন্ট
                </h2>
                <p class="text-xs sm:text-sm text-emerald-100/90 mt-1">
                    হযরত মাওলানা সাদ্দাম হোসেন—প্রতিটি পরিচালকের নামের পাশে <strong>বহাল / বাতিল / স্থগিত</strong> সিলেক্ট করে তালিকা চূড়ান্ত করুন।
                </p>
            </div>

            <!-- Instant Switcher & CSV Download Buttons -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                <div class="relative">
                    <select id="quickDirectorSelect" onchange="if(this.value) window.location.href=this.value" class="w-full sm:w-72 bg-emerald-950/90 hover:bg-emerald-950 text-white font-bold text-xs py-2.5 px-3 rounded-xl border border-gold-rich/50 shadow-inner focus:outline-none focus:ring-2 focus:ring-gold-rich transition cursor-pointer">
                        <option value="">⚡ যেকোনো পরিচালকের ড্যাশবোর্ড (৫৯)...</option>
                        <?php foreach ($allDirectors as $dirOption): ?>
                            <option value="<?= $baseUrl ?>/admin/directors/impersonate/<?= $dirOption['id'] ?>">
                                [ID #<?= $dirOption['id'] ?>] <?= htmlspecialchars($dirOption['district_name']) ?> — <?= htmlspecialchars($dirOption['name']) ?> (<?= htmlspecialchars($dirOption['division_name']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <a href="<?= $baseUrl ?>/admin/directors/export" class="bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white px-3.5 py-2.5 rounded-xl text-xs font-extrabold transition shadow flex items-center justify-center shrink-0" title="চূড়ান্ত যাচাইকৃত তালিকা এক্সেল CSV ফরম্যাটে ডাউনলোড করুন">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    তালিকা ডাউনলোড (CSV)
                </a>
            </div>
        </div>

        <!-- Real-time Status Counters Bar -->
        <div class="bg-slate-50 border-b border-[#e6dece] px-6 py-3 flex items-center justify-between flex-wrap gap-2 text-xs">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="font-extrabold text-slate-700">বর্তমান অবস্থা:</span>
                <span class="px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-900 font-extrabold flex items-center shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-600 mr-1.5 animate-pulse"></span>
                    বহাল / অনুমোদিত: <span id="counterActive" class="ml-1 font-black"><?= \Core\BengaliHelper::toBengaliNumber($statusCounts['active'] ?? 0) ?></span> জন
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-red-100 text-red-900 font-extrabold flex items-center shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-red-600 mr-1.5"></span>
                    বাতিল / বহিষ্কৃত: <span id="counterExpelled" class="ml-1 font-black"><?= \Core\BengaliHelper::toBengaliNumber($statusCounts['expelled'] ?? 0) ?></span> জন
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-amber-100 text-amber-900 font-extrabold flex items-center shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-amber-600 mr-1.5"></span>
                    সাময়িক স্থগিত: <span id="counterSuspended" class="ml-1 font-black"><?= \Core\BengaliHelper::toBengaliNumber($statusCounts['suspended'] ?? 0) ?></span> জন
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-slate-200 text-slate-700 font-extrabold flex items-center shadow-xs">
                    নিষ্ক্রিয়: <span id="counterInactive" class="ml-1 font-black"><?= \Core\BengaliHelper::toBengaliNumber($statusCounts['inactive'] ?? 0) ?></span> জন
                </span>
            </div>
            <div class="text-slate-500 font-medium">
                সর্বমোট পরিচালক: <strong><?= \Core\BengaliHelper::toBengaliNumber(count($allDirectors)) ?> জন</strong>
            </div>
        </div>

        <!-- Interactive Search & Division Filtering Bar -->
        <div class="p-6 border-b border-[#e6dece] bg-white space-y-4">
            <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                <!-- Search Box -->
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" id="directorSearchInput" onkeyup="filterDirectors()" placeholder="পরিচালকের নাম, দায়িত্বপ্রাপ্ত জেলা, বা মোবাইল নম্বর লিখুন..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-vibrant/40 focus:border-emerald-vibrant transition shadow-sm bg-slate-50/50 hover:bg-white" />
                </div>

                <!-- View Mode Toggle (Grid / Table) & Select All -->
                <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                    <label class="inline-flex items-center text-xs font-bold text-slate-700 cursor-pointer bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-lg border border-slate-200 select-none">
                        <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)" class="w-4 h-4 text-emerald-night rounded mr-1.5 focus:ring-0 cursor-pointer">
                        <span>সবাইকে নির্বাচন করুন</span>
                    </label>

                    <span class="text-slate-300">|</span>

                    <button type="button" id="btnGridView" onclick="setViewMode('grid')" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-night text-white shadow-sm transition flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        কার্ড ভিউ
                    </button>
                    <button type="button" id="btnTableView" onclick="setViewMode('table')" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        টেবিল তালিকা
                    </button>
                    <span id="directorCountBadge" class="text-xs font-extrabold text-emerald-night bg-emerald-100 px-3 py-1 rounded-full">
                        <?= \Core\BengaliHelper::toBengaliNumber(count($allDirectors)) ?> জন
                    </span>
                </div>
            </div>

            <!-- Division Filter Pills -->
            <div class="flex flex-wrap items-center gap-1.5 pt-1">
                <span class="text-xs font-bold text-slate-500 mr-1.5">বিভাগ ফিল্টার:</span>
                <button type="button" onclick="filterByDivision('all', this)" class="div-pill px-3 py-1 rounded-lg text-xs font-bold transition bg-emerald-night text-white shadow-sm">
                    সকল বিভাগ (<?= \Core\BengaliHelper::toBengaliNumber(count($allDirectors)) ?>)
                </button>
                <?php foreach ($divisions as $divName): ?>
                    <?php 
                    $divCount = count(array_filter($allDirectors, fn($d) => trim((string)($d['division_name'] ?? '')) === $divName));
                    ?>
                    <button type="button" onclick="filterByDivision('<?= htmlspecialchars($divName) ?>', this)" class="div-pill px-3 py-1 rounded-lg text-xs font-bold transition bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-emerald-night border border-slate-200/80">
                        <?= htmlspecialchars($divName) ?> (<?= \Core\BengaliHelper::toBengaliNumber($divCount) ?>)
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Sticky Floating Bulk Action Bar (Visible when >= 1 director selected) -->
        <div id="bulkActionsToolbar" class="hidden bg-emerald-950 text-white px-6 py-3 border-y-2 border-gold-rich flex items-center justify-between flex-wrap gap-3 sticky top-16 z-30 shadow-2xl transition-all duration-300">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-gold-rich animate-ping"></span>
                <span class="text-xs sm:text-sm font-black text-amber-300">
                    <span id="selectedCountText">০</span> জন পরিচালক নির্বাচিত
                </span>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" onclick="applyBulkAction('set_active')" class="bg-emerald-700 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition shadow flex items-center">
                    🟢 বহাল রাখুন
                </button>
                <button type="button" onclick="applyBulkAction('set_expelled')" class="bg-red-700 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition shadow flex items-center">
                    🔴 বাতিল / বহিষ্কার
                </button>
                <button type="button" onclick="applyBulkAction('set_suspended')" class="bg-amber-700 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition shadow flex items-center">
                    🟡 স্থগিত করুন
                </button>
                <button type="button" onclick="applyBulkAction('delete')" class="bg-slate-800 hover:bg-red-900 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition border border-red-500/50 flex items-center">
                    🗑️ সম্পূর্ণ মুছুন
                </button>
            </div>
        </div>

        <!-- 1. GRID VIEW OF DIRECTORS -->
        <div id="directorsGridView" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" id="directorsCardContainer">
                <?php foreach ($allDirectors as $dir): ?>
                    <?php 
                    $slug = $dir['slug'] ?? (string)$dir['id'];
                    $teachersCount = (int)($dir['teachers_count'] ?? 0);
                    $division = trim((string)($dir['division_name'] ?? ''));
                    $district = trim((string)($dir['district_name'] ?? ''));
                    $phone = trim((string)($dir['phone'] ?? ''));
                    $status = $dir['status'] ?? 'active';
                    $loginAllowed = (int)($dir['login_allowed'] ?? 1);
                    $reason = trim((string)($dir['status_reason'] ?? ''));
                    $remarks = trim((string)($dir['admin_remarks'] ?? ''));

                    $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                    if (str_starts_with($cleanPhone, '88')) $cleanPhone = '+' . $cleanPhone;
                    elseif (str_starts_with($cleanPhone, '01')) $cleanPhone = '+88' . $cleanPhone;

                    // Director JS Data Object for modal
                    $dirJson = htmlspecialchars(json_encode([
                        'id'            => $dir['id'],
                        'name'          => $dir['name'],
                        'district'      => $district,
                        'division'      => $division,
                        'phone'         => $phone,
                        'status'        => $status,
                        'login_allowed' => $loginAllowed,
                        'status_reason' => $reason,
                        'admin_remarks' => $remarks,
                        'teachers'      => $teachersCount,
                    ], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
                    ?>
                    <div class="director-card bg-white rounded-2xl border border-slate-200/90 hover:border-emerald-500 shadow-sm hover:shadow-lg transition-all duration-200 flex flex-col justify-between overflow-hidden group relative"
                         id="director-card-<?= $dir['id'] ?>"
                         data-id="<?= $dir['id'] ?>"
                         data-name="<?= strtolower(htmlspecialchars($dir['name'])) ?>"
                         data-district="<?= strtolower(htmlspecialchars($district)) ?>"
                         data-division="<?= htmlspecialchars($division) ?>"
                         data-phone="<?= htmlspecialchars($phone) ?>"
                         data-status="<?= $status ?>">
                        
                        <!-- Top Card Banner -->
                        <div class="p-5 pb-3">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" value="<?= $dir['id'] ?>" onchange="updateBulkToolbar()" class="dir-checkbox w-4 h-4 text-emerald-night rounded focus:ring-0 cursor-pointer">
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-night text-[11px] font-extrabold">
                                        ID #<?= $dir['id'] ?>
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-900 border border-amber-200 text-[11px] font-bold">
                                        <?= htmlspecialchars($division) ?>
                                    </span>
                                </div>

                                <!-- Dynamic Status Badge -->
                                <div class="flex items-center gap-1">
                                    <span id="badge-status-<?= $dir['id'] ?>" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-black <?= $status === 'active' ? 'bg-emerald-100 text-emerald-900' : ($status === 'expelled' ? 'bg-red-100 text-red-900' : 'bg-amber-100 text-amber-900') ?>">
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 <?= $status === 'active' ? 'bg-emerald-600' : ($status === 'expelled' ? 'bg-red-600' : 'bg-amber-600') ?>"></span>
                                        <span id="badge-text-<?= $dir['id'] ?>">
                                            <?= $status === 'active' ? 'বহাল' : ($status === 'expelled' ? 'বাতিল' : ($status === 'suspended' ? 'স্থগিত' : 'নিষ্ক্রিয়')) ?>
                                        </span>
                                    </span>
                                    <?php if ($loginAllowed === 0): ?>
                                        <span id="badge-login-<?= $dir['id'] ?>" class="px-1.5 py-0.5 rounded-full bg-slate-800 text-white text-[10px] font-bold" title="ড্যাশবোর্ড লগইন বন্ধ">🔒</span>
                                    <?php else: ?>
                                        <span id="badge-login-<?= $dir['id'] ?>" class="hidden"></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Director Identity -->
                            <div class="flex items-start space-x-3 mb-3">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-night via-emerald-deep to-emerald-700 text-gold-rich font-bold text-lg flex items-center justify-center shrink-0 shadow border border-emerald-600/40">
                                    <?= mb_substr($dir['name'], 0, 1) ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-extrabold text-slate-900 text-base leading-snug group-hover:text-emerald-night transition truncate">
                                        <?= htmlspecialchars($dir['name']) ?>
                                    </h3>
                                    <p class="text-xs text-gold-deep font-semibold truncate mt-0.5">
                                        <?= htmlspecialchars($dir['designation']) ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Area / District & Phone -->
                            <div class="bg-slate-50/80 rounded-xl p-3 border border-slate-100 space-y-2 text-xs">
                                <div class="flex items-start text-slate-700">
                                    <svg class="w-3.5 h-3.5 text-gold-deep mr-1.5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span class="font-semibold text-slate-900">এলাকা:</span>
                                    <span class="ml-1 text-slate-600 line-clamp-1"><?= htmlspecialchars($district) ?></span>
                                </div>
                                <div class="flex items-center justify-between text-slate-700">
                                    <div class="flex items-center">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 mr-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        <span class="font-mono text-slate-800 font-bold"><?= htmlspecialchars($phone) ?></span>
                                    </div>
                                    <span class="text-[11px] font-bold text-emerald-night bg-emerald-100/70 px-2 py-0.5 rounded-md">
                                        👥 শিক্ষক: <?= \Core\BengaliHelper::toBengaliNumber($teachersCount) ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Reason / Note if set -->
                            <div id="reason-box-<?= $dir['id'] ?>" class="<?= !empty($reason) ? '' : 'hidden' ?> mt-2 bg-amber-50 text-amber-900 border border-amber-200/80 px-2.5 py-1 rounded-lg text-[11px]">
                                <span class="font-bold">নোট:</span> <span id="reason-text-<?= $dir['id'] ?>"><?= htmlspecialchars($reason) ?></span>
                            </div>

                            <!-- ⚡ INSTANT 1-CLICK REVIEW BUTTONS (FOR HUZUR / MAIN OWNER) -->
                            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between gap-1.5">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">হুজুরের সিদ্ধান্ত:</span>
                                <div class="flex items-center gap-1">
                                    <button type="button" onclick="quickSetStatus(<?= $dir['id'] ?>, 'active')" class="btn-quick-status px-2.5 py-1 rounded-md text-[11px] font-extrabold transition shadow-xs <?= $status === 'active' ? 'bg-emerald-700 text-white' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-200' ?>" title="পরিচালক পদে বহাল ও সক্রিয় রাখুন">
                                        ✓ বহাল
                                    </button>
                                    <button type="button" onclick="quickSetStatus(<?= $dir['id'] ?>, 'expelled')" class="btn-quick-status px-2.5 py-1 rounded-md text-[11px] font-extrabold transition shadow-xs <?= $status === 'expelled' ? 'bg-red-700 text-white' : 'bg-red-50 text-red-800 hover:bg-red-200' ?>" title="তালিকা থেকে বাতিল বা বহিষ্কার করুন">
                                        ✕ বাতিল
                                    </button>
                                    <button type="button" onclick="quickSetStatus(<?= $dir['id'] ?>, 'suspended')" class="btn-quick-status px-2.5 py-1 rounded-md text-[11px] font-extrabold transition shadow-xs <?= $status === 'suspended' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-800 hover:bg-amber-200' ?>" title="সাময়িকভাবে স্থগিত রাখুন">
                                        ⏸ স্থগিত
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Card Action Buttons (Direct 1-Click Dashboard Access & Management) -->
                        <div class="px-5 py-3 bg-slate-50/90 border-t border-slate-100 flex items-center gap-2">
                            <!-- Main 1-Click Director Dashboard Switch -->
                            <a href="<?= $baseUrl ?>/admin/directors/impersonate/<?= $dir['id'] ?>" class="flex-1 bg-gradient-to-r from-emerald-night to-emerald-deep hover:from-emerald-deep hover:to-emerald-800 text-white py-2 px-2.5 rounded-xl text-xs font-extrabold shadow-sm transition flex items-center justify-center gap-1 border border-emerald-700/60" title="সরাসরি এই পরিচালকের একক ড্যাশবোর্ডে প্রবেশ করুন">
                                <svg class="w-3.5 h-3.5 text-gold-rich" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>ড্যাশবোর্ড</span>
                            </a>

                            <!-- Manage & Edit Notes Button (Opens Modal) -->
                            <button type="button" onclick='openDirectorModal(<?= $dirJson ?>)' class="bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 py-2 px-2.5 rounded-xl text-xs font-extrabold transition flex items-center justify-center shrink-0" title="স্ট্যাটাস ও বিস্তারিত নোট পরিবর্তন করুন">
                                <svg class="w-3.5 h-3.5 mr-1 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span>ম্যানেজ</span>
                            </button>

                            <!-- Single Delete Button -->
                            <button type="button" onclick="confirmDeleteDirector(<?= $dir['id'] ?>, '<?= htmlspecialchars($dir['name']) ?>')" class="bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 py-2 px-2.5 rounded-xl text-xs font-bold transition flex items-center justify-center shrink-0" title="পরিচালককে সম্পূর্ণ তালিকা থেকে বাদ দিন">
                                <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Zero State / Not Found -->
            <div id="noDirectorsFound" class="hidden text-center py-16 bg-white rounded-2xl border border-dashed border-slate-300">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h4 class="text-base font-bold text-slate-700">কোনো জেলা পরিচালক পাওয়া যায়নি</h4>
                <p class="text-xs text-slate-400 mt-1">অনুগ্রহ করে বানান যাচাই করুন অথবা অন্য কোনো ফিল্টার ব্যবহার করুন।</p>
            </div>
        </div>

        <!-- 2. TABLE VIEW OF DIRECTORS (Toggleable) -->
        <div id="directorsTableView" class="hidden overflow-x-auto p-6 pt-0 bg-white">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-3 py-3.5 text-center">
                            <input type="checkbox" onchange="toggleSelectAll(this)" class="w-4 h-4 text-emerald-night rounded focus:ring-0 cursor-pointer">
                        </th>
                        <th class="px-3 py-3.5 text-center">ID</th>
                        <th class="px-4 py-3.5">বিভাগ ও জেলা</th>
                        <th class="px-4 py-3.5">পরিচালকের নাম ও মোবাইল</th>
                        <th class="px-4 py-3.5 text-center">শিক্ষক</th>
                        <th class="px-4 py-3.5 text-center">বর্তমান অবস্থা</th>
                        <th class="px-4 py-3.5 text-center">হুজুরের দ্রুত সিদ্ধান্ত</th>
                        <th class="px-4 py-3.5 text-right">একশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($allDirectors as $dir): ?>
                        <?php 
                        $slug = $dir['slug'] ?? (string)$dir['id'];
                        $teachersCount = (int)($dir['teachers_count'] ?? 0);
                        $division = trim((string)($dir['division_name'] ?? ''));
                        $district = trim((string)($dir['district_name'] ?? ''));
                        $phone = trim((string)($dir['phone'] ?? ''));
                        $status = $dir['status'] ?? 'active';
                        $loginAllowed = (int)($dir['login_allowed'] ?? 1);
                        $reason = trim((string)($dir['status_reason'] ?? ''));
                        $remarks = trim((string)($dir['admin_remarks'] ?? ''));

                        $dirJson = htmlspecialchars(json_encode([
                            'id'            => $dir['id'],
                            'name'          => $dir['name'],
                            'district'      => $district,
                            'division'      => $division,
                            'phone'         => $phone,
                            'status'        => $status,
                            'login_allowed' => $loginAllowed,
                            'status_reason' => $reason,
                            'admin_remarks' => $remarks,
                            'teachers'      => $teachersCount,
                        ], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
                        ?>
                        <tr class="director-table-row hover:bg-slate-50/80 transition"
                            id="director-row-<?= $dir['id'] ?>"
                            data-name="<?= strtolower(htmlspecialchars($dir['name'])) ?>"
                            data-district="<?= strtolower(htmlspecialchars($district)) ?>"
                            data-division="<?= htmlspecialchars($division) ?>"
                            data-phone="<?= htmlspecialchars($phone) ?>">
                            <td class="px-3 py-3.5 text-center">
                                <input type="checkbox" value="<?= $dir['id'] ?>" onchange="updateBulkToolbar()" class="dir-checkbox w-4 h-4 text-emerald-night rounded focus:ring-0 cursor-pointer">
                            </td>
                            <td class="px-3 py-3.5 text-center font-bold text-slate-500 text-xs">#<?= $dir['id'] ?></td>
                            <td class="px-4 py-3.5">
                                <div class="font-extrabold text-slate-900 text-xs"><?= htmlspecialchars($district) ?></div>
                                <span class="text-[10px] text-slate-400"><?= htmlspecialchars($division) ?> বিভাগ</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-extrabold text-slate-900 text-sm"><?= htmlspecialchars($dir['name']) ?></div>
                                <div class="text-[11px] font-mono text-emerald-night font-bold"><?= htmlspecialchars($phone) ?></div>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="bg-emerald-100 text-emerald-night text-xs font-bold px-2 py-0.5 rounded-full">
                                    <?= \Core\BengaliHelper::toBengaliNumber($teachersCount) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span id="table-badge-<?= $dir['id'] ?>" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold <?= $status === 'active' ? 'bg-emerald-50 text-emerald-700' : ($status === 'expelled' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') ?>">
                                    <?= $status === 'active' ? 'বহাল' : ($status === 'expelled' ? 'বাতিল' : 'স্থগিত') ?>
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <button type="button" onclick="quickSetStatus(<?= $dir['id'] ?>, 'active')" class="px-2 py-0.5 rounded text-[11px] font-extrabold <?= $status === 'active' ? 'bg-emerald-700 text-white' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-200' ?>">
                                        বহাল
                                    </button>
                                    <button type="button" onclick="quickSetStatus(<?= $dir['id'] ?>, 'expelled')" class="px-2 py-0.5 rounded text-[11px] font-extrabold <?= $status === 'expelled' ? 'bg-red-700 text-white' : 'bg-red-50 text-red-800 hover:bg-red-200' ?>">
                                        বাতিল
                                    </button>
                                    <button type="button" onclick="quickSetStatus(<?= $dir['id'] ?>, 'suspended')" class="px-2 py-0.5 rounded text-[11px] font-extrabold <?= $status === 'suspended' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-800 hover:bg-amber-200' ?>">
                                        স্থগিত
                                    </button>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-right space-x-1">
                                <a href="<?= $baseUrl ?>/admin/directors/impersonate/<?= $dir['id'] ?>" class="inline-flex items-center px-2.5 py-1 bg-emerald-night hover:bg-emerald-deep text-white text-xs font-bold rounded-lg transition" title="ড্যাশবোর্ডে প্রবেশ">
                                    ড্যাশবোর্ড
                                </a>
                                <button type="button" onclick='openDirectorModal(<?= $dirJson ?>)' class="inline-flex items-center px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 text-xs font-extrabold rounded-lg transition" title="নোট ও ম্যানেজমেন্ট">
                                    ম্যানেজ
                                </button>
                                <button type="button" onclick="confirmDeleteDirector(<?= $dir['id'] ?>, '<?= htmlspecialchars($dir['name']) ?>')" class="inline-flex items-center px-2 py-1 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 text-xs font-bold rounded-lg transition" title="মুছুন">
                                    মুছুন
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Recent Admissions Table -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div>
                <h3 class="text-base font-bold text-emerald-night">সর্বশেষ অনলাইন ভর্তি আবেদন ও অনুসন্ধান</h3>
                <p class="text-xs text-slate-500">শিক্ষার্থী ও অভিভাবকদের সরাসরি আবেদন তালিকা</p>
            </div>
            <a href="<?= $baseUrl ?>/admin/admissions" class="text-xs font-bold text-emerald-vibrant hover:text-emerald-night flex items-center">
                সবগুলো দেখুন 
                <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3">শিক্ষার্থী নাম</th>
                        <th class="px-6 py-3">কোর্স</th>
                        <th class="px-6 py-3">মোবাইল নম্বর</th>
                        <th class="px-6 py-3">জেলা</th>
                        <th class="px-6 py-3">আবেদনের তারিখ</th>
                        <th class="px-6 py-3">অবস্থা</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($recentAdmissions)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                                <svg class="w-10 h-10 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p>এখনও কোনো নতুন ভর্তি আবেদন জমা পড়েনি।</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentAdmissions as $adm): ?>
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-6 py-4 font-bold text-slate-800"><?= htmlspecialchars($adm['student_name']) ?></td>
                                <td class="px-6 py-4 text-emerald-night font-semibold"><?= htmlspecialchars($adm['course_title'] ?? 'সাধারণ') ?></td>
                                <td class="px-6 py-4 font-mono"><?= htmlspecialchars($adm['phone']) ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($adm['district']) ?></td>
                                <td class="px-6 py-4 text-xs text-slate-400"><?= date('d M Y, h:i A', strtotime($adm['created_at'])) ?></td>
                                <td class="px-6 py-4">
                                    <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">নতুন</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============================================================
     DIRECTOR MANAGEMENT MODAL (হুজুর ও মূল অ্যাডমিনের বিস্তারিত নিয়ন্ত্রণ)
     ============================================================ -->
<div id="directorModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden border-2 border-gold-rich/50 animate-scale-up" onclick="event.stopPropagation()">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-emerald-night to-emerald-deep text-white px-6 py-5 flex items-center justify-between">
            <div>
                <span class="px-2 py-0.5 rounded-full bg-gold-rich text-white text-[10px] font-black uppercase tracking-wider">পরিচালক নিয়ন্ত্রণ</span>
                <h3 class="text-lg font-black text-white mt-1" id="modalDirectorName">পরিচালকের নাম</h3>
                <p class="text-xs text-emerald-200/90" id="modalDirectorArea">জেলা ও এলাকা</p>
            </div>
            <button type="button" onclick="closeDirectorModal()" class="text-emerald-200 hover:text-white text-2xl font-bold focus:outline-none">&times;</button>
        </div>

        <!-- Modal Form -->
        <form id="directorStatusForm" onsubmit="submitDirectorStatusForm(event)" class="p-6 space-y-4">
            <input type="hidden" name="director_id" id="modalDirectorId">
            <input type="hidden" name="ajax" value="1">

            <!-- Status Radio Pills -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">হুজুরের সিদ্ধান্ত / বর্তমান অবস্থা *</label>
                <div class="grid grid-cols-2 gap-2 text-xs font-bold">
                    <label class="flex items-center p-2.5 rounded-xl border border-slate-200 hover:border-emerald-500 cursor-pointer bg-emerald-50/50 has-checked:bg-emerald-100 has-checked:border-emerald-600 transition">
                        <input type="radio" name="status" value="active" id="statusRadioActive" class="w-4 h-4 text-emerald-600 focus:ring-0 mr-2">
                        <span>🟢 বহাল / অনুমোদিত</span>
                    </label>

                    <label class="flex items-center p-2.5 rounded-xl border border-slate-200 hover:border-red-500 cursor-pointer bg-red-50/50 has-checked:bg-red-100 has-checked:border-red-600 transition">
                        <input type="radio" name="status" value="expelled" id="statusRadioExpelled" class="w-4 h-4 text-red-600 focus:ring-0 mr-2">
                        <span>🔴 বাতিল / বহিষ্কৃত</span>
                    </label>

                    <label class="flex items-center p-2.5 rounded-xl border border-slate-200 hover:border-amber-500 cursor-pointer bg-amber-50/50 has-checked:bg-amber-100 has-checked:border-amber-600 transition">
                        <input type="radio" name="status" value="suspended" id="statusRadioSuspended" class="w-4 h-4 text-amber-600 focus:ring-0 mr-2">
                        <span>🟡 সাময়িক স্থগিত</span>
                    </label>

                    <label class="flex items-center p-2.5 rounded-xl border border-slate-200 hover:border-slate-400 cursor-pointer bg-slate-50 has-checked:bg-slate-200 has-checked:border-slate-500 transition">
                        <input type="radio" name="status" value="inactive" id="statusRadioInactive" class="w-4 h-4 text-slate-600 focus:ring-0 mr-2">
                        <span>⚪ অব্যাহতি / নিষ্ক্রিয়</span>
                    </label>
                </div>
            </div>

            <!-- Login Access Toggle -->
            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 flex items-center justify-between">
                <div>
                    <label for="modalLoginAllowed" class="text-xs font-bold text-slate-900 block cursor-pointer">ড্যাশবোর্ড লগইন এক্সেস</label>
                    <span class="text-[11px] text-slate-500">পরিচালক তার প্যানেলে লগইন করতে পারবেন কি না</span>
                </div>
                <input type="checkbox" name="login_allowed" value="1" id="modalLoginAllowed" class="w-5 h-5 text-emerald-night rounded focus:ring-0 cursor-pointer">
            </div>

            <!-- Status Reason / Order Note -->
            <div>
                <label for="modalStatusReason" class="block text-xs font-bold text-slate-700 mb-1">বহিষ্কার / স্থগিত / পরিবর্তনের কারণ (ঐচ্ছিক)</label>
                <input type="text" name="status_reason" id="modalStatusReason" placeholder="যেমন: ব্যক্তিগত কারণে অব্যাহতি, বা তদন্তাধীন স্থগিত..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-slate-50/50 focus:bg-white transition">
            </div>

            <!-- Admin Internal Remarks / Performance Notes -->
            <div>
                <label for="modalAdminRemarks" class="block text-xs font-bold text-slate-700 mb-1">মালিকের নিজস্ব পর্যবেক্ষণ ও রিপোর্ট (গোপনীয়)</label>
                <textarea name="admin_remarks" id="modalAdminRemarks" rows="2" placeholder="মেইন মালিকের নিজস্ব অভ্যন্তরীণ মন্তব্য..." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-slate-50/50 focus:bg-white transition"></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                <button type="button" onclick="deleteFromModal()" class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-xl text-xs font-bold transition flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    তালিকা থেকে বাদ দিন
                </button>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeDirectorModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">
                        বাতিল
                    </button>
                    <button type="submit" id="btnModalSave" class="px-5 py-2 bg-gradient-to-r from-emerald-night to-emerald-deep hover:from-emerald-deep hover:to-emerald-800 text-white rounded-xl text-xs font-black shadow-md transition flex items-center">
                        <span>সংরক্ষণ করুন</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Floating Toast Notification -->
<div id="toastMessage" class="hidden fixed bottom-6 right-6 z-50 bg-emerald-950 text-white px-5 py-3 rounded-2xl shadow-2xl border-2 border-gold-rich flex items-center gap-3 transition-all duration-300">
    <div class="w-7 h-7 rounded-full bg-gold-rich text-white flex items-center justify-center font-bold text-xs shrink-0" id="toastIcon">✓</div>
    <div class="text-xs font-bold" id="toastText">আপডেট সফলভাবে সম্পন্ন হয়েছে।</div>
</div>

<!-- ============================================================
     REAL-TIME JAVASCRIPT ENGINE FOR DIRECTORS STATUS MANAGEMENT
     ============================================================ -->
<script>
let currentDivision = 'all';
const BASE_URL = '<?= $baseUrl ?>';

// Show Toast Notification
function showToast(message, isSuccess = true) {
    const toast = document.getElementById('toastMessage');
    const toastText = document.getElementById('toastText');
    const toastIcon = document.getElementById('toastIcon');
    if (!toast) return;

    toastText.innerText = message;
    if (isSuccess) {
        toast.className = 'fixed bottom-6 right-6 z-50 bg-emerald-950 text-white px-5 py-3 rounded-2xl shadow-2xl border-2 border-emerald-500 flex items-center gap-3';
        toastIcon.innerText = '✓';
        toastIcon.className = 'w-7 h-7 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-xs shrink-0';
    } else {
        toast.className = 'fixed bottom-6 right-6 z-50 bg-red-950 text-white px-5 py-3 rounded-2xl shadow-2xl border-2 border-red-500 flex items-center gap-3';
        toastIcon.innerText = '✕';
        toastIcon.className = 'w-7 h-7 rounded-full bg-red-500 text-white flex items-center justify-center font-bold text-xs shrink-0';
    }
    toast.classList.remove('hidden');
    setTimeout(() => { toast.classList.add('hidden'); }, 4000);
}

// Convert English numbers to Bengali numerals
function toBnNum(num) {
    const bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
    return String(num).replace(/[0-9]/g, d => bn[d]);
}

// Recalculate Live Status Counters
function refreshCounters() {
    const cards = document.querySelectorAll('.director-card:not(.hidden)');
    let active = 0, suspended = 0, expelled = 0, inactive = 0;

    cards.forEach(card => {
        const st = card.getAttribute('data-status');
        if (st === 'active') active++;
        else if (st === 'suspended') suspended++;
        else if (st === 'expelled') expelled++;
        else if (st === 'inactive') inactive++;
    });

    const cActive = document.getElementById('counterActive');
    const cSuspended = document.getElementById('counterSuspended');
    const cExpelled = document.getElementById('counterExpelled');
    const cInactive = document.getElementById('counterInactive');

    if (cActive) cActive.innerText = toBnNum(active);
    if (cSuspended) cSuspended.innerText = toBnNum(suspended);
    if (cExpelled) cExpelled.innerText = toBnNum(expelled);
    if (cInactive) cInactive.innerText = toBnNum(inactive);
}

// ⚡ INSTANT 1-CLICK STATUS UPDATE (Called directly from card / row buttons)
function quickSetStatus(directorId, newStatus) {
    const card = document.getElementById('director-card-' + directorId);
    if (!card) return;

    const formData = new FormData();
    formData.append('director_id', directorId);
    formData.append('status', newStatus);
    formData.append('login_allowed', (newStatus === 'active') ? '1' : '0');
    formData.append('ajax', '1');

    fetch(BASE_URL + '/admin/directors/status', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Update Card Attribute & Badges
            card.setAttribute('data-status', newStatus);

            const badge = document.getElementById('badge-status-' + directorId);
            const badgeText = document.getElementById('badge-text-' + directorId);
            const tableBadge = document.getElementById('table-badge-' + directorId);

            let label = 'বহাল';
            let badgeClass = 'bg-emerald-100 text-emerald-900';
            if (newStatus === 'expelled') { label = 'বাতিল'; badgeClass = 'bg-red-100 text-red-900'; }
            else if (newStatus === 'suspended') { label = 'স্থগিত'; badgeClass = 'bg-amber-100 text-amber-900'; }
            else if (newStatus === 'inactive') { label = 'নিষ্ক্রিয়'; badgeClass = 'bg-slate-200 text-slate-800'; }

            if (badgeText) badgeText.innerText = label;
            if (badge) badge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-black ' + badgeClass;
            if (tableBadge) {
                tableBadge.innerText = label;
                tableBadge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold ' + badgeClass;
            }

            // Update Quick Buttons Active Styling
            const quickBtns = card.querySelectorAll('.btn-quick-status');
            quickBtns.forEach(btn => {
                if (newStatus === 'active' && btn.innerText.includes('বহাল')) {
                    btn.className = 'btn-quick-status px-2.5 py-1 rounded-md text-[11px] font-extrabold transition shadow-xs bg-emerald-700 text-white';
                } else if (newStatus === 'expelled' && btn.innerText.includes('বাতিল')) {
                    btn.className = 'btn-quick-status px-2.5 py-1 rounded-md text-[11px] font-extrabold transition shadow-xs bg-red-700 text-white';
                } else if (newStatus === 'suspended' && btn.innerText.includes('স্থগিত')) {
                    btn.className = 'btn-quick-status px-2.5 py-1 rounded-md text-[11px] font-extrabold transition shadow-xs bg-amber-600 text-white';
                } else {
                    btn.className = 'btn-quick-status px-2.5 py-1 rounded-md text-[11px] font-extrabold transition shadow-xs bg-slate-100 text-slate-700 hover:bg-slate-200';
                }
            });

            refreshCounters();
            showToast(data.message || 'স্ট্যাটাস সফলভাবে আপডেট হয়েছে!');
        } else {
            showToast(data.message || 'ত্রুটি ঘটেছে, পুনরায় চেষ্টা করুন।', false);
        }
    })
    .catch(err => {
        console.error(err);
        showToast('সার্ভারের সাথে সংযোগ স্থাপন করা সম্ভব হয়নি।', false);
    });
}

// Open Director Management Modal
function openDirectorModal(data) {
    document.getElementById('modalDirectorId').value = data.id || '';
    document.getElementById('modalDirectorName').innerText = (data.name || 'পরিচালক') + ' [ID #' + data.id + ']';
    document.getElementById('modalDirectorArea').innerText = 'দায়িত্বপ্রাপ্ত জেলা: ' + (data.district || 'অনির্ধারিত') + ' (' + (data.division || '') + ' বিভাগ) | মোবাইল: ' + (data.phone || '');

    // Set Status Radio
    const status = data.status || 'active';
    if (status === 'active') document.getElementById('statusRadioActive').checked = true;
    else if (status === 'expelled') document.getElementById('statusRadioExpelled').checked = true;
    else if (status === 'suspended') document.getElementById('statusRadioSuspended').checked = true;
    else if (status === 'inactive') document.getElementById('statusRadioInactive').checked = true;

    // Login Allowed Checkbox
    document.getElementById('modalLoginAllowed').checked = (parseInt(data.login_allowed) !== 0);

    // Reason & Remarks
    document.getElementById('modalStatusReason').value = data.status_reason || '';
    document.getElementById('modalAdminRemarks').value = data.admin_remarks || '';

    document.getElementById('directorModal').classList.remove('hidden');
}

function closeDirectorModal() {
    document.getElementById('directorModal').classList.add('hidden');
}

// Submit Modal Form via AJAX
function submitDirectorStatusForm(e) {
    e.preventDefault();
    const form = document.getElementById('directorStatusForm');
    const formData = new FormData(form);
    const id = formData.get('director_id');
    const status = formData.get('status');
    const reason = formData.get('status_reason') || '';

    const btn = document.getElementById('btnModalSave');
    btn.disabled = true;
    btn.innerText = 'সংরক্ষণ হচ্ছে...';

    fetch(BASE_URL + '/admin/directors/status', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerText = 'সংরক্ষণ করুন';

        if (data.success) {
            closeDirectorModal();
            showToast(data.message || 'পরিচালকের তথ্য সংরক্ষিত হয়েছে!');

            // Update DOM card
            const card = document.getElementById('director-card-' + id);
            if (card) {
                card.setAttribute('data-status', status);
                const badgeText = document.getElementById('badge-text-' + id);
                if (badgeText) {
                    badgeText.innerText = (status === 'active') ? 'বহাল' : ((status === 'expelled') ? 'বাতিল' : 'স্থগিত');
                }
                const reasonBox = document.getElementById('reason-box-' + id);
                const reasonText = document.getElementById('reason-text-' + id);
                if (reason && reason.trim() !== '') {
                    if (reasonText) reasonText.innerText = reason;
                    if (reasonBox) reasonBox.classList.remove('hidden');
                } else {
                    if (reasonBox) reasonBox.classList.add('hidden');
                }
            }
            refreshCounters();
        } else {
            showToast(data.message || 'ত্রুটি ঘটেছে।', false);
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerText = 'সংরক্ষণ করুন';
        showToast('সার্ভারে সমস্যা হয়েছে।', false);
    });
}

// Confirm Single Delete Director
function confirmDeleteDirector(id, name) {
    if (!confirm(`আপনি কি নিশ্চিতভাবে পরিচালক "${name}" (ID #${id})-কে তালিকা থেকে সম্পূর্ণ বাদ দিতে চান?`)) {
        return;
    }

    const formData = new FormData();
    formData.append('ajax', '1');

    fetch(BASE_URL + '/admin/directors/delete/' + id, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const card = document.getElementById('director-card-' + id);
            const row = document.getElementById('director-row-' + id);
            if (card) card.remove();
            if (row) row.remove();
            refreshCounters();
            showToast(data.message || 'পরিচালক তালিকা থেকে বাদ দেওয়া হয়েছে!');
        } else {
            showToast(data.message || 'মুছতে সমস্যা হয়েছে।', false);
        }
    })
    .catch(err => {
        showToast('সার্ভার এরর!', false);
    });
}

function deleteFromModal() {
    const id = document.getElementById('modalDirectorId').value;
    const name = document.getElementById('modalDirectorName').innerText;
    closeDirectorModal();
    if (id) {
        confirmDeleteDirector(id, name);
    }
}

// Checkbox Selection & Bulk Toolbar Logic
function updateBulkToolbar() {
    const checked = document.querySelectorAll('.dir-checkbox:checked');
    const toolbar = document.getElementById('bulkActionsToolbar');
    const countText = document.getElementById('selectedCountText');

    if (checked.length > 0) {
        toolbar.classList.remove('hidden');
        countText.innerText = toBnNum(checked.length);
    } else {
        toolbar.classList.add('hidden');
    }
}

function toggleSelectAll(masterCheckbox) {
    const checkboxes = document.querySelectorAll('.dir-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = masterCheckbox.checked;
    });
    updateBulkToolbar();
}

function applyBulkAction(action) {
    const checked = Array.from(document.querySelectorAll('.dir-checkbox:checked')).map(cb => cb.value);
    if (!checked.length) {
        alert('কোনো পরিচালক নির্বাচন করা হয়নি!');
        return;
    }

    let confirmMsg = `আপনি কি নির্বাচিত ${checked.length} জন পরিচালকের উপর এই অ্যাকশন কার্যকর করতে চান?`;
    if (action === 'delete') {
        confirmMsg = `⚠️ সতর্কতা: আপনি কি নিশ্চিতভাবে নির্বাচিত ${checked.length} জন পরিচালককে ডাটাবেজ থেকে সম্পূর্ণ মুছে ফেলতে চান?`;
    }
    if (!confirm(confirmMsg)) return;

    const formData = new FormData();
    checked.forEach(id => formData.append('director_ids[]', id));
    formData.append('action', action);
    formData.append('ajax', '1');

    fetch(BASE_URL + '/admin/directors/bulk-action', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || 'বাল্ক অ্যাকশন সফল হয়েছে!');
            // Reload page to reflect all changes cleanly
            setTimeout(() => { window.location.reload(); }, 1200);
        } else {
            showToast(data.message || 'ব্যর্থ হয়েছে।', false);
        }
    })
    .catch(err => {
        showToast('সার্ভার যোগাযোগে সমস্যা হয়েছে।', false);
    });
}

// View Mode Toggle (Grid / Table)
function setViewMode(mode) {
    const gridView = document.getElementById('directorsGridView');
    const tableView = document.getElementById('directorsTableView');
    const btnGrid = document.getElementById('btnGridView');
    const btnTable = document.getElementById('btnTableView');

    if (mode === 'grid') {
        gridView.classList.remove('hidden');
        tableView.classList.add('hidden');
        btnGrid.className = 'px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-night text-white shadow-sm transition flex items-center';
        btnTable.className = 'px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition flex items-center';
    } else {
        gridView.classList.add('hidden');
        tableView.classList.remove('hidden');
        btnTable.className = 'px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-night text-white shadow-sm transition flex items-center';
        btnGrid.className = 'px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition flex items-center';
    }
}

// Division Filtering
function filterByDivision(divName, buttonElement) {
    currentDivision = divName;
    document.querySelectorAll('.div-pill').forEach(btn => {
        btn.className = 'div-pill px-3 py-1 rounded-lg text-xs font-bold transition bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-emerald-night border border-slate-200/80';
    });
    if (buttonElement) {
        buttonElement.className = 'div-pill px-3 py-1 rounded-lg text-xs font-bold transition bg-emerald-night text-white shadow-sm';
    }
    filterDirectors();
}

// Live Search Filter
function filterDirectors() {
    const query = document.getElementById('directorSearchInput').value.toLowerCase().trim();
    const cards = document.querySelectorAll('.director-card');
    const rows = document.querySelectorAll('.director-table-row');
    const noFound = document.getElementById('noDirectorsFound');
    const countBadge = document.getElementById('directorCountBadge');

    let visibleCount = 0;

    cards.forEach(card => {
        const name = card.getAttribute('data-name') || '';
        const district = card.getAttribute('data-district') || '';
        const division = card.getAttribute('data-division') || '';
        const phone = card.getAttribute('data-phone') || '';

        const matchesQuery = query === '' || name.includes(query) || district.includes(query) || phone.includes(query);
        const matchesDiv = currentDivision === 'all' || division === currentDivision;

        if (matchesQuery && matchesDiv) {
            card.classList.remove('hidden');
            visibleCount++;
        } else {
            card.classList.add('hidden');
        }
    });

    rows.forEach(row => {
        const name = row.getAttribute('data-name') || '';
        const district = row.getAttribute('data-district') || '';
        const division = row.getAttribute('data-division') || '';
        const phone = row.getAttribute('data-phone') || '';

        const matchesQuery = query === '' || name.includes(query) || district.includes(query) || phone.includes(query);
        const matchesDiv = currentDivision === 'all' || division === currentDivision;

        if (matchesQuery && matchesDiv) {
            row.classList.remove('hidden');
        } else {
            row.classList.add('hidden');
        }
    });

    if (visibleCount === 0) {
        noFound.classList.remove('hidden');
    } else {
        noFound.classList.add('hidden');
    }

    if (countBadge) {
        countBadge.innerText = toBnNum(visibleCount) + ' জন';
    }
}
</script>
