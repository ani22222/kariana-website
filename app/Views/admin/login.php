<?php
/**
 * Admin Login Page - Kariana Quran
 */
$error = \Core\Session::getFlash('error');
$success = \Core\Session::getFlash('success');
?>
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-2xl shadow-xl border border-emerald-100">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-100 text-emerald-night mb-4">
                <i class="fas fa-lock text-2xl text-emerald-vibrant"></i>
            </div>
            <h2 class="text-2xl font-bold text-emerald-night">কারিয়ানা কুরআন — অ্যাডমিন লগইন</h2>
            <p class="mt-2 text-sm text-slate-500">সিএমএস এবং ডাটাবেস ব্যবস্থাপনায় প্রবেশ করুন</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r text-red-700 text-sm">
                <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r text-emerald-700 text-sm">
                <i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <!-- One-Click Instant Owner Login for Maulana Saddam Hossain -->
        <div class="bg-gradient-to-r from-emerald-950 via-emerald-900 to-emerald-800 p-4 rounded-2xl border-2 border-amber-400/60 shadow-lg text-center">
            <div class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-amber-400 text-emerald-950 font-bold mb-2 shadow">
                <i class="fas fa-crown text-base"></i>
            </div>
            <h3 class="text-white font-bold text-sm">সম্মানিত প্রতিষ্ঠাতা ও মালিক</h3>
            <p class="text-emerald-200 text-xs mt-0.5">মাওলানা সাদ্দাম হোসেন — কোনো পাসওয়ার্ড ছাড়াই সরাসরি প্রবেশ</p>
            <form action="<?= $baseUrl ?>/admin/login" method="POST" class="mt-3">
                <?= $csrfField ?>
                <input type="hidden" name="username" value="01717056816">
                <button type="submit" class="w-full bg-amber-400 hover:bg-amber-300 text-emerald-950 font-black py-2.5 px-4 rounded-xl text-sm transition shadow flex items-center justify-center space-x-2">
                    <i class="fas fa-sign-in-alt text-emerald-900"></i>
                    <span>মালিক হিসেবে ১-ক্লিকে সরাসরি ড্যাশবোর্ডে প্রবেশ</span>
                </button>
            </form>
        </div>

        <div class="relative flex py-1 items-center">
            <div class="flex-grow border-t border-slate-200"></div>
            <span class="flex-shrink mx-3 text-slate-400 text-xs">অথবা সাধারণ লগইন</span>
            <div class="flex-grow border-t border-slate-200"></div>
        </div>

        <form class="space-y-5" action="<?= $baseUrl ?>/admin/login" method="POST" id="adminLoginForm">
            <?= $csrfField ?>

            <div class="space-y-4">
                <div>
                    <label for="username" class="block text-sm font-semibold text-slate-700">মোবাইল নম্বর / ব্যবহারকারী নাম</label>
                    <div class="mt-1 relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <i class="fas fa-user"></i>
                        </span>
                        <input id="username" name="username" type="text" required autofocus
                            class="appearance-none block w-full pl-10 pr-3 py-3 border border-slate-300 rounded-xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-vibrant focus:border-emerald-vibrant sm:text-sm"
                            placeholder="01717056816 অথবা admin">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700">পাসওয়ার্ড</label>
                    <div class="mt-1 relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <i class="fas fa-key"></i>
                        </span>
                        <input id="password" name="password" type="password"
                            class="appearance-none block w-full pl-10 pr-3 py-3 border border-slate-300 rounded-xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-vibrant focus:border-emerald-vibrant sm:text-sm"
                            placeholder="মালিকের জন্য পাসওয়ার্ড প্রয়োজন নেই">
                    </div>
                    <p class="text-[11px] text-emerald-700 mt-1 font-medium">
                        <i class="fas fa-check-circle mr-1"></i><span class="text-amber-800 font-bold">০১৭১৭০৫৬৮১৬</span> অথবা <span class="text-amber-800 font-bold">admin</span> লিখলে পাসওয়ার্ড ছাড়াই সরাসরি প্রবেশ হবে।
                    </p>
                </div>
            </div>

            <div>
                <button type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-lg text-base font-bold text-white bg-emerald-night hover:bg-emerald-deep focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-vibrant transition">
                    লগইন করুন <i class="fas fa-sign-in-alt ml-2 mt-1"></i>
                </button>
            </div>
            
            <div class="text-center text-xs text-slate-400">
                মালিক: <code class="bg-slate-100 px-1 py-0.5 rounded text-emerald-night font-bold">01717056816</code> | পাসওয়ার্ড: <span class="text-emerald-700 font-bold">প্রয়োজন নেই</span>
            </div>
        </form>
    </div>
</div>
