<?php
/**
 * 64 Districts Prayer Times View
 */
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';
?>
<section class="max-w-7xl mx-auto px-4 py-12">
    <div class="text-center mb-12">
        <h1 class="text-3xl sm:text-4xl font-bold text-emerald-950 mb-3">৬৪ জেলার নামাজের সময়সূচি</h1>
        <p class="text-slate-600 max-w-2xl mx-auto text-sm sm:text-base">ইসলামিক ফাউন্ডেশন বাংলাদেশের সার্বিক দিকনির্দেশনা ও হানাফী মাযহাবের তাহক্বীক্ব অনুসারে প্রস্তুতকৃত।</p>
    </div>

    <!-- Active District Card -->
    <div class="max-w-3xl mx-auto islamic-card p-6 border-2 border-amber-500 mb-10 text-center">
        <h2 class="text-2xl font-bold text-emerald-900 mb-1">আজকের নামাজের সময়সূচি (ঢাকা ও পার্শ্ববর্তী এলাকা)</h2>
        <p class="text-xs text-slate-500 mb-6">অন্য জেলা নির্বাচন করতে নিচের তালিকা থেকে পছন্দ করুন</p>
        <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
            <div class="p-3 rounded-lg bg-emerald-50 border border-emerald-200">
                <div class="text-xs text-emerald-700 font-semibold">ফজর</div>
                <div class="text-base font-bold text-emerald-950">৪:৩৫</div>
            </div>
            <div class="p-3 rounded-lg bg-emerald-50 border border-emerald-200">
                <div class="text-xs text-emerald-700 font-semibold">সূর্যোদয়</div>
                <div class="text-base font-bold text-emerald-950">৫:৪৮</div>
            </div>
            <div class="p-3 rounded-lg bg-emerald-50 border border-emerald-200">
                <div class="text-xs text-emerald-700 font-semibold">যোহর</div>
                <div class="text-base font-bold text-emerald-950">১১:৫২</div>
            </div>
            <div class="p-3 rounded-lg bg-emerald-50 border border-emerald-200">
                <div class="text-xs text-emerald-700 font-semibold">আসর</div>
                <div class="text-base font-bold text-emerald-950">৪:১৩</div>
            </div>
            <div class="p-3 rounded-lg bg-amber-50 border border-amber-300">
                <div class="text-xs text-amber-800 font-semibold">মাগরিব</div>
                <div class="text-base font-bold text-amber-950">৫:৫৬</div>
            </div>
            <div class="p-3 rounded-lg bg-emerald-50 border border-emerald-200">
                <div class="text-xs text-emerald-700 font-semibold">এশা</div>
                <div class="text-base font-bold text-emerald-950">৭:১২</div>
            </div>
        </div>
    </div>

    <!-- 64 District Grid -->
    <h3 class="text-xl font-bold text-emerald-900 mb-4">সকল জেলার সময় অফসেট তালিকা:</h3>
    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
        <?php if (!empty($districts)): ?>
            <?php foreach ($districts as $d): ?>
                <div class="p-2.5 rounded-lg bg-white border border-slate-200 text-xs flex justify-between items-center">
                    <span class="font-bold text-emerald-950"><?= htmlspecialchars($d['name_bn'], ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="text-slate-500 font-mono"><?= $d['fajr_offset'] >= 0 ? '+' . $d['fajr_offset'] : $d['fajr_offset'] ?> মি.</span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
