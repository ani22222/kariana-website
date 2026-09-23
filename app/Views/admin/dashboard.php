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

        <a href="#teachers-hub" class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 hover:border-emerald-vibrant hover:shadow-md hover:scale-[1.02] transition flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-gold-deep flex items-center justify-center font-bold shrink-0">
                <i class="fas fa-chalkboard-user"></i>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 text-sm">সকল শিক্ষকবৃন্দ</h4>
                <p class="text-xs text-slate-400"><?= \Core\BengaliHelper::toBengaliNumber($stats['teachers'] ?? 218) ?> জন শিক্ষক</p>
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
    <!-- TELEGRAM 24/7 MANAGEMENT HUB & MULTI-ADMIN COMMAND CENTER (টপিক ১৭) -->
    <!-- ========================================================================= -->
    <div class="bg-gradient-to-r from-[#031d16] via-[#07271e] to-[#041f17] border-2 border-amber-400/40 rounded-3xl p-6 mb-10 shadow-xl text-white">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 pb-5 border-b border-amber-400/20">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-sky-500/20 border-2 border-sky-400/60 flex items-center justify-center text-sky-400 text-2xl shrink-0 shadow-[0_0_15px_rgba(56,189,248,0.25)]">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.121l-6.871 4.326-2.962-.924c-.643-.204-.657-.643.136-.953l11.57-4.461c.537-.194 1.006.131.832.922z"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <span class="px-2.5 py-0.5 rounded-full bg-sky-500/30 text-sky-300 border border-sky-400/50 text-[11px] font-black uppercase tracking-wider">
                            অফিসিয়াল টেলিগ্রাম গেটওয়ে
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/40 text-[11px] font-bold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                            লাইভ সক্রিয় 🟢
                        </span>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-black text-amber-300 flex items-center gap-2">
                        <span>@karianaquranbot</span>
                        <span class="text-xs text-slate-300 font-normal">(Kariana Quran Full Management)</span>
                    </h3>
                    <p class="text-xs sm:text-sm text-emerald-100/80 mt-0.5">
                        ২৪/৭ সার্বক্ষণিক কেন্দ্রীয় নোটিফিকেশন, ডিপ লিংক রেফারেল ট্র্যাকিং ও মাল্টি-অ্যাডমিন রোল ব্যবস্থাপনা
                    </p>
                </div>
            </div>

            <!-- Action buttons & Bot Quick Access -->
            <div class="flex flex-wrap items-center gap-3">
                <a href="https://t.me/karianaquranbot" target="_blank" class="px-4 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-600 text-white text-xs font-black shadow-md transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.121l-6.871 4.326-2.962-.924c-.643-.204-.657-.643.136-.953l11.57-4.461c.537-.194 1.006.131.832.922z"/></svg>
                    <span>বটে প্রবেশ করুন</span>
                </a>
                <a href="https://t.me/karianaquranbot?start=_tgr_KHdiM5ZlMDFl" target="_blank" class="px-3.5 py-2.5 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-400/50 text-xs font-bold transition flex items-center gap-1.5" title="রেফারেল লিংক টেস্ট করুন">
                    <span>🔗 রেফারেল লিংক টেস্ট</span>
                </a>
            </div>
        </div>

        <!-- 3-Pillar Multi-Admin Role Matrix -->
        <div class="mt-5 grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- 1. Super Admin: Rasel Gazi -->
            <div class="bg-black/30 border border-sky-400/30 rounded-2xl p-4 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-black uppercase tracking-wider text-sky-300 bg-sky-500/20 px-2 py-0.5 rounded-md border border-sky-400/30">
                            মেইন সিস্টেম অ্যাডমিন
                        </span>
                        <span class="text-xs text-emerald-400 font-mono">TG ID: 1827362508</span>
                    </div>
                    <h4 class="font-black text-sm text-white flex items-center gap-1.5">
                        <span>রাসেল গাজী (Rasel Gazi)</span>
                        <span class="text-xs text-sky-400 font-normal">(@mraselg)</span>
                    </h4>
                    <p class="text-[11px] text-slate-300 mt-1 leading-snug">
                        সার্ভার, ডেমন, এআই মডেল সুইচিং, ব্যাকআপ ও এপিআই আর্কিটেকচারের পূর্ণ কমান্ড কন্ট্রোল।
                    </p>
                </div>
                <div class="mt-3 pt-2 border-t border-white/10 flex items-center justify-between text-[11px] text-slate-400">
                    <span>পারমিশন: <strong class="text-amber-300">১০০% আনলিমিটেড</strong></span>
                    <span class="text-emerald-400">অনলাইন 🟢</span>
                </div>
            </div>

            <!-- 2. Founder Owner: Maulana Saddam Hossain -->
            <div class="bg-black/30 border border-amber-400/30 rounded-2xl p-4 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-black uppercase tracking-wider text-amber-300 bg-amber-500/20 px-2 py-0.5 rounded-md border border-amber-400/30">
                            প্রতিষ্ঠানের মালিক ও প্রতিষ্ঠাতা
                        </span>
                        <span class="text-xs text-amber-300 font-mono">01717056816</span>
                    </div>
                    <h4 class="font-black text-sm text-white flex items-center gap-1.5">
                        <span>মাওলানা সাদ্দাম হোসেন</span>
                    </h4>
                    <p class="text-[11px] text-slate-300 mt-1 leading-snug">
                        ইসলামিক বোর্ড অনুমোদন, ৫৯ জেলা পরিচালক নিয়োগ/স্থগিত এবং কিতাব প্রকাশনা সিদ্ধান্ত।
                    </p>
                </div>
                <div class="mt-3 pt-2 border-t border-white/10 flex items-center justify-between text-[11px] text-slate-400">
                    <span>পারমিশন: <strong class="text-amber-300">প্রাতিষ্ঠানিক প্রধান</strong></span>
                    <span class="text-emerald-400">সক্রিয় 🟢</span>
                </div>
            </div>

            <!-- 3. Operations Manager -->
            <div class="bg-black/30 border border-emerald-400/30 rounded-2xl p-4 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-black uppercase tracking-wider text-emerald-300 bg-emerald-500/20 px-2 py-0.5 rounded-md border border-emerald-400/30">
                            প্রশাসনিক ও ডেলিভারি ম্যানেজার
                        </span>
                        <span class="text-xs text-slate-400 font-mono">অর্ডার ডেস্ক</span>
                    </div>
                    <h4 class="font-black text-sm text-white flex items-center gap-1.5">
                        <span>সেন্ট্রাল ম্যানেজার</span>
                    </h4>
                    <p class="text-[11px] text-slate-300 mt-1 leading-snug">
                        পাইকারি বই অর্ডার সমন্বয়, কুরিয়ার ট্র্যাকিং, চালান এবং ভর্তি লিড ফলোআপ।
                    </p>
                </div>
                <div class="mt-3 pt-2 border-t border-white/10 flex items-center justify-between text-[11px] text-slate-400">
                    <span>পারমিশন: <strong class="text-amber-300">অর্ডার ও স্টক</strong></span>
                    <span class="text-emerald-400">স্ট্যান্ডবাই 🟡</span>
                </div>
            </div>
        </div>

        <!-- Telegram Live Counters -->
        <div class="mt-4 pt-3 border-t border-amber-400/20 flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-4 flex-wrap">
                <span>🤖 বট ডেমন: <strong class="text-emerald-400 font-mono">Active (v5.0)</strong></span>
                <span>👥 বট ব্যবহারকারী: <strong class="text-amber-300 font-bold"><?= \Core\BengaliHelper::toBengaliNumber($telegramUsersCount ?? 1) ?> জন</strong></span>
                <span>🔗 রেফারেল ক্লিক: <strong class="text-sky-300 font-bold"><?= \Core\BengaliHelper::toBengaliNumber($telegramReferralsCount ?? 0) ?> টি</strong></span>
            </div>
            <div class="text-[11px] text-slate-400">
                টেলিগ্রাম পুশ এপিআই ও এসএমএস গেটওয়ে সংযুক্ত
            </div>
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

            <!-- Instant Switcher, Add New Director & CSV Download Buttons -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                <div class="relative">
                    <select id="quickDirectorSelect" onchange="if(this.value) window.location.href=this.value" class="w-full sm:w-64 bg-emerald-950/90 hover:bg-emerald-950 text-white font-bold text-xs py-2.5 px-3 rounded-xl border border-gold-rich/50 shadow-inner focus:outline-none focus:ring-2 focus:ring-gold-rich transition cursor-pointer">
                        <option value="">⚡ যেকোনো পরিচালকের ড্যাশবোর্ড (<?= \Core\BengaliHelper::toBengaliNumber(count($allDirectors)) ?>)...</option>
                        <?php foreach ($allDirectors as $dirOption): ?>
                            <option value="<?= $baseUrl ?>/admin/directors/impersonate/<?= $dirOption['id'] ?>">
                                [ID #<?= $dirOption['id'] ?>] <?= htmlspecialchars($dirOption['district_name']) ?> — <?= htmlspecialchars($dirOption['name']) ?> (<?= htmlspecialchars($dirOption['division_name']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="button" onclick="toggleAddDirectorPanel()" class="bg-gradient-to-r from-emerald-600 via-emerald-700 to-emerald-800 hover:from-emerald-500 hover:to-emerald-700 text-amber-300 border border-amber-400/50 px-3.5 py-2.5 rounded-xl text-xs font-black transition shadow flex items-center justify-center shrink-0 cursor-pointer" title="তালিকায় নতুন পরিচালক যুক্ত করুন">
                    <svg class="w-4 h-4 mr-1.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>নতুন পরিচালক যুক্ত করুন</span>
                </button>

                <button type="button" onclick="toggleDeveloperMessagePanel()" class="bg-gradient-to-r from-sky-600 via-blue-600 to-indigo-700 hover:from-sky-500 hover:to-indigo-600 text-white border border-sky-300/40 px-3.5 py-2.5 rounded-xl text-xs font-black transition shadow flex items-center justify-center shrink-0 cursor-pointer relative" title="ডেভেলপারকে যেকোনো তথ্য, সংশোধন বা বার্তা পাঠান">
                    <svg class="w-4 h-4 mr-1.5 text-sky-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    <span>ডেভেলপারকে তথ্য দিন</span>
                    <?php if (!empty($unreadDevMessagesCount) && $unreadDevMessagesCount > 0): ?>
                        <span class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full ring-2 ring-white animate-pulse"><?= \Core\BengaliHelper::toBengaliNumber($unreadDevMessagesCount) ?></span>
                    <?php endif; ?>
                </button>

                <a href="<?= $baseUrl ?>/admin/directors/export" class="bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white px-3.5 py-2.5 rounded-xl text-xs font-extrabold transition shadow flex items-center justify-center shrink-0" title="চূড়ান্ত যাচাইকৃত তালিকা এক্সেল CSV ফরম্যাটে ডাউনলোড করুন">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    তালিকা ডাউনলোড (CSV)
                </a>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- ADD NEW DIRECTOR TEXT-BOX PANEL (হুজুরের জন্য নতুন পরিচালক সংযোজন বক্স) -->
        <!-- ========================================================================= -->
        <div id="addNewDirectorPanel" class="hidden bg-gradient-to-br from-amber-50/95 via-emerald-50/50 to-white border-b-2 border-amber-300 p-6 transition-all duration-300 shadow-inner">
            <div class="max-w-5xl mx-auto">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-amber-200/80">
                    <div class="flex items-center space-x-2.5">
                        <span class="w-9 h-9 rounded-xl bg-emerald-night text-amber-300 flex items-center justify-center shadow">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        </span>
                        <div>
                            <h3 class="text-base sm:text-lg font-black text-emerald-950">নতুন জেলা পরিচালক সংযোজন (টেক্সট বক্স এন্ট্রি ফর্ম)</h3>
                            <p class="text-xs text-slate-600">হুজুরের নির্দেশনা অনুযায়ী কোনো নতুন পরিচালককে অন্তর্ভুক্ত করতে নিচের টেক্সট বক্সগুলো পূরণ করুন:</p>
                        </div>
                    </div>
                    <button type="button" onclick="toggleAddDirectorPanel()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-white/80 transition" title="বন্ধ করুন">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form id="createDirectorForm" onsubmit="submitNewDirector(event)" class="space-y-4">
                    <?= $csrfField ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                        <div>
                            <label class="block text-xs font-extrabold text-slate-800 mb-1">
                                পরিচালকের পূর্ণ নাম <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="newDirName" name="name" required placeholder="যেমন: মাওলানা মুহাম্মাদ আব্দুল্লাহ" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-vibrant focus:border-emerald-600 bg-white shadow-xs">
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-800 mb-1">
                                দায়িত্বপ্রাপ্ত জেলা / এলাকা <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="newDirDistrict" name="district_name" required placeholder="যেমন: সিলেট / সুনামগঞ্জ" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-vibrant focus:border-emerald-600 bg-white shadow-xs">
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-800 mb-1">
                                প্রশাসনিক বিভাগ <span class="text-red-500">*</span>
                            </label>
                            <select id="newDirDivision" name="division_name" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-vibrant focus:border-emerald-600 bg-white shadow-xs cursor-pointer">
                                <option value="ঢাকা">ঢাকা বিভাগ</option>
                                <option value="চট্টগ্রাম">চট্টগ্রাম বিভাগ</option>
                                <option value="রাজশাহী">রাজশাহী বিভাগ</option>
                                <option value="খুলনা">খুলনা বিভাগ</option>
                                <option value="বরিশাল">বরিশাল বিভাগ</option>
                                <option value="সিলেট">সিলেট বিভাগ</option>
                                <option value="রংপুর">রংপুর বিভাগ</option>
                                <option value="ময়মনসিংহ">ময়মনসিংহ বিভাগ</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-800 mb-1">
                                মোবাইল নম্বর <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="newDirPhone" name="phone" required placeholder="যেমন: 017xxxxxxxx" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-vibrant focus:border-emerald-600 bg-white font-mono shadow-xs">
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-800 mb-1">পদবি</label>
                            <input type="text" id="newDirDesignation" name="designation" value="জেলা পরিচালক" placeholder="যেমন: জেলা পরিচালক" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-vibrant focus:border-emerald-600 bg-white shadow-xs">
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-800 mb-1">শিক্ষাগত যোগ্যতা / উপাধি</label>
                            <input type="text" id="newDirQualification" name="qualification" value="কারিয়ানা সার্টিফাইড ক্বারী ও প্রশিক্ষক" placeholder="যেমন: ক্বারী ও মুয়াল্লিম" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-vibrant focus:border-emerald-600 bg-white shadow-xs">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-amber-950 mb-1">
                            হুজুরের নোট / বিশেষ মন্তব্য (ঐচ্ছিক)
                        </label>
                        <input type="text" id="newDirNotes" name="status_reason" placeholder="যেমন: হুজুরের সরাসরি নির্দেশিত / নতুন দায়িত্বপ্রাপ্ত..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-amber-300 focus:ring-2 focus:ring-emerald-vibrant focus:border-emerald-600 bg-white shadow-xs">
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-2">
                        <button type="button" onclick="toggleAddDirectorPanel()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-white border border-slate-300 transition">
                            বাতিল
                        </button>
                        <button type="submit" id="btnSubmitDirector" class="px-5 py-2.5 rounded-xl text-xs font-black text-white bg-emerald-night hover:bg-emerald-deep shadow-md transition flex items-center cursor-pointer">
                            <svg class="w-4 h-4 mr-1.5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>তালিকায় যুক্ত করুন</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- DEVELOPER MESSAGE TEXT-BOX PANEL (ডেভেলপারকে যেকোনো তথ্য দেওয়ার টেক্সটবক্স) -->
        <!-- ========================================================================= -->
        <div id="developerMessagePanel" class="hidden bg-gradient-to-br from-sky-50/90 via-indigo-50/40 to-white border-b-2 border-sky-300 p-6 transition-all duration-300 shadow-inner">
            <div class="max-w-5xl mx-auto">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-sky-200/80">
                    <div class="flex items-center space-x-2.5">
                        <span class="w-9 h-9 rounded-xl bg-gradient-to-tr from-sky-600 to-indigo-700 text-white flex items-center justify-center shadow">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        </span>
                        <div>
                            <h3 class="text-base sm:text-lg font-black text-slate-900 flex items-center gap-2">
                                <span>ডেভেলপারকে যেকোনো তথ্য, সংশোধন বা পরামর্শ পাঠান</span>
                                <span class="text-[11px] px-2 py-0.5 bg-sky-100 text-sky-800 rounded-full font-bold">সরাসরি টেলিগ্রাম নোটিফিকেশন</span>
                            </h3>
                            <p class="text-xs text-slate-600">সম্মানিত মাওলানা সাদ্দাম হোসেন হুজুর—পরিচালক তালিকা বা ওয়েবসাইটে যেকোনো ধরনের পরিবর্তন ও নির্দেশ নিচের টেক্সটবক্সে লিখুন, সরাসরি ডেভেলপারের কাছে পৌঁছে যাবে:</p>
                        </div>
                    </div>
                    <button type="button" onclick="toggleDeveloperMessagePanel()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-white/80 transition cursor-pointer" title="বন্ধ করুন">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form id="sendDeveloperMessageForm" onsubmit="submitDeveloperMessage(event)" class="space-y-4">
                    <?= $csrfField ?>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                        <div>
                            <label class="block text-xs font-extrabold text-slate-800 mb-1">
                                আপনার নাম (প্রেরক)
                            </label>
                            <input type="text" id="devMsgSenderName" name="sender_name" value="<?= htmlspecialchars($user['name'] ?? 'মাওলানা সাদ্দাম হোসেন') ?>" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 bg-white shadow-xs font-medium">
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-800 mb-1">
                                আপনার মোবাইল নম্বর
                            </label>
                            <input type="text" id="devMsgSenderPhone" name="sender_phone" value="<?= htmlspecialchars($user['phone'] ?? '01717056816') ?>" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 bg-white font-mono shadow-xs">
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-slate-800 mb-1">
                                বার্তার বিষয় / ক্যাটাগরি
                            </label>
                            <select id="devMsgSubject" name="subject" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 bg-white shadow-xs cursor-pointer">
                                <option value="পরিচালক তালিকা সংক্রান্ত সংশোধন ও নির্দেশনা">পরিচালক তালিকা সংক্রান্ত সংশোধন ও নির্দেশনা</option>
                                <option value="নতুন পরিচালক যুক্ত করার অনুরোধ">নতুন পরিচালক যুক্ত করার অনুরোধ</option>
                                <option value="কোনো পরিচালকের তথ্য বা মোবাইল পরিবর্তন">কোনো পরিচালকের তথ্য বা মোবাইল পরিবর্তন</option>
                                <option value="ওয়েবসাইটের সাধারণ নির্দেশনা বা পরামর্শ">ওয়েবসাইটের সাধারণ নির্দেশনা বা পরামর্শ</option>
                                <option value="অন্যান্য বিশেষ নির্দেশনা">অন্যান্য বিশেষ নির্দেশনা</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-extrabold text-slate-900">
                                বিস্তারিত তথ্য / হুজুরের বার্তা <span class="text-red-500">*</span>
                            </label>
                            <span class="text-[11px] text-slate-500">পরিষ্কার ও বিস্তারিতভাবে লিখুন</span>
                        </div>
                        <textarea id="devMsgContent" name="message" rows="4" required placeholder="হুজুর, ওয়েবসাইট বা পরিচালকদের তালিকা সম্পর্কিত যেকোনো নির্দেশনা, পরিবর্তন, বা নতুন কোনো পরামর্শ এখানে লিখুন... যেমন: 'চট্টগ্রাম জেলার পরিচালকের ফোন নাম্বার হবে 018xxxxxxxx' অথবা 'সিলেটে অমুক হুজুরকে নতুন পরিচালক হিসেবে যুক্ত করতে হবে'..." class="w-full text-xs sm:text-sm px-4 py-3 rounded-xl border border-sky-300 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 bg-white shadow-sm leading-relaxed"></textarea>
                    </div>

                    <!-- Quick suggestion badges for 1-click typing -->
                    <div class="flex items-center gap-2 flex-wrap text-xs">
                        <span class="text-[11px] font-bold text-slate-500">কুইক টেমপ্লেট:</span>
                        <button type="button" onclick="appendDevMsgTemplate('পরিচালক তালিকায় এই নতুন ব্যক্তির নাম যুক্ত করুন: নাম: , জেলা: , মোবাইল: ')" class="px-2.5 py-1 bg-white hover:bg-sky-50 text-sky-800 border border-sky-200 rounded-lg text-[11px] font-medium transition cursor-pointer">➕ নতুন পরিচালক সংযোজন</button>
                        <button type="button" onclick="appendDevMsgTemplate('অমুক জেলার পরিচালকের মোবাইল নাম্বার সংশোধন করুন: নতুন নাম্বার: ')" class="px-2.5 py-1 bg-white hover:bg-sky-50 text-sky-800 border border-sky-200 rounded-lg text-[11px] font-medium transition cursor-pointer">✏️ মোবাইল নম্বর সংশোধন</button>
                        <button type="button" onclick="appendDevMsgTemplate('অমুক জেলার পরিচালককে তালিকা থেকে বাতিল বা স্থগিত রাখুন, কারণ: ')" class="px-2.5 py-1 bg-white hover:bg-sky-50 text-sky-800 border border-sky-200 rounded-lg text-[11px] font-medium transition cursor-pointer">⚠️ পরিচালক বাতিল/স্থগিত</button>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                        <div class="text-[11px] text-slate-500 flex items-center">
                            <svg class="w-4 h-4 mr-1 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>বার্তাটি পাঠালে ডেভেলপার সাথে সাথে টেলিগ্রাম (@raselcodebot) ও সিস্টেমে দেখতে পাবেন।</span>
                        </div>

                        <div class="flex items-center space-x-2 shrink-0">
                            <button type="button" onclick="toggleDeveloperMessagePanel()" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-white border border-slate-300 transition cursor-pointer">
                                বন্ধ করুন
                            </button>
                            <button type="submit" id="btnSubmitDevMsg" class="px-6 py-2.5 rounded-xl text-xs font-black text-white bg-gradient-to-r from-sky-600 via-blue-600 to-indigo-700 hover:from-sky-500 hover:to-indigo-600 shadow-md hover:shadow-lg transition flex items-center cursor-pointer">
                                <svg class="w-4 h-4 mr-1.5 text-sky-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                <span>ডেভেলপারকে পাঠান</span>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- DEVELOPER INBOX / বার্তা ইতিহাস (যদি পূর্বে কোনো বার্তা পাঠানো হয়ে থাকে) -->
                <?php if (!empty($developerMessages)): ?>
                    <div id="dev-messages" class="mt-6 pt-5 border-t border-sky-200">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-xs sm:text-sm font-extrabold text-slate-800 flex items-center">
                                <svg class="w-4 h-4 mr-1.5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                প্রেরিত বার্তার ইতিহাস ও ডেভেলপারের ইনবক্স (<?= \Core\BengaliHelper::toBengaliNumber(count($developerMessages)) ?>টি)
                            </h4>
                            <span class="text-[11px] text-slate-500 font-medium">সর্বশেষ প্রেরিত তথ্যসমূহ</span>
                        </div>

                        <div class="space-y-2.5 max-h-60 overflow-y-auto pr-1" id="devMessagesListContainer">
                            <?php foreach ($developerMessages as $dMsg): ?>
                                <div id="dev-msg-card-<?= $dMsg['id'] ?>" class="bg-white p-3.5 rounded-xl border border-sky-200/80 shadow-xs flex flex-col sm:flex-row sm:items-start justify-between gap-3 text-xs">
                                    <div class="space-y-1 flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-extrabold text-slate-800"><?= htmlspecialchars($dMsg['sender_name']) ?></span>
                                            <span class="text-slate-400 font-mono text-[11px]">(<?= htmlspecialchars($dMsg['sender_phone']) ?>)</span>
                                            <?php if (!empty($dMsg['subject'])): ?>
                                                <span class="px-2 py-0.5 rounded bg-sky-100 text-sky-800 text-[10px] font-bold"><?= htmlspecialchars($dMsg['subject']) ?></span>
                                            <?php endif; ?>
                                            <span class="text-[10px] text-slate-400 ml-auto sm:ml-0 font-medium"><?= \Core\BengaliHelper::formatDate($dMsg['created_at']) ?></span>
                                        </div>
                                        <p class="text-slate-700 whitespace-pre-wrap leading-relaxed bg-slate-50/80 p-2.5 rounded-lg border border-slate-100"><?= nl2br(htmlspecialchars($dMsg['message'])) ?></p>
                                    </div>
                                    <button type="button" onclick="deleteDevMessage(<?= $dMsg['id'] ?>)" class="text-red-500 hover:text-red-700 hover:bg-red-50 p-1.5 rounded-lg transition self-end sm:self-start shrink-0 cursor-pointer" title="বার্তাটি মুছে ফেলুন">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
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

                            <!-- ✍️ HUZUR INLINE NOTE / CUSTOM CHANGE WRITING BOX -->
                            <div class="mt-2.5 bg-amber-50/80 border border-amber-200/90 rounded-xl p-2.5">
                                <div class="flex items-center justify-between mb-1">
                                    <label for="card-note-<?= $dir['id'] ?>" class="text-[11px] font-extrabold text-amber-950 flex items-center">
                                        <svg class="w-3.5 h-3.5 mr-1 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        হুজুরের নোট / পরিবর্তন মন্তব্য:
                                    </label>
                                    <span id="card-note-saved-<?= $dir['id'] ?>" class="hidden text-[10px] text-emerald-800 font-bold bg-emerald-100 px-1.5 py-0.5 rounded shadow-xs">✓ সেভ হয়েছে</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <input type="text" 
                                           id="card-note-<?= $dir['id'] ?>" 
                                           value="<?= htmlspecialchars($reason ?: $remarks) ?>" 
                                           placeholder="হুজুরের নির্দেশ বা মন্তব্য লিখুন..." 
                                           class="flex-1 text-xs px-2.5 py-1.5 rounded-lg border border-amber-300 focus:ring-2 focus:ring-emerald-vibrant focus:border-emerald-600 bg-white placeholder-slate-400"
                                           onkeydown="if(event.key==='Enter'){ saveInlineNote(<?= $dir['id'] ?>, this.value); }">
                                    <button type="button" 
                                            onclick="saveInlineNote(<?= $dir['id'] ?>, document.getElementById('card-note-<?= $dir['id'] ?>').value)" 
                                            class="bg-amber-600 hover:bg-amber-700 text-white px-2.5 py-1.5 rounded-lg text-xs font-bold transition shadow-xs shrink-0 flex items-center cursor-pointer" title="নোট সংরক্ষণ করুন">
                                        <span>সেভ</span>
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

                            <!-- 1-Click ID Card Preview -->
                            <a href="<?= $baseUrl ?>/admin/id-card/director/<?= $dir['id'] ?>" target="_blank" class="bg-amber-500 hover:bg-amber-600 text-white py-2 px-2.5 rounded-xl text-xs font-extrabold shadow-sm transition flex items-center justify-center gap-1 border border-amber-600/60" title="অফিসিয়াল আইডি কার্ড ও কিউআর সনদ">
                                <span>🪪</span>
                                <span>আইডি</span>
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
                        <th class="px-4 py-3.5">হুজুরের নোট / পরিবর্তন মন্তব্য</th>
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
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-1.5">
                                    <input type="text" 
                                           id="table-note-<?= $dir['id'] ?>" 
                                           value="<?= htmlspecialchars($reason ?: $remarks) ?>" 
                                           placeholder="হুজুরের নির্দেশ বা মন্তব্য..." 
                                           class="w-44 sm:w-56 text-xs px-2.5 py-1.5 rounded-lg border border-amber-300 focus:ring-1 focus:ring-emerald-vibrant focus:border-emerald-600 bg-amber-50/30"
                                           onkeydown="if(event.key==='Enter'){ saveInlineNote(<?= $dir['id'] ?>, this.value); }">
                                    <button type="button" 
                                            onclick="saveInlineNote(<?= $dir['id'] ?>, document.getElementById('table-note-<?= $dir['id'] ?>').value)" 
                                            class="bg-amber-600 hover:bg-amber-700 text-white px-2 py-1.5 rounded-lg text-xs font-bold transition shrink-0 cursor-pointer" title="নোট সংরক্ষণ করুন">
                                        সেভ
                                    </button>
                                    <span id="table-note-saved-<?= $dir['id'] ?>" class="hidden text-[10px] text-emerald-800 font-bold bg-emerald-100 px-1.5 py-0.5 rounded shadow-xs">✓</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-right space-x-1">
                                <a href="<?= $baseUrl ?>/admin/directors/impersonate/<?= $dir['id'] ?>" class="inline-flex items-center px-2.5 py-1 bg-emerald-night hover:bg-emerald-deep text-white text-xs font-bold rounded-lg transition" title="ড্যাশবোর্ডে প্রবেশ">
                                    ড্যাশবোর্ড
                                </a>
                                <a href="<?= $baseUrl ?>/admin/id-card/director/<?= $dir['id'] ?>" target="_blank" class="inline-flex items-center px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg transition" title="অফিসিয়াল আইডি কার্ড">
                                    🪪 আইডি
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

<!-- ========================================================================= -->
<!-- NATIONWIDE TEACHERS HUB (সারাদেশের সকল শিক্ষক ও মুয়াল্লিম তালিকা) -->
<!-- ========================================================================= -->
<section id="teachers-hub" class="bg-[#fffefb] rounded-3xl shadow-sm border-2 border-[#e6dece] overflow-hidden mb-12">
    <div class="bg-gradient-to-r from-emerald-night via-emerald-deep to-emerald-night px-6 py-6 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                <span class="px-2.5 py-0.5 rounded-full bg-gold-rich text-white text-xs font-black uppercase tracking-wider shadow">সারাদেশের শিক্ষক তালিকা</span>
                <span class="text-xs text-emerald-100/90 font-medium">৬৪ জেলার মোট <?= \Core\BengaliHelper::toBengaliNumber(count($allTeachers ?? [])) ?> জন শিক্ষক</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-black text-white flex items-center">
                <i class="fas fa-chalkboard-user mr-2.5 text-gold-rich"></i>
                কেন্দ্রীয় শিক্ষক ও মুয়াল্লিম ডিরেক্টরি
            </h2>
            <p class="text-xs sm:text-sm text-emerald-100/90 mt-1">
                প্রত্যেক জেলা পরিচালকের অধীনস্থ শিক্ষক, তাদের দায়িত্বপ্রাপ্ত এলাকা ও শিক্ষার্থী সংখ্যা এক নজরে পর্যবেক্ষণ করুন।
            </p>
        </div>

        <!-- Search box -->
        <div class="relative w-full sm:w-72">
            <input type="text" id="teacherSearchInput" onkeyup="filterTeachersAdmin()" placeholder="শিক্ষকের নাম, মোবাইল বা জেলা..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gold-rich/50 bg-emerald-950/80 text-white placeholder-emerald-200/60 outline-none focus:ring-2 focus:ring-gold-rich">
        </div>
    </div>

    <div class="p-6">
        <?php if (!empty($allTeachers)): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5" id="teachersGridAdmin">
                <?php foreach ($allTeachers as $t): ?>
                    <div class="teacher-admin-card bg-[#fffefb] rounded-2xl border-2 border-[#e6dcce] hover:border-gold-rich p-4 shadow-sm transition"
                         data-name="<?= strtolower(htmlspecialchars($t['name'])) ?>"
                         data-district="<?= strtolower(htmlspecialchars($t['district_name'] ?? '')) ?>"
                         data-phone="<?= htmlspecialchars($t['phone']) ?>">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-800 flex items-center justify-center font-bold text-sm border border-emerald-200 shrink-0">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <div>
                                    <h4 class="font-black text-sm text-emerald-night leading-tight"><?= htmlspecialchars($t['name']) ?></h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5">
                                        <i class="fas fa-map-pin text-gold-deep mr-1"></i> <?= htmlspecialchars($t['area_name']) ?>
                                    </p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-black rounded-full shrink-0">
                                <?= htmlspecialchars($t['status'] ?? 'active') === 'active' ? 'সক্রিয়' : 'প্রশিক্ষণ' ?>
                            </span>
                        </div>

                        <div class="bg-[#fcfaf5] p-2.5 rounded-xl border border-[#efe6d5] my-2 text-xs flex justify-between items-center text-slate-700">
                            <span>জেলা: <strong><?= htmlspecialchars($t['district_name'] ?? '') ?></strong></span>
                            <span>ছাত্র: <strong><?= \Core\BengaliHelper::toBengaliNumber($t['total_students']) ?> জন</strong></span>
                        </div>

                        <div class="text-[11px] text-slate-500 mb-2">
                            পরিচালক: <strong class="text-emerald-night"><?= htmlspecialchars($t['director_name'] ?? '') ?></strong>
                        </div>

                        <div class="pt-2 border-t border-[#ede5d6] flex items-center gap-2">
                            <a href="tel:<?= htmlspecialchars($t['phone']) ?>" class="flex-1 py-1.5 px-2 bg-emerald-50 hover:bg-emerald-night hover:text-white border border-emerald-300 text-emerald-900 rounded-xl text-[11px] font-black transition text-center flex items-center justify-center gap-1">
                                <i class="fas fa-phone-alt text-emerald-600"></i> <?= htmlspecialchars($t['phone']) ?>
                            </a>
                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $t['phone']) ?>" target="_blank" class="w-8 h-8 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition flex items-center justify-center text-xs">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-xs text-slate-500 text-center py-6">কোনো শিক্ষকের তথ্য পাওয়া যায়নি।</p>
        <?php endif; ?>
    </div>
</section>

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

// Live Search for Teachers Hub
function filterTeachersAdmin() {
    const q = document.getElementById('teacherSearchInput').value.toLowerCase().trim();
    document.querySelectorAll('.teacher-admin-card').forEach(card => {
        const name = card.getAttribute('data-name') || '';
        const district = card.getAttribute('data-district') || '';
        const phone = card.getAttribute('data-phone') || '';
        if (q === '' || name.includes(q) || district.includes(q) || phone.includes(q)) {
            card.classList.remove('hidden');
        } else {
            card.classList.add('hidden');
        }
    });
}

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

// Save Inline Note for Huzur (Instant AJAX without reload)
function saveInlineNote(directorId, noteText) {
    const cardSaved = document.getElementById('card-note-saved-' + directorId);
    const tableSaved = document.getElementById('table-note-saved-' + directorId);
    
    const formData = new FormData();
    formData.append('director_id', directorId);
    formData.append('status_reason', noteText.trim());
    formData.append('ajax', '1');

    fetch(BASE_URL + '/admin/directors/status', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Flash saved badges
            if (cardSaved) {
                cardSaved.classList.remove('hidden');
                setTimeout(() => cardSaved.classList.add('hidden'), 2500);
            }
            if (tableSaved) {
                tableSaved.classList.remove('hidden');
                setTimeout(() => tableSaved.classList.add('hidden'), 2500);
            }

            // Sync both card and table inputs
            const cInput = document.getElementById('card-note-' + directorId);
            const tInput = document.getElementById('table-note-' + directorId);
            if (cInput && cInput.value !== noteText) cInput.value = noteText;
            if (tInput && tInput.value !== noteText) tInput.value = noteText;

            showToast(data.message || 'হুজুরের নোট সফলভাবে সংরক্ষিত হয়েছে!');
        } else {
            showToast(data.message || 'নোট সংরক্ষণে সমস্যা হয়েছে।', false);
        }
    })
    .catch(err => {
        console.error(err);
        showToast('সার্ভারের সাথে সংযোগ স্থাপন সম্ভব হয়নি।', false);
    });
}

// Toggle Add Director Text-box Panel
function toggleAddDirectorPanel() {
    const panel = document.getElementById('addNewDirectorPanel');
    if (!panel) return;
    
    panel.classList.toggle('hidden');
    if (!panel.classList.contains('hidden')) {
        panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
        const nameInput = document.getElementById('newDirName');
        if (nameInput) setTimeout(() => nameInput.focus(), 300);
    }
}

// Toggle Developer Message Text-box Panel
function toggleDeveloperMessagePanel() {
    const panel = document.getElementById('developerMessagePanel');
    if (!panel) return;

    panel.classList.toggle('hidden');
    if (!panel.classList.contains('hidden')) {
        panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
        const contentInput = document.getElementById('devMsgContent');
        if (contentInput) setTimeout(() => contentInput.focus(), 300);
    }
}

// Append quick template to developer message
function appendDevMsgTemplate(text) {
    const textarea = document.getElementById('devMsgContent');
    if (!textarea) return;
    if (textarea.value.trim() === '') {
        textarea.value = text;
    } else {
        textarea.value += "\n" + text;
    }
    textarea.focus();
}

// Submit Developer Message via AJAX
function submitDeveloperMessage(e) {
    e.preventDefault();
    const form = document.getElementById('sendDeveloperMessageForm');
    const formData = new FormData(form);
    const btn = document.getElementById('btnSubmitDevMsg');

    const originalBtnHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> <span>পাঠানো হচ্ছে...</span>';

    fetch(BASE_URL + '/admin/developer-message', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = originalBtnHtml;

        if (data.success) {
            showToast(data.message || 'ডেভেলপারের কাছে আপনার বার্তা সফলভাবে পাঠানো হয়েছে!');
            const msgInput = document.getElementById('devMsgContent');
            if (msgInput) msgInput.value = '';
            // Reload page smoothly to display newly inserted message in list
            setTimeout(() => {
                window.location.reload();
            }, 1200);
        } else {
            showToast(data.message || 'বার্তা পাঠাতে সমস্যা হয়েছে।', false);
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = originalBtnHtml;
        showToast('সার্ভার যোগাযোগে সমস্যা হয়েছে।', false);
    });
}

// Delete Developer Message via AJAX
function deleteDevMessage(id) {
    if (!confirm('আপনি কি নিশ্চিতভাবে এই বার্তাটি মুছে ফেলতে চান?')) return;

    fetch(BASE_URL + '/admin/developer-message/delete/' + id, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('বার্তাটি মুছে ফেলা হয়েছে।');
            const card = document.getElementById('dev-msg-card-' + id);
            if (card) {
                card.remove();
            }
        } else {
            showToast('বার্তা ডিলিট করতে সমস্যা হয়েছে।', false);
        }
    })
    .catch(err => {
        showToast('সার্ভার যোগাযোগে সমস্যা হয়েছে।', false);
    });
}

// Submit New Director Form via AJAX
function submitNewDirector(e) {
    e.preventDefault();
    const form = document.getElementById('createDirectorForm');
    const formData = new FormData(form);
    const btn = document.getElementById('btnSubmitDirector');

    const originalBtnHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-amber-300 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> <span>যুক্ত করা হচ্ছে...</span>';

    fetch(BASE_URL + '/admin/directors/create', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = originalBtnHtml;

        if (data.success) {
            showToast(data.message || 'নতুন পরিচালক সফলভাবে যুক্ত হয়েছে!');
            form.reset();
            // Reload page smoothly to display new director card, options & counters
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showToast(data.message || 'ত্রুটি ঘটেছে, পুনরায় চেষ্টা করুন।', false);
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = originalBtnHtml;
        showToast('সার্ভার যোগাযোগে সমস্যা হয়েছে।', false);
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
