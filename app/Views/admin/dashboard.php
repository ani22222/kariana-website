<?php
/**
 * Admin Dashboard View - Kariana Quran
 */
$user = \Core\Session::getUser();
?>
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between pb-6 border-b border-slate-200 mb-8">
        <div>
            <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-night text-xs font-bold rounded-full mb-2">অ্যাডমিন ড্যাশবোর্ড</span>
            <h1 class="text-3xl font-bold text-emerald-night">কারিয়ানা কুরআন কন্ট্রোল প্যানেল</h1>
            <p class="text-sm text-slate-500">স্বাগতম, <?= htmlspecialchars($user['name'] ?? 'এডমিন') ?> | সর্বশেষ ডাটাবেস ও সাইট পারফরম্যান্স ওভারভিউ</p>
        </div>
        <div class="mt-4 md:mt-0 flex flex-wrap gap-3">
            <a href="<?= $baseUrl ?>/admin/settings" class="bg-gold-rich hover:bg-gold-deep text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow transition flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
                মার্কেটিং ও স্ক্রিপ্ট সেটিংস
            </a>
            <a href="<?= $baseUrl ?>/admin/logout" class="bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 px-4 py-2.5 rounded-xl text-sm font-bold transition flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                লগআউট
            </a>
        </div>
    </div>

    <!-- Quick Navigation Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <a href="<?= $baseUrl ?>/admin/posts" class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 hover:border-emerald-vibrant hover:shadow-md transition flex items-center space-x-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-night flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 text-sm">ব্লগ ও নিউজ</h4>
                <p class="text-xs text-slate-400">পোস্ট ম্যানেজমেন্ট</p>
            </div>
        </a>
        <a href="<?= $baseUrl ?>/admin/courses" class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 hover:border-emerald-vibrant hover:shadow-md transition flex items-center space-x-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-night flex items-center justify-center font-bold">
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
        <a href="<?= $baseUrl ?>/admin/admissions" class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 hover:border-emerald-vibrant hover:shadow-md transition flex items-center space-x-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-night flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 text-sm">ভর্তি আবেদন</h4>
                <p class="text-xs text-slate-400">স্টুডেন্ট ইনবক্স</p>
            </div>
        </a>
        <a href="<?= $baseUrl ?>/admin/books" class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 hover:border-emerald-vibrant hover:shadow-md transition flex items-center space-x-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-night flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 text-sm">বই ও প্রকাশনা</h4>
                <p class="text-xs text-slate-400">কারিয়ানা কুরআন</p>
            </div>
        </a>
    </div>

    <!-- Stat Counters -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">মোট ব্লগ পোস্ট</p>
                    <h3 class="text-3xl font-extrabold text-emerald-night mt-1"><?= \Core\BengaliHelper::toBengaliNumber($stats['posts'] ?? 0) ?></h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-vibrant flex items-center justify-center text-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">মোট কোর্স</p>
                    <h3 class="text-3xl font-extrabold text-emerald-night mt-1"><?= \Core\BengaliHelper::toBengaliNumber($stats['courses'] ?? 0) ?></h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-vibrant flex items-center justify-center text-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">মোট ভর্তি আবেদন</p>
                    <h3 class="text-3xl font-extrabold text-gold-deep mt-1"><?= \Core\BengaliHelper::toBengaliNumber($stats['admissions'] ?? 0) ?></h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gold-50 text-gold-rich flex items-center justify-center text-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">মোট বই ও প্রকাশনা</p>
                    <h3 class="text-3xl font-extrabold text-emerald-night mt-1"><?= \Core\BengaliHelper::toBengaliNumber($stats['books'] ?? 0) ?></h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-vibrant flex items-center justify-center text-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Admissions Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
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
