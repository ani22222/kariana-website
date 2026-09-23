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
            </div>
            <h1 class="text-3xl font-extrabold text-emerald-night">কারিয়ানা কুরআন কন্ট্রোল প্যানেল</h1>
            <p class="text-sm text-slate-500">স্বাগতম, <?= htmlspecialchars($user['name'] ?? 'এডমিন') ?> | কেন্দ্রীয় ডাটাবেস ও সকল পরিচালকের একক ড্যাশবোর্ড ম্যানেজমেন্ট</p>
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
    <!-- DEDICATED DIRECTORS HUB (সকল পরিচালকের লিস্ট ও পৃথক ড্যাশবোর্ড কমান্ড সেন্টার) -->
    <!-- ========================================================================= -->
    <section id="directors-hub" class="bg-[#fffefb] rounded-3xl shadow-sm border-2 border-[#e6dece] overflow-hidden mb-12">
        <!-- Section Header -->
        <div class="bg-gradient-to-r from-emerald-night via-emerald-deep to-emerald-night px-6 py-6 text-white flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="px-2.5 py-0.5 rounded-full bg-gold-rich text-white text-xs font-extrabold uppercase tracking-wider">কমান্ড সেন্টার</span>
                    <span class="text-xs text-emerald-100/90 font-medium">বাংলাদেশব্যাপী ৫৯ জেলা নেটওয়ার্ক</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-white flex items-center">
                    <svg class="w-6 h-6 mr-2.5 text-gold-rich" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    সকল জেলা পরিচালক তালিকা ও পৃথক পৃথক ড্যাশবোর্ড হাব
                </h2>
                <p class="text-xs sm:text-sm text-emerald-100/80 mt-1">যেকোনো পরিচালকের নিজস্ব একক ড্যাশবোর্ড ও রিপোর্ট দেখতে নিচে ১-ক্লিকে সুইচ বা প্রবেশ করুন।</p>
            </div>

            <!-- Instant Director Switcher Dropdown (1-Click Switch) -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <div class="relative">
                    <select id="quickDirectorSelect" onchange="if(this.value) window.location.href=this.value" class="w-full sm:w-80 bg-emerald-950/90 hover:bg-emerald-950 text-white font-bold text-xs py-3 px-3.5 rounded-xl border border-gold-rich/50 shadow-inner focus:outline-none focus:ring-2 focus:ring-gold-rich transition cursor-pointer">
                        <option value="">⚡ যেকোনো পরিচালকের ড্যাশবোর্ডে প্রবেশ করুন (৫৯)...</option>
                        <?php foreach ($allDirectors as $dirOption): ?>
                            <option value="<?= $baseUrl ?>/admin/directors/impersonate/<?= $dirOption['id'] ?>">
                                [ID #<?= $dirOption['id'] ?>] <?= htmlspecialchars($dirOption['district_name']) ?> — <?= htmlspecialchars($dirOption['name']) ?> (<?= htmlspecialchars($dirOption['division_name']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <a href="<?= $baseUrl ?>/admin/directors" class="bg-gold-rich hover:bg-gold-deep text-white px-4 py-2.5 rounded-xl text-xs font-bold transition shadow shrink-0 flex items-center justify-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    এসএমএস হাব
                </a>
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

                <!-- View Mode Toggle (Grid / Table) -->
                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-xs font-bold text-slate-400">ভিউ মোড:</span>
                    <button type="button" id="btnGridView" onclick="setViewMode('grid')" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-night text-white shadow-sm transition flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        কার্ড গ্রিড
                    </button>
                    <button type="button" id="btnTableView" onclick="setViewMode('table')" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        তালিকা টেবিল
                    </button>
                    <span id="directorCountBadge" class="text-xs font-extrabold text-emerald-night bg-emerald-100 px-3 py-1 rounded-full">
                        <?= \Core\BengaliHelper::toBengaliNumber(count($allDirectors)) ?> জন
                    </span>
                </div>
            </div>

            <!-- Division Filter Pills -->
            <div class="flex flex-wrap items-center gap-1.5 pt-1">
                <span class="text-xs font-bold text-slate-500 mr-1.5">বিভাগ:</span>
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
                    $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                    if (str_starts_with($cleanPhone, '88')) $cleanPhone = '+' . $cleanPhone;
                    elseif (str_starts_with($cleanPhone, '01')) $cleanPhone = '+88' . $cleanPhone;
                    ?>
                    <div class="director-card bg-white rounded-2xl border border-slate-200/90 hover:border-emerald-500 shadow-sm hover:shadow-lg transition-all duration-200 flex flex-col justify-between overflow-hidden group"
                         data-name="<?= strtolower(htmlspecialchars($dir['name'])) ?>"
                         data-district="<?= strtolower(htmlspecialchars($district)) ?>"
                         data-division="<?= htmlspecialchars($division) ?>"
                         data-phone="<?= htmlspecialchars($phone) ?>">
                        
                        <!-- Top Card Banner -->
                        <div class="p-5 pb-3">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-night text-[11px] font-extrabold">
                                        ID #<?= $dir['id'] ?>
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-md bg-amber-50 text-amber-900 border border-amber-200 text-[11px] font-bold">
                                        <?= htmlspecialchars($division) ?> বিভাগ
                                    </span>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold <?= ($dir['status'] === 'active') ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' ?>">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 <?= ($dir['status'] === 'active') ? 'bg-emerald-500' : 'bg-red-500' ?>"></span>
                                    <?= ($dir['status'] === 'active') ? 'কর্মরত' : 'স্থগিত' ?>
                                </span>
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
                        </div>

                        <!-- Card Action Buttons (Direct 1-Click Dashboard Access) -->
                        <div class="px-5 py-3.5 bg-slate-50/70 border-t border-slate-100 flex items-center gap-2">
                            <!-- Main 1-Click Director Dashboard Switch -->
                            <a href="<?= $baseUrl ?>/admin/directors/impersonate/<?= $dir['id'] ?>" class="flex-1 bg-gradient-to-r from-emerald-night to-emerald-deep hover:from-emerald-deep hover:to-emerald-800 text-white py-2 px-3 rounded-xl text-xs font-extrabold shadow-sm transition flex items-center justify-center gap-1.5 border border-emerald-700/60" title="সরাসরি এই পরিচালকের একক ড্যাশবোর্ডে প্রবেশ করুন">
                                <svg class="w-3.5 h-3.5 text-gold-rich" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>একক ড্যাশবোর্ড</span>
                            </a>

                            <!-- Public Profile & Teachers List -->
                            <a href="<?= $baseUrl ?>/directors/<?= $slug ?>" target="_blank" class="bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 py-2 px-2.5 rounded-xl text-xs font-bold transition flex items-center justify-center shrink-0" title="পরিচালকের পাবলিক প্রোফাইল ও শিক্ষক তালিকা">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>

                            <!-- Call / WhatsApp -->
                            <?php if ($phone !== 'প্রযোজ্য নয়' && !empty($cleanPhone)): ?>
                                <a href="https://wa.me/<?= ltrim($cleanPhone, '+') ?>" target="_blank" class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 py-2 px-2.5 rounded-xl text-xs font-bold transition flex items-center justify-center shrink-0" title="হোয়াটসঅ্যাপে যোগাযোগ">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.694.072-2.124-.519-1.428-.59-2.34-2.043-2.411-2.138-.071-.094-.576-.767-.576-1.464 0-.697.362-1.039.49-1.182.129-.144.281-.181.375-.181.094 0 .188.002.27.006.086.005.201-.033.314.24.118.283.402.98.437 1.052.035.072.059.156.012.25-.047.094-.07.153-.141.236-.071.082-.149.184-.213.247-.071.07-.145.146-.063.287.082.141.365.602.784.975.54.481.996.63 1.137.701.141.071.224.059.307-.035.083-.095.354-.412.448-.553.094-.141.188-.118.318-.071.129.047.824.388.966.459.141.071.235.106.27.165.035.059.035.341-.109.746z"/></svg>
                                </a>
                            <?php endif; ?>
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
                        <th class="px-4 py-3.5 text-center">ID</th>
                        <th class="px-4 py-3.5">বিভাগ</th>
                        <th class="px-4 py-3.5">জেলা ও এলাকা</th>
                        <th class="px-4 py-3.5">পরিচালকের নাম ও পদবি</th>
                        <th class="px-4 py-3.5">মোবাইল নম্বর</th>
                        <th class="px-4 py-3.5 text-center">শিক্ষক</th>
                        <th class="px-4 py-3.5 text-center">স্ট্যাটাস</th>
                        <th class="px-4 py-3.5 text-right">একক ড্যাশবোর্ড অ্যাকশন</th>
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
                        ?>
                        <tr class="director-table-row hover:bg-slate-50/80 transition"
                            data-name="<?= strtolower(htmlspecialchars($dir['name'])) ?>"
                            data-district="<?= strtolower(htmlspecialchars($district)) ?>"
                            data-division="<?= htmlspecialchars($division) ?>"
                            data-phone="<?= htmlspecialchars($phone) ?>">
                            <td class="px-4 py-3.5 text-center font-bold text-slate-500 text-xs">#<?= $dir['id'] ?></td>
                            <td class="px-4 py-3.5">
                                <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-900 border border-amber-200 text-xs font-bold">
                                    <?= htmlspecialchars($division) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3.5 font-semibold text-slate-800 text-xs max-w-xs truncate" title="<?= htmlspecialchars($district) ?>">
                                📍 <?= htmlspecialchars($district) ?>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-extrabold text-slate-900 text-sm"><?= htmlspecialchars($dir['name']) ?></div>
                                <div class="text-[11px] text-gold-deep"><?= htmlspecialchars($dir['designation']) ?></div>
                            </td>
                            <td class="px-4 py-3.5 font-mono text-xs font-bold text-slate-800"><?= htmlspecialchars($phone) ?></td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="bg-emerald-100 text-emerald-night text-xs font-bold px-2 py-0.5 rounded-full">
                                    <?= \Core\BengaliHelper::toBengaliNumber($teachersCount) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold <?= ($dir['status'] === 'active') ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' ?>">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1 <?= ($dir['status'] === 'active') ? 'bg-emerald-500' : 'bg-red-500' ?>"></span>
                                    <?= ($dir['status'] === 'active') ? 'কর্মরত' : 'স্থগিত' ?>
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right space-x-1.5">
                                <a href="<?= $baseUrl ?>/admin/directors/impersonate/<?= $dir['id'] ?>" class="inline-flex items-center px-3 py-1.5 bg-emerald-night hover:bg-emerald-deep text-white text-xs font-bold rounded-lg shadow-sm transition">
                                    <svg class="w-3.5 h-3.5 mr-1 text-gold-rich" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    ড্যাশবোর্ড
                                </a>
                                <a href="<?= $baseUrl ?>/directors/<?= $slug ?>" target="_blank" class="inline-flex items-center px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition" title="পাবলিক প্রোফাইল">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
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

<!-- Real-time Vanilla JavaScript Filter & View Switcher Script -->
<script>
let currentDivision = 'all';

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

function filterByDivision(divName, buttonElement) {
    currentDivision = divName;
    
    // Update division button styles
    document.querySelectorAll('.div-pill').forEach(btn => {
        btn.className = 'div-pill px-3 py-1 rounded-lg text-xs font-bold transition bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-emerald-night border border-slate-200/80';
    });
    if (buttonElement) {
        buttonElement.className = 'div-pill px-3 py-1 rounded-lg text-xs font-bold transition bg-emerald-night text-white shadow-sm';
    }

    filterDirectors();
}

function toBnNum(num) {
    const bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
    return String(num).replace(/[0-9]/g, d => bn[d]);
}

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
