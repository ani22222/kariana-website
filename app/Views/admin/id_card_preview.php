<?php
/**
 * Admin Official ID Card Generator & Print View
 * Template: Official Kariana Quran Accreditation Card (Based on Adobe Illustrator .ai)
 * Standard Dimensions: Portrait CR80 (54mm × 85.6mm, Aspect Ratio 1:1.6, 600px × 960px)
 */
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';
$type = $type ?? 'teacher';
$member = $member ?? [];
$frontSvg = $frontSvg ?? '';
$backSvg = $backSvg ?? '';
$uuid = $member['kyc_uuid'] ?? bin2hex(random_bytes(16));
$nameBn = $member['name'] ?? 'নাম দেওয়া হয়নি';
$roleTitle = ($type === 'director') ? ($member['designation'] ?? 'জেলা পরিচালক') : 'শিক্ষক (মুয়াল্লিম)';
$fatherName = $member['father_name'] ?? '';
$dob = $member['dob'] ?? '';
$bloodGroup = $member['blood_group'] ?? 'A+';
$address = $member['address'] ?? ($member['area_name'] ?? ($member['district_name'] ?? ''));
$idNo = $member['id'] ?? '১';
$photoUrl = !empty($member['photo']) ? $member['photo'] : null;
$certSerial = 'KQ-ID-' . strtoupper(substr($type, 0, 1)) . '-' . str_pad((string)$member['id'], 4, '0', STR_PAD_LEFT);
$verifyUrl = "https://project.rasel.cloud/kariana/verify/{$uuid}";
?>

<div class="container mx-auto px-4 py-8 font-bengali">
    <!-- Top Action Bar (Hidden on Print) -->
    <div class="print:hidden mb-8 bg-gradient-to-r from-emerald-950 via-emerald-900 to-teal-950 rounded-3xl p-6 text-white shadow-2xl flex flex-col md:flex-row items-center justify-between gap-6 border border-amber-400/40">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-400/20 text-amber-300 text-xs font-bold uppercase mb-2 border border-amber-400/40">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                কারিয়ানা অফিসিয়াল পরিচয়পত্র টেমপ্লেট
            </div>
            <h1 class="text-2xl md:text-3xl font-black text-amber-200">
                <?= htmlspecialchars($nameBn) ?> — পরিচয়পত্র
            </h1>
            <p class="text-xs md:text-sm text-emerald-200 mt-1">
                পদবি: <span class="text-amber-300 font-bold"><?= htmlspecialchars($roleTitle) ?></span> | 
                আইডি নং: <span class="font-mono text-amber-300 font-bold"><?= htmlspecialchars($certSerial) ?></span> | 
                ভেরিফিকেশন টোকেন: <span class="font-mono text-emerald-300"><?= substr($uuid, 0, 10) ?>...</span>
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center gap-3">
            <a href="<?= $baseUrl ?>/admin/id-card/a4/<?= $type ?>/<?= $member['id'] ?>?auto_print=1" target="_blank" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-emerald-950 font-black text-sm shadow-xl flex items-center gap-2 transition-all transform hover:scale-105">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>🖨️ A4 কার্ড প্রিন্ট করুন</span>
            </a>

            <a href="<?= $baseUrl ?>/admin/id-card/a4/<?= $type ?>/<?= $member['id'] ?>" class="px-4 py-2.5 rounded-xl bg-emerald-800/90 hover:bg-emerald-700 text-white text-xs font-bold shadow-md flex items-center gap-1.5 border border-amber-400/50 transition">
                <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>📄 A4 শিট প্রিভিউ</span>
            </a>

            <a href="<?= $baseUrl ?>/admin/id-card/download-a4/<?= $type ?>/<?= $member['id'] ?>" class="px-4 py-2.5 rounded-xl bg-emerald-800/90 hover:bg-emerald-700 text-white text-xs font-bold shadow-md flex items-center gap-1.5 border border-emerald-600 transition">
                <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span>⬇️ A4 শিট (SVG)</span>
            </a>
            
            <a href="<?= $baseUrl ?>/admin/id-card/download/<?= $type ?>/<?= $member['id'] ?>?side=front" class="px-3.5 py-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-200 text-xs font-bold shadow-sm flex items-center gap-1 border border-slate-700 transition">
                <span>সম্মুখ ভাগ</span>
            </a>

            <a href="<?= $baseUrl ?>/admin/id-card/download/<?= $type ?>/<?= $member['id'] ?>?side=back" class="px-3.5 py-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-200 text-xs font-bold shadow-sm flex items-center gap-1 border border-slate-700 transition">
                <span>পশ্চাৎ ভাগ</span>
            </a>

            <a href="<?= $baseUrl ?>/admin" class="px-4 py-2.5 rounded-xl bg-emerald-950 hover:bg-black text-emerald-300 text-xs font-bold border border-emerald-800 transition">
                ← ফিরে যান
            </a>
        </div>
    </div>

    <!-- VIEW SWITCHER (Hidden on Print) -->
    <div class="print:hidden flex justify-center mb-6">
        <div class="inline-flex p-1.5 rounded-2xl bg-emerald-950/80 border border-emerald-800/80 shadow-inner">
            <button onclick="switchCardView('front')" id="tabBtnFront" class="px-5 py-2 rounded-xl text-xs md:text-sm font-bold transition-all bg-emerald-700 text-white shadow">
                সম্মুখ ভাগ (Front)
            </button>
            <button onclick="switchCardView('back')" id="tabBtnBack" class="px-5 py-2 rounded-xl text-xs md:text-sm font-bold transition-all text-emerald-300 hover:text-white">
                পশ্চাৎ ভাগ (Back)
            </button>
            <button onclick="switchCardView('both')" id="tabBtnBoth" class="px-5 py-2 rounded-xl text-xs md:text-sm font-bold transition-all text-emerald-300 hover:text-white">
                উভয় ভাগ (Side by Side)
            </button>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    <?php if ($msg = \Core\Session::getFlash('success')): ?>
        <div class="print:hidden max-w-xl mx-auto mb-6 p-4 rounded-2xl bg-emerald-900/80 border border-emerald-500 text-emerald-100 text-sm font-bold flex items-center gap-3">
            <span class="text-xl">✅</span>
            <span><?= htmlspecialchars($msg) ?></span>
        </div>
    <?php endif; ?>

    <!-- ID CARD WORKSPACE CANVAS -->
    <div id="idCardWorkspace" class="flex flex-col md:flex-row items-center justify-center gap-8 print:gap-4 print:flex-row print:justify-start">
        
        <!-- 1. FRONT CARD CONTAINER -->
        <div id="frontCardContainer" class="card-display-shell w-full max-w-[340px] sm:max-w-[360px] aspect-[1/1.6] bg-white rounded-2xl shadow-2xl overflow-hidden border-2 border-emerald-700/60 transition-all transform hover:scale-[1.01] print:shadow-none print:border-emerald-800">
            <?= $frontSvg ?>
        </div>

        <!-- 2. BACK CARD CONTAINER -->
        <div id="backCardContainer" class="card-display-shell w-full max-w-[340px] sm:max-w-[360px] aspect-[1/1.6] bg-white rounded-2xl shadow-2xl overflow-hidden border-2 border-emerald-700/60 transition-all transform hover:scale-[1.01] print:shadow-none print:border-emerald-800">
            <?= $backSvg ?>
        </div>

    </div>

    <!-- EDIT CANDIDATE INFORMATION FORM (Hidden on Print) -->
    <div class="print:hidden max-w-3xl mx-auto mt-12 bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-emerald-100">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-xl font-bold">
                    ✏️
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900">আইডি কার্ডের তথ্য সংশোধন বা পরিবর্তন</h3>
                    <p class="text-xs text-slate-500">পিতার নাম, জন্ম তারিখ, রক্তের গ্রুপ, ঠিকানা অথবা ছবি পরিবর্তন করে সাথে সাথে আইডি রিফ্রেশ করুন</p>
                </div>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                ডাটাবেজ সিঙ্ক
            </span>
        </div>

        <form action="<?= $baseUrl ?>/admin/id-card/update/<?= $type ?>/<?= $member['id'] ?>" method="POST" enctype="multipart/form-data" class="space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- পিতার নাম -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">পিতার নাম :</label>
                    <input type="text" name="father_name" value="<?= htmlspecialchars($fatherName) ?>" placeholder="যেমন: মোঃ মালেক মিয়া" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <!-- জন্ম তারিখ -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">জন্ম তারিখ :</label>
                    <input type="text" name="dob" value="<?= htmlspecialchars($dob) ?>" placeholder="যেমন: ২৮/০৯/২০০৪ ইং" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <!-- রক্তের গ্রুপ -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">রক্তের গ্রুপ (Blood Group) :</label>
                    <select name="blood_group" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none bg-white">
                        <?php foreach (['A+', 'B+', 'AB+', 'O+', 'A-', 'B-', 'AB-', 'O-'] as $bg): ?>
                            <option value="<?= $bg ?>" <?= ($bloodGroup === $bg) ? 'selected' : '' ?>><?= $bg ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- পাসপোর্ট সাইজ ছবি -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">প্রোফাইল ছবি আপলোড (ছবি পরিবর্তন করতে) :</label>
                    <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                </div>
            </div>

            <!-- ঠিকানা -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">ঠিকানা :</label>
                <input type="text" name="address" value="<?= htmlspecialchars($address) ?>" placeholder="যেমন: ভাতুড়িয়া, সাধুহাটি, ঝিনাইদহ" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div class="pt-3 flex items-center justify-end gap-3">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-sm shadow-md flex items-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>সংরক্ষণ ও আইডি কার্ড রিফ্রেশ করুন</span>
                </button>
            </div>
        </form>
    </div>

</div>

<!-- PRINT SPECIFIC CSS FOR ACCREDITATION CR80 PLASTIC CARDS -->
<style>
@media print {
    @page {
        size: A4 portrait;
        margin: 10mm;
    }
    body {
        background: #ffffff !important;
        color: #000000 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .print\:hidden {
        display: none !important;
    }
    #idCardWorkspace {
        display: flex !important;
        flex-direction: row !important;
        align-items: flex-start !important;
        justify-content: flex-start !important;
        gap: 15mm !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .card-display-shell {
        display: block !important;
        width: 54mm !important;
        height: 85.6mm !important;
        max-width: 54mm !important;
        page-break-inside: avoid !important;
        border: 1px solid #064e3b !important;
        border-radius: 4mm !important;
        overflow: hidden !important;
    }
    .card-display-shell svg {
        width: 100% !important;
        height: 100% !important;
    }
}
</style>

<!-- CLIENT JAVASCRIPT CONTROLS -->
<script>
function switchCardView(view) {
    const front = document.getElementById('frontCardContainer');
    const back = document.getElementById('backCardContainer');
    const btnF = document.getElementById('tabBtnFront');
    const btnB = document.getElementById('tabBtnBack');
    const btnBoth = document.getElementById('tabBtnBoth');

    // Reset styles
    [btnF, btnB, btnBoth].forEach(b => {
        b.className = 'px-5 py-2 rounded-xl text-xs md:text-sm font-bold transition-all text-emerald-300 hover:text-white';
    });

    if (view === 'front') {
        front.style.display = 'block';
        back.style.display = 'none';
        btnF.className = 'px-5 py-2 rounded-xl text-xs md:text-sm font-bold transition-all bg-emerald-700 text-white shadow';
    } else if (view === 'back') {
        front.style.display = 'none';
        back.style.display = 'block';
        btnB.className = 'px-5 py-2 rounded-xl text-xs md:text-sm font-bold transition-all bg-emerald-700 text-white shadow';
    } else {
        front.style.display = 'block';
        back.style.display = 'block';
        btnBoth.className = 'px-5 py-2 rounded-xl text-xs md:text-sm font-bold transition-all bg-emerald-700 text-white shadow';
    }
}

// Download Card container as High-Res PNG
function downloadAsPng(containerId, filename) {
    const container = document.getElementById(containerId);
    if (!container) return;
    const svgElem = container.querySelector('svg');
    if (!svgElem) return;

    const svgData = new XMLSerializer().serializeToString(svgElem);
    const canvas = document.createElement('canvas');
    canvas.width = 600;
    canvas.height = 960;
    const ctx = canvas.getContext('2d');
    const img = new Image();
    const svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
    const url = URL.createObjectURL(svgBlob);

    img.onload = function() {
        ctx.drawImage(img, 0, 0, 600, 960);
        URL.revokeObjectURL(url);
        const a = document.createElement('a');
        a.download = filename;
        a.href = canvas.toDataURL('image/png');
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    };
    img.src = url;
}
</script>
