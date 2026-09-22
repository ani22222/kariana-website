<?php
/**
 * Universal Multi-Role Authentication & Smart Onboarding View
 * Single-Box Phone-First Progressive Flow - Kariana Quran
 * 100% SVG Vector Icons - Zero Emojis
 */
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';
$activeTab = $activeTab ?? 'login';
$prefillPhone = $prefillPhone ?? '';
$success = \Core\Session::getFlash('success');
$error = \Core\Session::getFlash('error');
$info = \Core\Session::getFlash('info');
$unregisteredPhone = \Core\Session::getFlash('unregistered_phone');
$initialPhone = $prefillPhone ?: ($unregisteredPhone ?: '');
?>

<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 bg-[#f8f5ee]">
    <div class="max-w-lg w-full bg-[#fffefb] border-2 border-[#e7dec6] rounded-3xl p-6 sm:p-10 shadow-xl relative overflow-hidden" 
         x-data="{ 
             step: '<?= (!empty($unregisteredPhone) || ($activeTab === 'register' && !empty($initialPhone))) ? 'register' : 'phone' ?>',
             demoMenu: false,
             phoneInput: '<?= htmlspecialchars($initialPhone) ?>',
             userGreeting: null,
             userRole: null,
             isChecking: false,
             validationError: '',
             fillDemo(user, pass) {
                 this.phoneInput = user;
                 this.step = 'login';
                 this.userGreeting = 'ডেমো রোল: ' + user;
                 $nextTick(() => {
                     const passInput = document.getElementById('login-password');
                     if (passInput) {
                         passInput.value = pass;
                         document.getElementById('universal-login-form').submit();
                     }
                 });
             },
             checkIdentifier() {
                 let val = this.phoneInput.trim();
                 this.validationError = '';
                 if (val.length < 3) {
                     this.validationError = 'অনুগ্রহ করে আপনার সঠিক মোবাইল নম্বর অথবা ইউজারনেম লিখুন।';
                     return;
                 }
                 this.isChecking = true;
                 fetch('<?= $baseUrl ?>/api/check-phone?phone=' + encodeURIComponent(val))
                     .then(r => r.json())
                     .then(d => {
                         this.isChecking = false;
                         if (d.exists) {
                             this.step = 'login';
                             this.userGreeting = d.greeting || (d.name ? 'স্বাগতম, ' + d.name + '!' : 'অ্যাকাউন্ট শনাক্ত হয়েছে');
                             this.userRole = d.role || null;
                             $nextTick(() => {
                                 const p = document.getElementById('login-password');
                                 if (p) p.focus();
                             });
                         } else {
                             this.step = 'register';
                             $nextTick(() => {
                                 const n = document.getElementById('reg-name');
                                 if (n) n.focus();
                             });
                         }
                     })
                     .catch(() => {
                         this.isChecking = false;
                         this.step = 'login';
                     });
             },
             resetToPhone() {
                 this.step = 'phone';
                 this.validationError = '';
                 $nextTick(() => {
                     const el = document.getElementById('login-identifier');
                     if (el) el.focus();
                 });
             }
         }"
         x-init="if (phoneInput && phoneInput.length >= 11 && step === 'phone') { checkIdentifier(); }">

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
            <p class="text-xs text-slate-500 mt-1">কুরআন প্রচার, শিক্ষা ও প্রাতিষ্ঠানিক সমন্বয় প্ল্যাটফর্ম — একক প্রবেশদ্বার</p>
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

        <!-- ============================================================
             STEP 1: SMART SINGLE MOBILE NUMBER / IDENTIFIER INPUT BOX
             ============================================================ -->
        <div x-show="step === 'phone'" x-transition class="space-y-4">
            <div>
                <label class="block font-bold text-slate-800 mb-1 text-xs sm:text-sm">
                    আপনার মোবাইল নম্বর লিখুন
                </label>
                <p class="text-[11px] text-slate-500 mb-2">
                    মোবাইল নম্বর লিখলে আপনার অ্যাকাউন্ট ও রোল স্বয়ংক্রিয়ভাবে শনাক্ত হবে
                </p>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </span>
                    <input type="text" 
                           id="login-identifier" 
                           name="identifier" 
                           x-model="phoneInput" 
                           @keydown.enter.prevent="checkIdentifier()" 
                           placeholder="যেমন: 017xxxxxxxx বা ইউজারনেম" 
                           class="w-full pl-10 pr-4 py-3 bg-[#fdfcf8] border-2 border-[#d8cfbe] rounded-xl focus:ring-2 focus:ring-emerald-vibrant focus:border-emerald-600 text-slate-800 text-sm font-medium transition shadow-sm">
                </div>
                <p x-show="validationError" x-text="validationError" class="text-rose-600 text-[11px] mt-1.5 font-bold"></p>
            </div>

            <button type="button" 
                    @click="checkIdentifier()" 
                    :disabled="isChecking"
                    class="w-full bg-emerald-night hover:bg-emerald-deep text-white py-3.5 rounded-xl font-bold transition shadow-md flex items-center justify-center text-xs sm:text-sm">
                <template x-if="!isChecking">
                    <span class="flex items-center">
                        <span>পরবর্তী</span>
                        <svg class="w-4 h-4 ml-1.5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </span>
                </template>
                <template x-if="isChecking">
                    <span class="flex items-center">
                        <svg class="animate-spin w-4 h-4 mr-2 text-amber-300" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        যাচাই করা হচ্ছে...
                    </span>
                </template>
            </button>
        </div>

        <!-- ============================================================
             STEP 2A: EXISTING REGISTERED USER -> PASSWORD INPUT LOGIN
             ============================================================ -->
        <div x-show="step === 'login'" x-transition class="space-y-4">
            <!-- Account Identified Friendly Banner -->
            <div class="bg-emerald-50 border border-emerald-300 rounded-2xl p-3.5 flex items-center justify-between text-xs">
                <div class="flex items-center space-x-2.5">
                    <span class="w-8 h-8 rounded-full bg-emerald-deep text-amber-300 flex items-center justify-center shrink-0 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                    <div>
                        <p class="font-bold text-emerald-950 text-xs sm:text-sm" x-text="userGreeting || 'অ্যাকাউন্ট শনাক্ত হয়েছে'"></p>
                        <p class="text-[11px] text-slate-500 font-mono" x-text="phoneInput"></p>
                    </div>
                </div>
                <button type="button" @click="resetToPhone()" class="text-[11px] text-emerald-800 hover:text-emerald-950 font-bold underline bg-emerald-100/60 px-2.5 py-1 rounded-lg transition">
                    নম্বর পরিবর্তন
                </button>
            </div>

            <form id="universal-login-form" action="<?= $baseUrl ?>/login" method="POST" class="space-y-4 text-xs">
                <?= \Core\Csrf::field() ?>
                <input type="hidden" name="identifier" :value="phoneInput">
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect ?? '') ?>">

                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block font-bold text-slate-700">আপনার গোপন পাসওয়ার্ড প্রদান করুন</label>
                        <a href="https://wa.me/8801711756391?text=পাসওয়ার্ড%20রিসেট%20সহায়তা" target="_blank" class="text-[11px] text-emerald-deep hover:underline">পাসওয়ার্ড ভুলে গেছেন?</a>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </span>
                        <input type="password" id="login-password" name="password" required autofocus
                            placeholder="আপনার গোপন পাসওয়ার্ড লিখুন" 
                            class="w-full pl-10 pr-4 py-3 bg-[#fdfcf8] border border-[#d8cfbe] rounded-xl focus:ring-2 focus:ring-emerald-vibrant focus:border-emerald-600 text-slate-800 text-xs font-medium">
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
        </div>

        <!-- ============================================================
             STEP 2B: UNREGISTERED NEW USER -> PROGRESSIVE ONBOARDING
             ============================================================ -->
        <div x-show="step === 'register'" x-transition class="space-y-4">
            <!-- Polite New User Notification Alert -->
            <div class="bg-amber-50 border border-amber-300 rounded-2xl p-3.5 text-xs text-amber-900">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-2">
                        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="font-bold text-amber-950 text-xs sm:text-sm">এই নম্বরে কোনো পূর্ববর্তী অ্যাকাউন্ট পাওয়া যায়নি</p>
                            <p class="text-[11px] text-amber-800 mt-0.5">
                                আপনার নাম ও তথ্য প্রদান করে এক মিনিটেই নতুন অ্যাকাউন্ট খুলে প্রবেশ করুন।
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="resetToPhone()" class="text-[11px] text-amber-900 hover:text-black font-bold underline bg-amber-200/60 px-2 py-1 rounded-lg shrink-0 ml-2">
                        নম্বর পরিবর্তন
                    </button>
                </div>
            </div>

            <!-- Self-Registration Form -->
            <form action="<?= $baseUrl ?>/register" method="POST" class="space-y-3.5 text-xs">
                <?= \Core\Csrf::field() ?>
                <input type="hidden" name="phone" :value="phoneInput">

                <div>
                    <label class="block font-bold text-slate-700 mb-1">আপনার পূর্ণ নাম *</label>
                    <input type="text" id="reg-name" name="name" required placeholder="যেমন: মোহাম্মদ আব্দুল্লাহ" 
                        class="w-full px-3.5 py-2.5 bg-[#fdfcf8] border border-[#d8cfbe] rounded-xl focus:ring-2 focus:ring-emerald-vibrant text-slate-800 text-xs">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">জেলা *</label>
                        <input type="text" name="district" required placeholder="যেমন: ঢাকা, মাগুরা, চট্টগ্রাম..." 
                            class="w-full px-3.5 py-2.5 bg-[#fdfcf8] border border-[#d8cfbe] rounded-xl focus:ring-2 focus:ring-emerald-vibrant text-slate-800 text-xs">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">পাসওয়ার্ড (কমপক্ষে ৬ অক্ষর) *</label>
                        <input type="password" name="password" required minlength="6" placeholder="গোপন পাসওয়ার্ড লিখুন" 
                            class="w-full px-3.5 py-2.5 bg-[#fdfcf8] border border-[#d8cfbe] rounded-xl focus:ring-2 focus:ring-emerald-vibrant text-slate-800 text-xs">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" 
                        class="w-full bg-emerald-deep hover:bg-emerald-night text-white py-3 rounded-xl font-bold transition shadow-md flex items-center justify-center text-xs sm:text-sm">
                        <svg class="w-4 h-4 mr-2 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        <span>নতুন অ্যাকাউন্ট খুলুন ও প্রবেশ করুন</span>
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-6 text-center text-[11px] text-slate-400">
            কারিয়ানা কুরআন শিক্ষা সোসাইটি &bull; নিরাপদ ও সুরক্ষিত প্ল্যাটফর্ম
        </div>
    </div>
</div>
