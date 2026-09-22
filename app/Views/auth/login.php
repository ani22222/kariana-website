<?php
/**
 * Universal Multi-Role Authentication & Self-Registration View
 * Dedicated Single Door Entry - Kariana Quran
 */
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';
$activeTab = $activeTab ?? 'login';
$prefillPhone = $prefillPhone ?? '';
$success = \Core\Session::getFlash('success');
$error = \Core\Session::getFlash('error');
$info = \Core\Session::getFlash('info');
$unregisteredPhone = \Core\Session::getFlash('unregistered_phone');
?>

<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 bg-[#f8f5ee]">
    <div class="max-w-lg w-full bg-[#fffefb] border-2 border-[#e7dec6] rounded-3xl p-6 sm:p-10 shadow-xl relative overflow-hidden" 
         x-data="{ 
             tab: '<?= $activeTab === 'register' ? 'register' : 'login' ?>',
             demoMenu: false,
             phoneInput: '<?= htmlspecialchars($prefillPhone) ?>',
             isChecking: false,
             phoneStatus: null,
             checkPhoneTimer: null,
             fillDemo(user, pass) {
                 this.tab = 'login';
                 $nextTick(() => {
                     const idInput = document.getElementById('login-identifier');
                     const passInput = document.getElementById('login-password');
                     if (idInput && passInput) {
                         idInput.value = user;
                         passInput.value = pass;
                         this.phoneInput = user;
                         document.getElementById('universal-login-form').submit();
                     }
                 });
             },
             onPhoneBlur() {
                 let val = this.phoneInput.trim();
                 if (val.length >= 11 && /^[0-9+]+$/.test(val)) {
                     this.isChecking = true;
                     fetch('<?= $baseUrl ?>/api/check-phone?phone=' + encodeURIComponent(val))
                         .then(r => r.json())
                         .then(d => {
                             this.isChecking = false;
                             this.phoneStatus = d.exists ? 'registered' : 'unregistered';
                         })
                         .catch(() => { this.isChecking = false; });
                 } else {
                     this.phoneStatus = null;
                 }
             }
         }">

        <!-- Demo Quick Switcher Header Banner -->
        <div class="mb-6 bg-gradient-to-r from-emerald-900 to-emerald-950 text-white rounded-2xl p-3 sm:p-4 border border-gold-rich/40 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="w-7 h-7 rounded-lg bg-gold-rich text-white flex items-center justify-center text-xs font-bold shadow">
                        <i class="fas fa-bolt"></i>
                    </span>
                    <div>
                        <h4 class="text-xs sm:text-sm font-bold text-gold-shimmer">ডেমো ওয়ান-ক্লিক লগইন</h4>
                        <p class="text-[11px] text-emerald-200/80">যেকোনো রোলে এক ক্লিকে টেস্ট লগইন করুন</p>
                    </div>
                </div>
                <button type="button" @click="demoMenu = !demoMenu" 
                        class="text-xs bg-emerald-800 hover:bg-emerald-700 text-gold-shimmer px-3 py-1.5 rounded-xl font-bold border border-gold-rich/30 transition flex items-center">
                    <span>রোল নির্বাচন</span>
                    <i class="fas fa-chevron-down text-[10px] ml-1.5 transition-transform" :class="{'rotate-180': demoMenu}"></i>
                </button>
            </div>

            <!-- Demo Quick Options Dropdown -->
            <div x-show="demoMenu" x-transition class="mt-3 pt-3 border-t border-emerald-800/80 grid grid-cols-2 gap-2 text-xs">
                <button type="button" @click="fillDemo('admin', 'kariana2026!')" 
                        class="bg-white/10 hover:bg-white/20 p-2 rounded-xl text-left border border-white/10 transition">
                    <strong class="text-gold-shimmer block text-[11px]">🛡️ কেন্দ্রীয় অ্যাডমিন</strong>
                    <span class="text-[10px] text-slate-300 font-mono">admin / 2026!</span>
                </button>
                <button type="button" @click="fillDemo('manager', 'kariana2026!')" 
                        class="bg-white/10 hover:bg-white/20 p-2 rounded-xl text-left border border-white/10 transition">
                    <strong class="text-emerald-300 block text-[11px]">💼 হিসাব ব্যবস্থাপক</strong>
                    <span class="text-[10px] text-slate-300 font-mono">manager / 2026!</span>
                </button>
                <button type="button" @click="fillDemo('01711756391', 'kariana2026!')" 
                        class="bg-white/10 hover:bg-white/20 p-2 rounded-xl text-left border border-white/10 transition">
                    <strong class="text-amber-300 block text-[11px]">🏢 জেলা পরিচালক</strong>
                    <span class="text-[10px] text-slate-300 font-mono">মাগুরা / 2026!</span>
                </button>
                <button type="button" @click="fillDemo('teacher01', 'kariana2026!')" 
                        class="bg-white/10 hover:bg-white/20 p-2 rounded-xl text-left border border-white/10 transition">
                    <strong class="text-teal-300 block text-[11px]">📖 মুয়াল্লিম / শিক্ষক</strong>
                    <span class="text-[10px] text-slate-300 font-mono">teacher01 / 2026!</span>
                </button>
            </div>
        </div>

        <!-- Brand Icon & Title -->
        <div class="text-center mb-6">
            <div class="w-14 h-14 bg-emerald-deep text-gold-shimmer rounded-2xl flex items-center justify-center text-2xl mx-auto mb-3 border-2 border-gold-rich shadow-md">
                <i class="fas fa-quran"></i>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-emerald-night">কারিয়ানা কুরআন সেবা পোর্টাল</h1>
            <p class="text-xs text-slate-500 mt-1">কুরআন প্রচার, শিক্ষা ও প্রাতিষ্ঠানিক সমন্বয় প্ল্যাটফর্ম</p>
        </div>

        <!-- Flash Messages -->
        <?php if ($success): ?>
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-3 rounded-r text-emerald-800 text-xs mb-4">
                <i class="fas fa-check-circle mr-1.5"></i><?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-rose-50 border-l-4 border-rose-500 p-3 rounded-r text-rose-800 text-xs mb-4">
                <i class="fas fa-exclamation-circle mr-1.5"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($info): ?>
            <div class="bg-blue-50 border-l-4 border-blue-500 p-3 rounded-r text-blue-800 text-xs mb-4">
                <i class="fas fa-info-circle mr-1.5"></i><?= htmlspecialchars($info) ?>
            </div>
        <?php endif; ?>

        <!-- Tabs Navigation -->
        <div class="flex border-b border-[#e4dccb] mb-6">
            <button type="button" @click="tab = 'login'" 
                    :class="{'border-b-2 border-emerald-deep text-emerald-night font-extrabold': tab === 'login', 'text-slate-500 font-medium': tab !== 'login'}"
                    class="flex-1 py-2.5 text-center text-xs sm:text-sm transition">
                <i class="fas fa-sign-in-alt mr-1.5 text-gold-rich"></i> লগইন করুন
            </button>
            <button type="button" @click="tab = 'register'" 
                    :class="{'border-b-2 border-emerald-deep text-emerald-night font-extrabold': tab === 'register', 'text-slate-500 font-medium': tab !== 'register'}"
                    class="flex-1 py-2.5 text-center text-xs sm:text-sm transition">
                <i class="fas fa-user-plus mr-1.5 text-gold-rich"></i> নতুন অ্যাকাউন্ট খুলুন
            </button>
        </div>

        <!-- ================= TAB 1: LOGIN FORM ================= -->
        <div x-show="tab === 'login'" x-transition>
            <!-- Intelligent Unregistered Phone Prompt -->
            <div x-show="phoneStatus === 'unregistered'" class="mb-4 p-3 bg-amber-50 border border-amber-300 rounded-2xl text-xs text-amber-900 flex items-start space-x-2">
                <i class="fas fa-exclamation-triangle text-amber-600 mt-0.5 mr-1.5"></i>
                <div class="flex-1">
                    <p class="font-bold">নম্বরটি আগে নিবন্ধিত নয়!</p>
                    <p class="text-[11px] text-amber-800 mt-0.5">আপনি কি নতুন শিক্ষার্থী/সদস্য হিসেবে অ্যাকাউন্ট খুলতে চান?</p>
                    <button type="button" @click="tab = 'register'" class="mt-2 inline-block bg-amber-600 hover:bg-amber-700 text-white font-bold px-3 py-1 rounded-lg text-[11px] shadow-sm">
                        হ্যাঁ, নতুন সাইন-আপ করুন &rarr;
                    </button>
                </div>
            </div>

            <form id="universal-login-form" action="<?= $baseUrl ?>/login" method="POST" class="space-y-4 text-xs">
                <?= \Core\Csrf::field() ?>
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect ?? '') ?>">

                <div>
                    <label class="block font-bold text-slate-700 mb-1">মোবাইল নম্বর অথবা ইউজারনেম</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" id="login-identifier" name="identifier" required x-model="phoneInput" @blur="onPhoneBlur()"
                            placeholder="যেমন: 017xxxxxxxx বা ইউজারনেম" 
                            class="w-full pl-10 pr-4 py-2.5 bg-[#fdfcf8] border border-[#d8cfbe] rounded-xl focus:ring-2 focus:ring-emerald-vibrant focus:border-transparent text-slate-800 text-xs font-medium">
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block font-bold text-slate-700">পাসওয়ার্ড</label>
                        <a href="https://wa.me/8801711756391?text=পাসওয়ার্ড%20রিসেট%20সহায়তা" target="_blank" class="text-[11px] text-emerald-deep hover:underline">পাসওয়ার্ড ভুলে গেছেন?</a>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" id="login-password" name="password" required 
                            placeholder="আপনার গোপন পাসওয়ার্ড" 
                            class="w-full pl-10 pr-4 py-2.5 bg-[#fdfcf8] border border-[#d8cfbe] rounded-xl focus:ring-2 focus:ring-emerald-vibrant focus:border-transparent text-slate-800 text-xs">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" 
                        class="w-full bg-emerald-night hover:bg-emerald-deep text-white py-3 rounded-xl font-bold transition shadow-md flex items-center justify-center text-xs sm:text-sm">
                        <i class="fas fa-sign-in-alt mr-2 text-gold-shimmer"></i> লগইন করুন
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-4 border-t border-[#ede4d4] text-center text-xs text-slate-500">
                নতুন ব্যবহারকারী? 
                <button type="button" @click="tab = 'register'" class="font-bold text-emerald-deep hover:underline ml-1">
                    এখানে ক্লিক করে সাইন-আপ করুন
                </button>
            </div>
        </div>

        <!-- ================= TAB 2: REGISTER FORM ================= -->
        <div x-show="tab === 'register'" x-transition>
            <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-2xl p-3 text-xs text-emerald-900 mb-4">
                <i class="fas fa-graduation-cap text-emerald-vibrant mr-1"></i> 
                <strong>শিক্ষার্থী ও শুভাকাঙ্ক্ষী অ্যাকাউন্ট:</strong> সহজেই নিবন্ধন করে আপনার পছন্দের কুরআন কোর্সে ভর্তি ও বই অর্ডার করুন।
            </div>

            <form action="<?= $baseUrl ?>/register" method="POST" class="space-y-3.5 text-xs">
                <?= \Core\Csrf::field() ?>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">আপনার পূর্ণ নাম *</label>
                    <input type="text" name="name" required placeholder="যেমন: মোহাম্মদ আব্দুল্লাহ" 
                        class="w-full px-3.5 py-2.5 bg-[#fdfcf8] border border-[#d8cfbe] rounded-xl focus:ring-2 focus:ring-emerald-vibrant text-slate-800 text-xs">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">মোবাইল নম্বর (১১ ডিজিট) *</label>
                    <input type="text" name="phone" required x-model="phoneInput" placeholder="017xxxxxxxx" 
                        class="w-full px-3.5 py-2.5 bg-[#fdfcf8] border border-[#d8cfbe] rounded-xl focus:ring-2 focus:ring-emerald-vibrant text-slate-800 text-xs font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">জেলা (ঐচ্ছিক)</label>
                    <input type="text" name="district" placeholder="যেমন: ঢাকা, মাগুরা, কুমিল্লা..." 
                        class="w-full px-3.5 py-2.5 bg-[#fdfcf8] border border-[#d8cfbe] rounded-xl focus:ring-2 focus:ring-emerald-vibrant text-slate-800 text-xs">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">পাসওয়ার্ড (কমপক্ষে ৬ অক্ষর) *</label>
                    <input type="password" name="password" required minlength="6" placeholder="নতুন গোপন পাসওয়ার্ড দিন" 
                        class="w-full px-3.5 py-2.5 bg-[#fdfcf8] border border-[#d8cfbe] rounded-xl focus:ring-2 focus:ring-emerald-vibrant text-slate-800 text-xs">
                </div>

                <div class="pt-2">
                    <button type="submit" 
                        class="w-full bg-emerald-deep hover:bg-emerald-night text-white py-3 rounded-xl font-bold transition shadow-md flex items-center justify-center text-xs sm:text-sm">
                        <i class="fas fa-user-plus mr-2 text-gold-shimmer"></i> অ্যাকাউন্ট তৈরি করুন
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-4 border-t border-[#ede4d4] text-center text-xs text-slate-500">
                ইতিমধ্যে অ্যাকাউন্ট রয়েছে? 
                <button type="button" @click="tab = 'login'" class="font-bold text-emerald-deep hover:underline ml-1">
                    লগইন পেজে যান
                </button>
            </div>
        </div>

        <div class="mt-6 text-center text-[11px] text-slate-400">
            কারিয়ানা কুরআন শিক্ষা সোসাইটি &bull; নিরাপদ ও সুরক্ষিত প্ল্যাটফর্ম
        </div>
    </div>
</div>
