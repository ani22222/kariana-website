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

        <form class="mt-8 space-y-6" action="<?= $baseUrl ?>/admin/login" method="POST">
            <?= $csrfField ?>

            <div class="space-y-4">
                <div>
                    <label for="username" class="block text-sm font-semibold text-slate-700">ব্যবহারকারী নাম বা ইমেইল</label>
                    <div class="mt-1 relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <i class="fas fa-user"></i>
                        </span>
                        <input id="username" name="username" type="text" required autofocus
                            class="appearance-none block w-full pl-10 pr-3 py-3 border border-slate-300 rounded-xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-vibrant focus:border-emerald-vibrant sm:text-sm"
                            placeholder="admin">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700">পাসওয়ার্ড</label>
                    <div class="mt-1 relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <i class="fas fa-key"></i>
                        </span>
                        <input id="password" name="password" type="password" required
                            class="appearance-none block w-full pl-10 pr-3 py-3 border border-slate-300 rounded-xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-vibrant focus:border-emerald-vibrant sm:text-sm"
                            placeholder="••••••••">
                    </div>
                </div>
            </div>

            <div>
                <button type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-lg text-base font-bold text-white bg-emerald-night hover:bg-emerald-deep focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-vibrant transition">
                    লগইন করুন <i class="fas fa-sign-in-alt ml-2 mt-1"></i>
                </button>
            </div>
            
            <div class="text-center text-xs text-slate-400">
                ডিফল্ট এডমিন তথ্য: ইউজার: <code class="bg-slate-100 px-1 py-0.5 rounded text-emerald-night">admin</code> | পাসওয়ার্ড: <code class="bg-slate-100 px-1 py-0.5 rounded text-emerald-night">admin123</code>
            </div>
        </form>
    </div>
</div>
