<?php
/**
 * Admin 1-Click SVG ID Card Generator & Print View
 * Template: Royal Islamic Emerald & Gold Vector Accreditation Card
 */
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';
$type = $type ?? 'teacher';
$member = $member ?? [];
$uuid = $member['kyc_uuid'] ?? bin2hex(random_bytes(16));
$nameBn = $member['name'] ?? 'নাম দেওয়া হয়নি';
$nameEn = $member['name_en'] ?? '';
$district = $member['district_name'] ?? ($member['area_name'] ?? 'বাংলাদেশ');
$phone = $member['phone'] ?? '';
$roleTitle = ($type === 'director') ? 'অফিসিয়াল জেলা পরিচালক' : 'অনুমোদিত মুয়াল্লিম (শিক্ষক)';
$photoUrl = !empty($member['photo']) ? $member['photo'] : null;
$certSerial = 'KQ-CERT-2026-' . strtoupper(substr(hash('sha256', $uuid), 0, 8));
$verifyUrl = "https://project.rasel.cloud/kariana/verify/{$uuid}";
$qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=" . urlencode($verifyUrl);
?>

<div class="container mx-auto px-4 py-8 font-bengali">
    <!-- Top Action Bar (Hidden on Print) -->
    <div class="print:hidden mb-8 bg-gradient-to-r from-emerald-950 via-emerald-900 to-teal-950 rounded-2xl p-5 text-white shadow-xl flex flex-col sm:flex-row items-center justify-between gap-4 border border-amber-400/40">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-0.5 rounded-full bg-amber-400/20 text-amber-300 text-xs font-bold uppercase mb-1 border border-amber-400/30">
                <span>🪪</span>
                অফিসিয়াল আইডি কার্ড জেনারেটর
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-amber-200">
                <?= htmlspecialchars($nameBn) ?> — ডিজিটাল পরিচয়পত্র
            </h1>
            <p class="text-xs text-emerald-200">
                পদবি: <span class="text-amber-300 font-bold"><?= $roleTitle ?></span> | ক্রমিক নং: <span class="font-mono text-amber-300"><?= $certSerial ?></span>
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-500 hover:to-amber-600 text-emerald-950 font-bold text-sm shadow-lg flex items-center gap-2 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>কার্ড প্রিন্ট করুন</span>
            </button>
            <a href="<?= $baseUrl ?>/admin" class="px-4 py-2.5 rounded-xl bg-emerald-900/80 hover:bg-emerald-800 text-emerald-200 text-xs font-bold border border-emerald-700 transition">
                ড্যাশবোর্ডে ফিরুন
            </a>
        </div>
    </div>

    <!-- DUAL-SIDED ID CARD CANVAS (Standard CR80: 85.6mm x 54mm) -->
    <div class="flex flex-col lg:flex-row items-center justify-center gap-8 print:gap-4 print:flex-row print:m-0 print:p-0">
        <!-- 1. CARD FRONT FACE -->
        <div class="w-[380px] h-[240px] sm:w-[420px] sm:h-[265px] bg-gradient-to-br from-emerald-900 via-emerald-950 to-slate-950 rounded-2xl shadow-2xl p-5 text-white relative overflow-hidden border-2 border-amber-400/60 flex flex-col justify-between print:shadow-none print:border-emerald-900">
            <!-- Decorative Guilloche Background Accents -->
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
                    অফিসিয়াল আইডি
                </span>
            </div>

            <!-- Card Body -->
            <div class="flex items-center gap-4 py-2 relative z-10">
                <!-- Member Photo -->
                <div class="w-20 h-24 sm:w-22 sm:h-26 rounded-xl bg-slate-900 border-2 border-amber-400/80 overflow-hidden shrink-0 shadow-md flex items-center justify-center relative">
                    <?php if (!empty($photoUrl)): ?>
                        <img src="<?= $baseUrl ?>/<?= htmlspecialchars($photoUrl) ?>" alt="Photo" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="hidden w-full h-full items-center justify-center text-3xl text-amber-300">👤</div>
                    <?php else: ?>
                        <div class="text-3xl text-amber-300">👤</div>
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
                        <span>জেলা/এলাকা:</span>
                        <span class="text-white font-medium"><?= htmlspecialchars($district) ?></span>
                    </div>
                    <div class="text-[10px] text-slate-400 font-mono">
                        মোবাইল: <span class="text-amber-200 font-bold"><?= htmlspecialchars($phone) ?></span>
                    </div>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="flex items-center justify-between border-t border-emerald-800/60 pt-1.5 text-[9px] text-emerald-300 relative z-10">
                <span>মেয়াদ: আজীবন (ভেরিফাইড)</span>
                <span class="font-mono text-amber-300"><?= htmlspecialchars($certSerial) ?></span>
            </div>
        </div>

        <!-- 2. CARD BACK FACE -->
        <div class="w-[380px] h-[240px] sm:w-[420px] sm:h-[265px] bg-gradient-to-br from-slate-950 via-emerald-950 to-emerald-900 rounded-2xl shadow-2xl p-5 text-white relative overflow-hidden border-2 border-amber-400/60 flex flex-col justify-between print:shadow-none print:border-emerald-900">
            <!-- Guilloche Accent -->
            <div class="absolute -left-12 -top-12 w-48 h-48 rounded-full border border-amber-400/20 opacity-30 pointer-events-none"></div>

            <!-- Top Header Disclaimer -->
            <div class="border-b border-amber-400/30 pb-1.5 text-center relative z-10">
                <div class="text-[10px] font-bold text-amber-300">অফিসিয়াল ডিজিটাল প্রমাণপত্র ও যাচাই কিউআর</div>
                <div class="text-[8px] text-slate-300">যেকোনো স্মার্টফোন দিয়ে কিউআর কোড স্ক্যান করে সত্যতা নিশ্চিত করুন</div>
            </div>

            <!-- Center Content: Large QR Code + Official Seal -->
            <div class="flex items-center justify-around py-2 relative z-10">
                <!-- QR Code Box -->
                <div class="p-2 bg-white rounded-xl shadow-lg border border-amber-400/50">
                    <img src="<?= $qrApiUrl ?>" alt="Verification QR Code" class="w-24 h-24 sm:w-28 sm:h-28 object-contain">
                </div>

                <!-- Terms & Central Signature -->
                <div class="text-left text-[9px] text-slate-300 max-w-[170px] space-y-2">
                    <p class="leading-relaxed">
                        এই কার্ডধারী কারিয়ানা কুরআন কেন্দ্রীয় একাডেমির নিবন্ধিত ও অনুমোদিত প্রতিনিধি।
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
</div>

<style>
@media print {
    body {
        background: white !important;
        color: black !important;
    }
    header, footer, nav, .site-header, .site-footer, .print\\:hidden {
        display: none !important;
    }
}
</style>
