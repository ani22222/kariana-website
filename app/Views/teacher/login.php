<?php
/**
 * Teacher / Muallim Login View - Kariana Quran
 */
$csrfToken = \Core\Csrf::getToken();
?>
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 bg-[#f8f5ee]">
    <div class="max-w-md w-full bg-[#fffefb] border-2 border-gold-rich/40 rounded-3xl p-8 shadow-xl relative overflow-hidden">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-emerald-deep text-gold-shimmer rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4 border-2 border-gold-rich shadow-md">
                <i class="fas fa-chalkboard-user"></i>
            </div>
            <div class="inline-block px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-full text-xs font-bold mb-2">
                ক্বারীয়ানা মুয়াল্লিম পোর্টাল
            </div>
            <h1 class="text-2xl font-extrabold text-emerald-night">সম্মানিত শিক্ষক লগইন</h1>
            <p class="text-xs text-slate-500 mt-1">সবক ক্লাস আবেদন, জেলা পরিচালকের নিকট বই অর্ডার ও পাঠদান সমস্যা</p>
        </div>

        <?php if ($error = \Core\Session::flash('error')): ?>
        <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs font-bold flex items-center">
            <i class="fas fa-exclamation-circle mr-2 text-base"></i> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <?php if ($info = \Core\Session::flash('info')): ?>
        <div class="mb-4 p-3 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 text-xs font-bold flex items-center">
            <i class="fas fa-info-circle mr-2 text-base"></i> <?= htmlspecialchars($info) ?>
        </div>
        <?php endif; ?>

        <form action="<?= $baseUrl ?>/teacher/login" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">শিক্ষক ইউজারনেম / মোবাইল</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <i class="fas fa-user-tie"></i>
                    </span>
                    <input type="text" name="username" required placeholder="teacher01" 
                           class="w-full pl-10 pr-4 py-2.5 border border-[#d6ccb9] rounded-xl text-sm font-semibold bg-[#fdfbf7] focus:ring-2 focus:ring-emerald-deep focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">গোপন পাসওয়ার্ড</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" required placeholder="••••••••" 
                           class="w-full pl-10 pr-4 py-2.5 border border-[#d6ccb9] rounded-xl text-sm font-semibold bg-[#fdfbf7] focus:ring-2 focus:ring-emerald-deep focus:outline-none">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full bg-emerald-deep hover:bg-emerald-night text-white font-bold py-3 rounded-xl shadow-lg transition flex items-center justify-center">
                    <i class="fas fa-sign-in-alt mr-2 text-gold-shimmer"></i> শিক্ষক ড্যাশবোর্ডে প্রবেশ
                </button>
            </div>
        </form>

        <div class="mt-6 pt-4 border-t border-[#ede5d5] text-center">
            <p class="text-xs text-slate-400">
                ডেমো শিক্ষক অ্যাকাউন্ট: <code class="bg-slate-100 text-slate-700 px-1 py-0.5 rounded font-mono">teacher01</code> / <code class="bg-slate-100 text-slate-700 px-1 py-0.5 rounded font-mono">kariana2026!</code>
            </p>
            <div class="mt-3 flex justify-center space-x-4 text-xs font-bold text-emerald-deep">
                <a href="<?= $baseUrl ?>/director/login" class="hover:underline">জেলা পরিচালক লগইন</a>
                <span>•</span>
                <a href="<?= $baseUrl ?>/manager/login" class="hover:underline">ম্যানেজার লগইন</a>
            </div>
        </div>
    </div>
</div>
