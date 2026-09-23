<?php
/**
 * Public Verification Credential & Dual-Sided ID Card View
 * URL: /verify/{uuid}
 */
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';
$kyc = $kyc ?? [];
$roleTitle = $roleTitle ?? 'সম্মানিত সদস্য';
$nameBn = $nameBn ?? 'সম্মানিত সদস্য';
$nameEn = $nameEn ?? '';
$district = $district ?? 'বাংলাদেশ';
$photoUrl = $photoUrl ?? null;
$phone = $phone ?? '';
$certSerial = $certSerial ?? 'KQ-CERT-2026';
$verifyUrl = $verifyUrl ?? "https://project.rasel.cloud/kariana/verify/{$uuid}";
$qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=" . urlencode($verifyUrl);
$isLevel2 = ((int)($kyc['kyc_level'] ?? 0)) >= 2;
?>

<div class="py-8 px-4 max-w-5xl mx-auto print:p-0 print:m-0 font-bengali">
    <!-- Verification Status Banner (Hidden on Print) -->
    <div class="print:hidden mb-8 bg-gradient-to-r from-emerald-900 via-emerald-800 to-teal-900 rounded-3xl p-6 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-4 border border-amber-400/30">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-amber-400/20 border border-amber-400/50 flex items-center justify-center text-3xl shrink-0">
                <?= $isLevel2 ? '🛡️' : '📱' ?>
            </div>
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-700/80 text-amber-300 text-xs font-semibold mb-1 border border-emerald-500/50">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                    <?= $isLevel2 ? 'সরকারি এনআইডি ভেরিফাইড ক্রেডেনশিয়াল' : 'মোবাইল ওটিপি ভেরিফাইড' ?>
                </div>
                <h1 class="text-xl md:text-2xl font-extrabold text-white">
                    কারিয়ানা কুরআন — অফিসিয়াল অ্যাক্রিডিটেশন
                </h1>
                <p class="text-xs md:text-sm text-emerald-200">
                    সনদ ক্রমিক: <span class="font-mono text-amber-300 font-bold"><?= htmlspecialchars($certSerial) ?></span> | ভেরিফিকেশন তারিখ: <?= date('d M, Y', strtotime($kyc['verified_at'] ?? $kyc['created_at'])) ?>
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-emerald-950 font-bold text-sm shadow-lg flex items-center gap-2 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>কার্ড প্রিন্ট করুন</span>
            </button>
            <a href="<?= $baseUrl ?>/verify/kyc" class="px-4 py-2.5 rounded-xl bg-emerald-950/60 hover:bg-emerald-950 text-emerald-200 text-xs font-semibold border border-emerald-700/50 transition-all">
                নতুন যাচাই
            </a>
        </div>
    </div>

    <!-- DUAL-SIDED ID CARD SECTION -->
    <div class="text-center mb-6 print:hidden">
        <h2 class="text-xl font-bold text-emerald-950">ডিজিটাল স্মার্ট আইডি কার্ড (সম্মুখ ও পশ্চাৎ ভাগ)</h2>
        <p class="text-xs text-slate-500">প্রমিত আইডি কার্ড সাইজ (৮৫.৬ মিমি × ৫৪ মিমি)</p>
    </div>

    <div class="flex flex-col lg:flex-row items-center justify-center gap-8 print:gap-4 print:flex-row">
        <!-- 1. CARD FRONT SIDE -->
        <div class="w-[360px] h-[225px] sm:w-[400px] sm:h-[250px] bg-gradient-to-br from-emerald-900 via-emerald-950 to-slate-950 rounded-2xl shadow-2xl p-5 text-white relative overflow-hidden border-2 border-amber-400/50 flex flex-col justify-between print:shadow-none print:border-emerald-900">
            <!-- Decorative Guilloche Background Pattern -->
            <div class="absolute -right-12 -bottom-12 w-48 h-48 rounded-full border border-amber-400/20 opacity-30 pointer-events-none"></div>
            <div class="absolute -right-6 -bottom-6 w-36 h-36 rounded-full border border-amber-400/20 opacity-30 pointer-events-none"></div>
            <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 opacity-5 text-9xl pointer-events-none">📖</div>

            <!-- Card Header -->
            <div class="flex items-center justify-between border-b border-amber-400/30 pb-2 relative z-10">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-400/20 border border-amber-400/60 flex items-center justify-center text-amber-300 font-extrabold text-sm">
                        ক্ব
                    </div>
                    <div>
                        <div class="text-xs font-extrabold text-amber-300 tracking-wider">কারিয়ানা কুরআন একাডেমি</div>
                        <div class="text-[9px] text-emerald-200">Kariana Quran Islamic Foundation</div>
                    </div>
                </div>
                <span class="text-[9px] font-bold bg-amber-400/20 text-amber-300 px-2 py-0.5 rounded border border-amber-400/40 uppercase">
                    <?= $isLevel2 ? 'সরকারি NID ভেরিফাইড' : 'ভেরিফাইড' ?>
                </span>
            </div>

            <!-- Card Body -->
            <div class="flex items-center gap-4 py-2 relative z-10">
                <!-- Photo Avatar -->
                <div class="w-20 h-24 sm:w-22 sm:h-26 rounded-xl bg-slate-800 border-2 border-amber-400/80 overflow-hidden shrink-0 shadow-md flex items-center justify-center">
                    <?php if (!empty($photoUrl)): ?>
                        <img src="<?= htmlspecialchars($photoUrl) ?>" alt="Member Photo" class="w-full h-full object-cover">
                    <?php else: ?>
                        <div class="text-3xl text-emerald-300">👤</div>
                    <?php endif; ?>
                </div>

                <!-- Info Details -->
                <div class="space-y-1 text-left min-w-0">
                    <div class="text-base sm:text-lg font-bold text-white truncate drop-shadow"><?= htmlspecialchars($nameBn) ?></div>
                    <?php if (!empty($nameEn)): ?>
                        <div class="text-[11px] font-medium text-emerald-200 font-sans tracking-wide uppercase truncate"><?= htmlspecialchars($nameEn) ?></div>
                    <?php endif; ?>
                    <div class="inline-block text-[11px] font-semibold text-amber-300 bg-emerald-800/80 px-2 py-0.5 rounded border border-emerald-600/50">
                        <?= htmlspecialchars($roleTitle) ?>
                    </div>
                    <div class="text-[11px] text-slate-300 flex items-center gap-1">
                        <span>জেলা:</span>
                        <span class="text-white font-medium"><?= htmlspecialchars($district) ?></span>
                    </div>
                    <div class="text-[10px] text-slate-400 font-mono">
                        আইডি: <span class="text-amber-200 font-bold"><?= substr($uuid, 0, 12) ?>...</span>
                    </div>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="flex items-center justify-between border-t border-emerald-800/60 pt-1.5 text-[9px] text-emerald-300 relative z-10">
                <span>মেয়াদ: আজীবন (ভেরিফাইড)</span>
                <span class="font-mono text-amber-300"><?= htmlspecialchars($certSerial) ?></span>
            </div>
        </div>

        <!-- 2. CARD BACK SIDE -->
        <div class="w-[360px] h-[225px] sm:w-[400px] sm:h-[250px] bg-gradient-to-br from-slate-950 via-emerald-950 to-emerald-900 rounded-2xl shadow-2xl p-5 text-white relative overflow-hidden border-2 border-amber-400/50 flex flex-col justify-between print:shadow-none print:border-emerald-900">
            <!-- Guilloche Accent -->
            <div class="absolute -left-12 -top-12 w-48 h-48 rounded-full border border-amber-400/20 opacity-30 pointer-events-none"></div>

            <!-- Top Header Disclaimer -->
            <div class="border-b border-amber-400/30 pb-1.5 text-center relative z-10">
                <div class="text-[10px] font-bold text-amber-300">অফিসিয়াল ডিজিটাল আইডেন্টিটি ও প্রমাণপত্র</div>
                <div class="text-[8px] text-slate-300">যেকোনো স্মার্টফোন ক্যামেরা দিয়ে কিউআর কোড স্ক্যান করে সত্যতা যাচাই করুন</div>
            </div>

            <!-- Center Content: Large QR Code + Official Seal -->
            <div class="flex items-center justify-around py-2 relative z-10">
                <!-- QR Code Box -->
                <div class="p-2 bg-white rounded-xl shadow-lg border border-amber-400/40">
                    <img src="<?= $qrApiUrl ?>" alt="Verification QR Code" class="w-24 h-24 sm:w-28 sm:h-28 object-contain">
                </div>

                <!-- Terms & Central Signature -->
                <div class="text-left text-[9px] text-slate-300 max-w-[170px] space-y-2">
                    <p class="leading-relaxed">
                        এই কার্ডধারী কারিয়ানা কুরআন কেন্দ্রীয় একাডেমির নিবন্ধিত ও সম্মানিত প্রতিনিধি।
                    </p>
                    <div class="pt-2 border-t border-emerald-800/60">
                        <div class="font-serif italic text-amber-300 text-[11px] font-bold">Maulana Saddam Hussain</div>
                        <div class="text-[8px] text-emerald-400 font-semibold">প্রতিষ্ঠাতা ও প্রধান মুয়াল্লিম</div>
                    </div>
                </div>
            </div>

            <!-- Bottom Helpline -->
            <div class="flex items-center justify-between border-t border-emerald-800/60 pt-1.5 text-[8px] text-slate-400 relative z-10">
                <span>হেল্পলাইন: 01827362508</span>
                <span class="font-sans text-amber-300">project.rasel.cloud/kariana</span>
            </div>
        </div>
    </div>

    <!-- Printable Official Verification Summary Table (Shows on Print & Web) -->
    <div class="mt-12 bg-white rounded-3xl p-6 md:p-8 shadow-lg border border-slate-200">
        <h3 class="text-lg font-bold text-emerald-950 mb-4 flex items-center gap-2">
            <span>📋</span>
            <span>অফিসিয়াল ভেরিফিকেশন ডাটাবেজ রেকর্ড</span>
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex justify-between">
                <span class="text-slate-500">পূর্ণ নাম (বাংলা):</span>
                <span class="font-bold text-slate-800"><?= htmlspecialchars($nameBn) ?></span>
            </div>
            <?php if (!empty($nameEn)): ?>
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex justify-between">
                <span class="text-slate-500">Name (English):</span>
                <span class="font-bold text-slate-800 font-sans"><?= htmlspecialchars($nameEn) ?></span>
            </div>
            <?php endif; ?>
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex justify-between">
                <span class="text-slate-500">পদবি / স্ট্যাটাস:</span>
                <span class="font-bold text-emerald-800"><?= htmlspecialchars($roleTitle) ?></span>
            </div>
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex justify-between">
                <span class="text-slate-500">এলাকা / জেলা:</span>
                <span class="font-bold text-slate-800"><?= htmlspecialchars($district) ?></span>
            </div>
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex justify-between">
                <span class="text-slate-500">মোবাইল নম্বর:</span>
                <span class="font-bold text-slate-800 font-mono"><?= htmlspecialchars(substr($phone, 0, 5) . '****' . substr($phone, -2)) ?> (যাচাইকৃত)</span>
            </div>
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex justify-between">
                <span class="text-slate-500">কেওয়াইসি লেভেল:</span>
                <span class="font-bold <?= $isLevel2 ? 'text-amber-600' : 'text-emerald-700' ?>">
                    <?= $isLevel2 ? 'লেভেল ২ (পূর্ণাঙ্গ সরকারি এনআইডি এপিআই ম্যাচ)' : 'লেভেল ১ (মোবাইল ওটিপি)' ?>
                </span>
            </div>
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex justify-between col-span-1 md:col-span-2">
                <span class="text-slate-500">ভেরিফিকেশন পাবলিক লিংক:</span>
                <a href="<?= $verifyUrl ?>" target="_blank" class="font-mono text-emerald-700 underline text-xs font-semibold"><?= $verifyUrl ?></a>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body {
        background: white !important;
        color: black !important;
    }
    header, footer, nav, .site-header, .site-footer {
        display: none !important;
    }
}
</style>
