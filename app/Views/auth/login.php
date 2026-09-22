<?php
/**
 * Universal Multi-Role Authentication & Self-Registration View
 * Dedicated Single Door Entry - Kariana Quran
 * 100% SVG Vector Icons - Zero Emojis
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
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </span>
                    <div>
                        <h4 class="text-xs sm:text-sm font-bold text-gold-shimmer">ডেমো ওয়ান-ক্লিক লগইন</h4>
                        <p class="text-[11px] text-emerald-200/80">যেকোনো রোলে এক ক্লিকে টেস্ট লগইন করুন</p>
                    </div>
                </div>
                <button type="button" @click="demoMenu = !demoMenu" 
                        class="text-xs bg-emerald-800 hover:bg-emerald-700 text-gold-shimmer px-3 py-1.5 rounded-xl font-bold border border-gold-rich/30 transition flex items-center">
                    <span>রোল নির্বাচন</span>
                    <svg class="w-3.5 h-3.5 ml-1.5 transition-transform" :class="{'rotate-180': demoMenu}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>

            <!-- Demo Quick Options Dropdown (Pure SVGs, Zero Emojis) -->
            <div x-show="demoMenu" x-transition class="mt-3 pt-3 border-t border-emerald-800/80 grid grid-cols-2 gap-2 text-xs">
                <button type="button" @click="fillDemo('admin', 'kariana2026!')" 
                        class="bg-white/10 hover:bg-white/20 p-2 rounded-xl text-left border border-white/10 transition group">
                    <div class="flex items-center space-x-1.5 text-gold-shimmer text-[11px] font-bold">
                        <svg class="w-3.5 h-3.5 text-amber-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span>কেন্দ্রীয় অ্যাডমিন</span>
                    </div>
                    <span class="text-[10px] text-slate-300 font-mono block mt-0.5">admin / 2026!</span>
                </button>

                <button type="button" @click="fillDemo('manager', 'kariana2026!')" 
                        class="bg-white/10 hover:bg-white/20 p-2 rounded-xl text-left border border-white/10 transition group">
                    <div class="flex items-center space-x-1.5 text-emerald-300 text-[11px] font-bold">
                        <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>হিসাব ব্যবস্থাপক</span>
                    </div>
                    <span class="text-[10px] text-slate-300 font-mono block mt-0.5">manager / 2026!</span>
                </button>

                <button type="button" @click="fillDemo('01711756391', 'kariana2026!')" 
                        class="bg-white/10 hover:bg-white/20 p-2 rounded-xl text-left border border-white/10 transition group">
                    <div class="flex items-center space-x-1.5 text-amber-300 text-[11px] font-bold">
                        <svg class="w-3.5 h-3.5 text-amber-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>জেলা পরিচালক</span>
                    </div>
                    <span class="text-[10px] text-slate-300 font-mono block mt-0.5">মাগুরা / 2026!</span>
                </button>

                <button type="button" @click="fillDemo('teacher01', 'kariana2026!')" 
                        class="bg-white/10 hover:bg-white/20 p-2 rounded-xl text-left border border-white/10 transition group">
                    <div class="flex items-center space-x-1.5 text-teal-300 text-[11px] font-bold">
                        <svg class="w-3.5 h-3.5 text-teal-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <span>মুয়াল্লিম / শিক্ষক</span>
                    </div>
                    <span class="text-[10px] text-slate-300 font-mono block mt-0.5">teacher01 / 2026!</span>
                </button>
            </div>
        </div>

        <!-- Brand Icon & Title -->
        <div class="text-center mb-6">
            <div class="w-14 h-14 bg-emerald-deep text-amber-300 rounded-2xl flex items-center justify-center mx-auto mb-3 border-2 border-gold-rich shadow-md">
                <svg class="w-8 h-8 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-emerald-night">কারিয়ানা কুরআন সেবা পোর্টাল</h1>
            <p class="text-xs text-slate-500 mt-1">কুরআন প্রচার, শিক্ষা ও প্রাতিষ্ঠানিক সমন্বয় প্ল্যাটফর্ম</p>
        </div>

        <!-- Flash Messages -->
        <?php if ($success): ?>
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-3 rounded-r text-emerald-800 text-xs mb-4 flex items-center">
                <svg class="w-4 h-4 mr-2 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span><?= htmlspecialchars($success) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-rose-50 border-l-4 border-rose-500 p-3 rounded-r text-rose-800 text-xs mb-4 flex items-center">
                <svg class="w-4 h-4 mr-2 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($info): ?>
            <div class="bg-blue-50 border-l-4 border-blue-500 p-3 rounded-r text-blue-800 text-xs mb-4 flex items-center">
                <svg class="w-4 h-4 mr-2 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span><?= htmlspecialchars($info) ?></span>
            </div>
        <?php endif; ?>

        <!-- Tabs Navigation -->
        <div class="flex border-b border-[#e4dccb] mb-6">
            <button type="button" @click="tab = 'login'" 
                    :class="{'border-b-2 border-emerald-deep text-emerald-night font-extrabold': tab === 'login', 'text-slate-500 font-medium': tab !== 'login'}"
                    class="flex-1 py-2.5 text-center text-xs sm:text-sm transition flex items-center justify-center space-x-1.5">
                <svg class="w-4 h-4 text-gold-rich" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                <span>লগইন করুন</span>
            </button>
            <button type="button" @click="tab = 'register'" 
                    :class="{'border-b-2 border-emerald-deep text-emerald-night font-extrabold': tab === 'register', 'text-slate-500 font-medium': tab !== 'register'}"
                    class="flex-1 py-2.5 text-center text-xs sm:text-sm transition flex items-center justify-center space-x-1.5">
                <svg class="w-4 h-4 text-gold-rich" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                <span>নতুন অ্যাকাউন্ট খুলুন</span>
            </button>
        </div>

        <!-- ================= TAB 1: LOGIN FORM ================= -->
        <div x-show="tab === 'login'" x-transition>
            <!-- Intelligent Unregistered Phone Prompt -->
            <div x-show="phoneStatus === 'unregistered'" class="mb-4 p-3 bg-amber-50 border border-amber-300 rounded-2xl text-xs text-amber-900 flex items-start space-x-2">
                <svg class="w-4 h-4 text-amber-600 mt-0.5 mr-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
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
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
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
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </span>
                        <input type="password" id="login-password" name="password" required 
                            placeholder="আপনার গোপন পাসওয়ার্ড" 
                            class="w-full pl-10 pr-4 py-2.5 bg-[#fdfcf8] border border-[#d8cfbe] rounded-xl focus:ring-2 focus:ring-emerald-vibrant focus:border-transparent text-slate-800 text-xs">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" 
                        class="w-full bg-emerald-night hover:bg-emerald-deep text-white py-3 rounded-xl font-bold transition shadow-md flex items-center justify-center text-xs sm:text-sm">
                        <svg class="w-4 h-4 mr-2 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <span>লগইন করুন</span>
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
            <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-2xl p-3 text-xs text-emerald-900 mb-4 flex items-center">
                <svg class="w-4 h-4 text-emerald-600 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                </svg>
                <span><strong>শিক্ষার্থী ও শুভাকাঙ্ক্ষী অ্যাকাউন্ট:</strong> সহজেই নিবন্ধন করে পছন্দের কুরআন কোর্সে ভর্তি ও বই অর্ডার করুন।</span>
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
                        <svg class="w-4 h-4 mr-2 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        <span>অ্যাকাউন্ট তৈরি করুন</span>
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
