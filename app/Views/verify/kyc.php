<?php
/**
 * 2-Step Centralized KYC Verification Portal
 * Wizard with Radar Scanning Animation, Level 1 SMS OTP, and Level 2 Government NID API.
 * Styled with Royal Islamic Emerald & Gold Luxury Aesthetic
 */
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';
$userData = $userData ?? null;
$token = $token ?? '';
$activeUuid = $activeUuid ?? '';
$kycLevel = (int)($kycLevel ?? 0);
?>

<section class="max-w-3xl mx-auto px-4 py-8 md:py-12" x-data="kycPortalHandler()">
    <!-- Header Hero -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-800/80 text-amber-300 text-xs font-semibold uppercase tracking-wider mb-3 border border-amber-400/30 shadow">
            <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
            অফিসিয়াল আইডেন্টিটি পোর্টাল
        </div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-amber-200 font-bengali drop-shadow">
            কারিয়ানা কুরআন — কেন্দ্রীয় ২-ধাপ ভেরিফিকেশন (KYC)
        </h1>
        <p class="text-emerald-200 text-sm md:text-base mt-2 max-w-xl mx-auto">
            নিরাপদ ডাটা এনক্রিপশন (AES-256) এবং বাংলাদেশ সরকার অনুমোদিত সার্ভার এপিআই এর মাধ্যমে তাৎক্ষণিক পরিচিতি নিশ্চিত করুন।
        </p>
    </div>

    <!-- Stepper Tracker -->
    <div class="mb-8">
        <div class="flex items-center justify-between max-w-lg mx-auto relative">
            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-emerald-900/80 z-0"></div>
            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-amber-400 transition-all duration-500 z-0"
                 :style="'width: ' + (step === 1 ? '0%' : (step === 2 ? '50%' : '100%'))"></div>

            <!-- Step 1 Indicator -->
            <div class="relative z-10 flex flex-col items-center">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-md transition-all duration-300"
                     :class="step >= 1 ? (kycLevel >= 1 ? 'bg-emerald-600 text-white ring-2 ring-amber-400' : 'bg-amber-400 text-emerald-950 font-black ring-4 ring-amber-400/30') : 'bg-emerald-950 text-slate-400 border border-emerald-800'">
                    <template x-if="kycLevel >= 1">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </template>
                    <template x-if="kycLevel < 1"><span>১</span></template>
                </div>
                <span class="text-xs font-semibold mt-2" :class="step === 1 ? 'text-amber-300' : 'text-emerald-300'">মোবাইল ওটিপি</span>
            </div>

            <!-- Step 2 Indicator -->
            <div class="relative z-10 flex flex-col items-center">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-md transition-all duration-300"
                     :class="kycLevel >= 2 ? 'bg-emerald-600 text-white ring-2 ring-amber-400' : (step === 2 ? 'bg-amber-400 text-emerald-950 font-black ring-4 ring-amber-400/30' : 'bg-emerald-950 text-slate-400 border border-emerald-800')">
                    <template x-if="kycLevel >= 2">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </template>
                    <template x-if="kycLevel < 2"><span>২</span></template>
                </div>
                <span class="text-xs font-semibold mt-2" :class="step === 2 ? 'text-amber-300' : 'text-emerald-300'">সরকারি এনআইডি</span>
            </div>

            <!-- Step 3 Complete Indicator -->
            <div class="relative z-10 flex flex-col items-center">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-md transition-all duration-300"
                     :class="kycLevel >= 2 ? 'bg-gradient-to-r from-amber-400 to-amber-500 text-emerald-950 ring-4 ring-amber-400/30' : 'bg-emerald-950 text-slate-400 border border-emerald-800'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <span class="text-xs font-semibold mt-2" :class="kycLevel >= 2 ? 'text-amber-300' : 'text-emerald-300'">ডিজিটাল সনদ</span>
            </div>
        </div>
    </div>

    <!-- Main Card Container -->
    <div class="bg-[#0a3324] rounded-3xl shadow-2xl border border-amber-400/30 overflow-hidden relative">
        <!-- Golden Top Accent Line -->
        <div class="h-1.5 bg-gradient-to-r from-emerald-600 via-amber-400 to-emerald-800"></div>

        <div class="p-6 md:p-8">
            <!-- Alert Messages -->
            <div x-show="message" x-cloak
                 :class="isError ? 'bg-rose-950/80 border-rose-500 text-rose-200' : 'bg-emerald-900/80 border-emerald-500 text-emerald-100'"
                 class="mb-6 p-4 rounded-xl border text-sm flex items-start gap-3 transition-all">
                <span class="text-lg" x-text="isError ? '⚠️' : '✅'"></span>
                <span class="font-medium" x-text="message"></span>
            </div>

            <!-- STEP 1: MOBILE OTP VERIFICATION -->
            <div x-show="step === 1" x-cloak>
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-emerald-800/60">
                    <div class="w-10 h-10 rounded-xl bg-amber-400/20 border border-amber-400/40 text-amber-300 flex items-center justify-center font-bold text-lg">
                        📱
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-amber-100">ধাপ ১: মোবাইল নম্বর ও ওটিপি যাচাই</h2>
                        <p class="text-xs text-emerald-300">আপনার মোবাইল নম্বরে ৬ ডিজিটের নিরাপত্তা কোড পাঠানো হবে</p>
                    </div>
                </div>

                <div class="space-y-4 max-w-md mx-auto">
                    <div>
                        <label class="block text-xs font-bold text-amber-200 uppercase mb-1">প্রার্থী / সম্মানিত সদস্যের নাম</label>
                        <input type="text" x-model="formData.name" placeholder="আপনার নাম লিখুন"
                               class="w-full px-4 py-3 rounded-xl border border-emerald-700/60 bg-emerald-950/80 text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-amber-200 uppercase mb-1">মোবাইল নম্বর (১১ ডিজিট)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-amber-400 text-sm font-semibold">+88</span>
                            <input type="tel" x-model="formData.phone" placeholder="01XXXXXXXXX" maxlength="11"
                                   :disabled="otpSent"
                                   class="w-full pl-14 pr-4 py-3 rounded-xl border border-emerald-700/60 bg-emerald-950/80 text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 font-medium tracking-wide">
                        </div>
                    </div>

                    <!-- OTP Code Input (Visible after OTP is sent) -->
                    <div x-show="otpSent" x-cloak class="pt-2">
                        <label class="block text-xs font-bold text-amber-300 uppercase mb-1 flex justify-between">
                            <span>৬-ডিজিটের এসএমএস ওটিপি কোড</span>
                            <span class="text-emerald-300 font-normal normal-case">মেয়াদ: ৫ মিনিট</span>
                        </label>
                        <input type="text" x-model="formData.otp_code" placeholder="123456" maxlength="6"
                               class="w-full px-4 py-3 text-center tracking-widest text-2xl font-mono font-bold rounded-xl border-2 border-amber-400 bg-emerald-950 text-amber-300 focus:outline-none focus:ring-2 focus:ring-amber-400">
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-4 flex flex-col sm:flex-row gap-3">
                        <template x-if="!otpSent">
                            <button type="button" @click="handleSendOtp()" :disabled="isLoading"
                                    class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600 hover:from-amber-500 hover:to-amber-700 text-emerald-950 font-bold text-sm shadow-lg transition-all flex items-center justify-center gap-2">
                                <span x-show="isLoading" class="animate-spin inline-block w-4 h-4 border-2 border-emerald-950 border-t-transparent rounded-full"></span>
                                <span>ওটিপি কোড পাঠান 📲</span>
                            </button>
                        </template>

                        <template x-if="otpSent">
                            <div class="w-full space-y-2">
                                <button type="button" @click="handleVerifyOtp()" :disabled="isLoading"
                                        class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600 hover:from-amber-500 hover:to-amber-700 text-emerald-950 font-bold text-sm shadow-lg transition-all flex items-center justify-center gap-2">
                                    <span x-show="isLoading" class="animate-spin inline-block w-4 h-4 border-2 border-emerald-950 border-t-transparent rounded-full"></span>
                                    <span>ওটিপি যাচাই করুন ও পরবর্তী ধাপে যান ➡️</span>
                                </button>
                                <button type="button" @click="handleSendOtp()" :disabled="isLoading"
                                        class="w-full text-center text-xs text-amber-300 hover:text-white underline py-1">
                                    পুনরায় কোড পাঠান
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- STEP 2: GOVERNMENT NID API VERIFICATION -->
            <div x-show="step === 2" x-cloak>
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-emerald-800/60">
                    <div class="w-10 h-10 rounded-xl bg-amber-400/20 border border-amber-400/40 text-amber-300 flex items-center justify-center font-bold text-lg">
                        🛡️
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-amber-100">ধাপ ২: সরকারি জাতীয় পরিচয়পত্র (NID) ভেরিফিকেশন</h2>
                        <p class="text-xs text-emerald-300">নির্বাচন কমিশন / ই-কেওয়াইসি ডাটাবেজের সাথে স্বয়ংক্রিয় ম্যাচিং</p>
                    </div>
                </div>

                <!-- Radar Scanning Animation during API call -->
                <div x-show="isScanning" class="py-12 text-center" x-cloak>
                    <div class="relative w-28 h-28 mx-auto mb-4">
                        <div class="absolute inset-0 rounded-full border-4 border-amber-400/30 animate-ping"></div>
                        <div class="absolute inset-2 rounded-full border-4 border-emerald-400/40 animate-pulse"></div>
                        <div class="absolute inset-0 rounded-full border-2 border-amber-400 flex items-center justify-center bg-emerald-950 text-white shadow-2xl">
                            <svg class="w-10 h-10 text-amber-400 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-base font-bold text-amber-200">সরকারি ডাটাবেজ অনুসন্ধান চলছে...</h3>
                    <p class="text-xs text-emerald-300 mt-1">দয়া করে অপেক্ষা করুন, পরিচয় যাচাই করা হচ্ছে</p>
                </div>

                <div x-show="!isScanning" class="space-y-4 max-w-md mx-auto">
                    <div class="p-3 bg-emerald-900/60 rounded-xl border border-emerald-600/50 text-xs text-emerald-200 flex items-center gap-2">
                        <span class="text-amber-300 font-bold">✅</span>
                        <span>মোবাইল নম্বর <strong><span class="text-amber-200" x-text="formData.phone"></span></strong> সফলভাবে যাচাইকৃত।</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-amber-200 uppercase mb-1">জাতীয় পরিচয়পত্র নম্বর (১০ বা ১৭ ডিজিট)</label>
                        <input type="text" x-model="formData.nid_number" placeholder="যেমন: 19901234567890123" maxlength="17"
                               class="w-full px-4 py-3 rounded-xl border border-emerald-700/60 bg-emerald-950/80 text-white placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 font-mono font-medium">
                        <span class="text-[11px] text-emerald-400">১০ ডিজিটের স্মার্ট কার্ড বা ১৭ ডিজিটের এনআইডি দিন</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-amber-200 uppercase mb-1">জন্ম তারিখ (এনআইডি অনুযায়ী)</label>
                        <input type="date" x-model="formData.date_of_birth"
                               class="w-full px-4 py-3 rounded-xl border border-emerald-700/60 bg-emerald-950/80 text-white text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 font-medium">
                    </div>

                    <div class="pt-4">
                        <button type="button" @click="handleVerifyNid()" :disabled="isLoading"
                                class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600 hover:from-amber-500 hover:to-amber-700 text-emerald-950 font-bold text-sm shadow-lg transition-all flex items-center justify-center gap-2">
                            <span>সরকারি এপিআই দিয়ে যাচাই সম্পন্ন করুন 🔍</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- STEP 3: COMPLETED SUCCESS VIEW -->
            <div x-show="step === 3" x-cloak class="text-center py-6">
                <div class="w-20 h-20 mx-auto rounded-full bg-amber-400/20 border-4 border-amber-400 flex items-center justify-center text-3xl shadow-lg mb-4">
                    🎉
                </div>
                <div class="inline-block px-3 py-1 rounded-full bg-emerald-800 text-amber-300 text-xs font-bold uppercase mb-2 border border-amber-400/40">
                    ✅ পূর্ণাঙ্গ ভেরিফিকেশন সম্পন্ন
                </div>
                <h2 class="text-2xl font-extrabold text-amber-100 font-bengali">
                    মুবারকবাদ! আপনার পরিচিতি ভেরিফাইড
                </h2>
                <p class="text-emerald-200 text-sm mt-2 max-w-md mx-auto">
                    আপনার জাতীয় পরিচয়পত্র ও পরিচিতি সফলভাবে কারিয়ানা কুরআন কেন্দ্রীয় ডাটাবেজে নথিবদ্ধ হয়েছে।
                </p>

                <!-- Verified Summary Card -->
                <div class="mt-6 p-6 rounded-2xl bg-gradient-to-br from-emerald-900 via-emerald-950 to-slate-950 text-white max-w-sm mx-auto shadow-2xl border border-amber-400/40 text-left relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 opacity-10 text-8xl">🕌</div>
                    <div class="flex items-center justify-between border-b border-emerald-700/50 pb-3 mb-3">
                        <span class="text-xs text-amber-300 font-semibold tracking-wider uppercase">অফিসিয়াল পরিচিতি ব্যাজ</span>
                        <span class="text-xs bg-amber-400/20 text-amber-300 px-2 py-0.5 rounded border border-amber-400/30">লেভেল ২ ভেরিফাইড</span>
                    </div>
                    <div class="space-y-1.5 text-sm">
                        <div class="text-lg font-bold text-amber-100 font-bengali" x-text="verifiedData.name || formData.name"></div>
                        <div class="text-xs text-emerald-300 flex items-center gap-1">
                            <span>জেলা:</span>
                            <span class="text-white font-medium" x-text="verifiedData.district || 'কেন্দ্রীয়'"></span>
                        </div>
                        <div class="text-xs text-emerald-300 flex items-center gap-1 font-mono">
                            <span>ID:</span>
                            <span class="text-amber-200 font-bold" x-text="uuid"></span>
                        </div>
                    </div>
                </div>

                <!-- Action Button: View/Download Dual-Sided ID Card -->
                <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a :href="'<?= $baseUrl ?>/verify/' + uuid"
                       class="w-full sm:w-auto px-6 py-3 rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-500 hover:to-amber-600 text-emerald-950 font-bold text-sm shadow-md transition-all flex items-center justify-center gap-2">
                        <span>🪪 ডিজিটাল আইডি কার্ড ও সনদ দেখুন</span>
                    </a>
                    <a href="<?= $baseUrl ?>/"
                       class="w-full sm:w-auto px-6 py-3 rounded-xl bg-emerald-900/60 hover:bg-emerald-900 text-emerald-200 font-semibold text-sm border border-emerald-700 transition-all">
                        প্রধান পাতায় ফিরুন
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function kycPortalHandler() {
    return {
        step: <?= $kycLevel >= 2 ? 3 : ($kycLevel === 1 ? 2 : 1) ?>,
        kycLevel: <?= (int)$kycLevel ?>,
        uuid: '<?= htmlspecialchars($activeUuid, ENT_QUOTES, 'UTF-8') ?>',
        token: '<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>',
        otpSent: false,
        isLoading: false,
        isScanning: false,
        message: '',
        isError: false,
        formData: {
            user_type: '<?= htmlspecialchars($userData['user_type'] ?? 'user', ENT_QUOTES, 'UTF-8') ?>',
            user_id: <?= (int)($userData['user_id'] ?? 0) ?>,
            name: '<?= htmlspecialchars($userData['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>',
            phone: '<?= htmlspecialchars($userData['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>',
            otp_code: '',
            nid_number: '',
            date_of_birth: ''
        },
        verifiedData: {
            name: '',
            district: ''
        },

        async handleSendOtp() {
            if (!this.formData.phone || this.formData.phone.length < 11) {
                this.showMsg('সঠিক ১১ ডিজিটের মোবাইল নম্বর দিন।', true);
                return;
            }
            this.isLoading = true;
            this.message = '';

            try {
                const fd = new FormData();
                fd.append('phone', this.formData.phone);
                fd.append('user_type', this.formData.user_type);
                fd.append('user_id', this.formData.user_id);
                fd.append('token', this.token);

                const res = await fetch('<?= $baseUrl ?>/verify/kyc/send-otp', {
                    method: 'POST',
                    body: fd
                });
                const data = await res.json();

                if (data.success) {
                    this.uuid = data.uuid;
                    this.otpSent = true;
                    this.showMsg(data.message || 'আপনার মোবাইলে ৬-ডিজিটের ওটিপি পাঠানো হয়েছে।', false);
                } else {
                    this.showMsg(data.message || 'ওটিপি পাঠাতে ব্যর্থ হয়েছে।', true);
                }
            } catch (e) {
                this.showMsg('সার্ভারে যোগাযোগ করা যায়নি। পুনরায় চেষ্টা করুন।', true);
            } finally {
                this.isLoading = false;
            }
        },

        async handleVerifyOtp() {
            if (!this.formData.otp_code || this.formData.otp_code.length !== 6) {
                this.showMsg('৬-ডিজিটের সঠিক ওটিপি কোড দিন।', true);
                return;
            }
            this.isLoading = true;
            this.message = '';

            try {
                const fd = new FormData();
                fd.append('uuid', this.uuid);
                fd.append('otp_code', this.formData.otp_code);

                const res = await fetch('<?= $baseUrl ?>/verify/kyc/verify-otp', {
                    method: 'POST',
                    body: fd
                });
                const data = await res.json();

                if (data.success) {
                    this.kycLevel = 1;
                    this.step = 2;
                    this.showMsg(data.message, false);
                } else {
                    this.showMsg(data.message || 'ওটিপি যাচাই ব্যর্থ হয়েছে।', true);
                }
            } catch (e) {
                this.showMsg('সার্ভার রেসপন্সে সমস্যা হয়েছে।', true);
            } finally {
                this.isLoading = false;
            }
        },

        async handleVerifyNid() {
            if (!this.formData.nid_number || this.formData.nid_number.length < 10) {
                this.showMsg('১০ বা ১৭ ডিজিটের এনআইডি নম্বর দিন।', true);
                return;
            }
            if (!this.formData.date_of_birth) {
                this.showMsg('সঠিক জন্ম তারিখ সিলেক্ট করুন।', true);
                return;
            }

            this.isScanning = true;
            this.isLoading = true;
            this.message = '';

            try {
                const fd = new FormData();
                fd.append('uuid', this.uuid);
                fd.append('nid_number', this.formData.nid_number);
                fd.append('date_of_birth', this.formData.date_of_birth);

                const res = await fetch('<?= $baseUrl ?>/verify/kyc/verify-nid', {
                    method: 'POST',
                    body: fd
                });
                const data = await res.json();

                if (data.success) {
                    this.kycLevel = 2;
                    this.step = 3;
                    this.verifiedData.name = data.verified_name || this.formData.name;
                    this.verifiedData.district = data.district || '';
                    this.showMsg(data.message, false);
                } else {
                    this.showMsg(data.message || 'এনআইডি যাচাই সম্পন্ন করা যায়নি।', true);
                }
            } catch (e) {
                this.showMsg('সরকারি সার্ভারে যোগাযোগ করা যায়নি।', true);
            } finally {
                this.isScanning = false;
                this.isLoading = false;
            }
        },

        showMsg(msg, isErr) {
            this.message = msg;
            this.isError = isErr;
        }
    };
}
</script>
