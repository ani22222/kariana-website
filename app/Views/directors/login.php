<?php
/**
 * District Director Portal Login View - Kariana Quran
 */
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';
$error = \Core\Session::getFlash('error');
$success = \Core\Session::getFlash('success');
?>
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-3xl shadow-xl border border-emerald-100">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-emerald-night text-gold-shimmer mb-4 shadow-lg border border-gold-rich/40">
                <i class="fas fa-user-tie text-2xl"></i>
            </div>
            <span class="inline-block px-3 py-1 bg-gold-100 text-gold-deep text-xs font-bold rounded-full mb-1">পরিচালক এক্সেস</span>
            <h2 class="text-2xl font-bold text-emerald-night">জেলা পরিচালক পোর্টাল</h2>
            <p class="mt-1 text-xs text-slate-500">আপনার জেলার শিক্ষক ও প্রকাশনা হিসাব পরিচালনা করুন</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r text-red-700 text-xs">
                <i class="fas fa-exclamation-circle mr-1.5"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r text-emerald-700 text-xs">
                <i class="fas fa-check-circle mr-1.5"></i><?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form class="mt-6 space-y-5" action="<?= $baseUrl ?>/director/login" method="POST">
            <?= $csrfField ?>

            <div>
                <label for="username" class="block text-xs font-bold text-slate-700 mb-1">ইউজারনেম বা মোবাইল নম্বর</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <i class="fas fa-user"></i>
                    </span>
                    <input id="username" name="username" type="text" required autofocus
                        class="block w-full pl-10 pr-3 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-vibrant focus:outline-none"
                        placeholder="যেমন: director_1 বা 01711234567">
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 mb-1">গোপন পাসওয়ার্ড</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input id="password" name="password" type="password" required
                        class="block w-full pl-10 pr-3 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-vibrant focus:outline-none"
                        placeholder="••••••••">
                </div>
            </div>

            <button type="submit"
                class="w-full flex justify-center py-3 px-4 rounded-xl shadow-lg text-sm font-bold text-white bg-emerald-night hover:bg-emerald-deep focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-vibrant transition">
                লগইন করুন <i class="fas fa-sign-in-alt ml-2 mt-0.5"></i>
            </button>

            <div class="text-center pt-2">
                <p class="text-xs text-slate-400">
                    ডিফল্ট পাসওয়ার্ড: <code class="bg-slate-100 text-emerald-night px-1.5 py-0.5 rounded font-mono font-bold">director123</code>
                </p>
                <a href="<?= $baseUrl ?>/directors" class="inline-block mt-3 text-xs text-emerald-vibrant hover:text-emerald-night font-bold">
                    <i class="fas fa-arrow-left mr-1"></i> পাবলিক পরিচালক তালিকায় ফিরে যান
                </a>
            </div>
        </form>
    </div>
</div>
