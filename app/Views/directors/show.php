<?php
/**
 * Public District Director Profile & Teachers List - Kariana Quran
 */
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';
?>
<div class="container mx-auto px-4 py-10">
    <!-- Breadcrumb -->
    <div class="max-w-5xl mx-auto mb-6">
        <nav class="flex text-xs text-slate-500 space-x-2 space-x-reverse">
            <a href="<?= $baseUrl ?>/" class="hover:text-emerald-night">হোম</a>
            <span>/</span>
            <a href="<?= $baseUrl ?>/directors" class="hover:text-emerald-night">জেলা পরিচালকবৃন্দ</a>
            <span>/</span>
            <span class="text-emerald-night font-bold"><?= htmlspecialchars($director['name']) ?></span>
        </nav>
    </div>

    <!-- Main Director Profile Card (Warm Ivory & Royal Emerald) -->
    <div class="max-w-5xl mx-auto bg-[#fffefb] rounded-3xl shadow-sm border border-[#e5ddcb] overflow-hidden mb-10">
        <div class="bg-gradient-to-r from-emerald-night via-emerald-deep to-emerald-night p-6 md:p-8 text-white relative">
            <div class="flex flex-col md:flex-row items-center md:items-start space-y-4 md:space-y-0 md:space-x-6 md:space-x-reverse text-center md:text-right">
                
                <div class="w-24 h-24 md:w-32 md:h-32 rounded-3xl bg-gold-rich text-white flex items-center justify-center font-bold text-4xl shadow-xl border-4 border-emerald-800 flex-shrink-0">
                    <?= mb_substr($director['name'], 0, 1, 'UTF-8') ?>
                </div>

                <div class="space-y-2 flex-1">
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-2">
                        <span class="px-3 py-1 bg-gold-deep text-white text-xs font-bold rounded-full">
                            <?= htmlspecialchars($director['designation']) ?>
                        </span>
                        
                        <?php if ($director['status'] === 'active'): ?>
                            <span class="px-3 py-1 bg-emerald-700/80 text-emerald-100 text-xs font-bold rounded-full border border-emerald-500">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-300 inline-block mr-1"></span> দায়িত্বপালনরত
                            </span>
                        <?php elseif ($director['status'] === 'suspended'): ?>
                            <span class="px-3 py-1 bg-amber-600 text-white text-xs font-bold rounded-full">
                                দায়িত্ব স্থগিত
                            </span>
                        <?php else: ?>
                            <span class="px-3 py-1 bg-red-600 text-white text-xs font-bold rounded-full">
                                বহিষ্কৃত
                            </span>
                        <?php endif; ?>
                    </div>

                    <h1 class="text-2xl md:text-3xl font-extrabold text-white">
                        <?= htmlspecialchars($director['name']) ?>
                    </h1>
                    
                    <p class="text-xs md:text-sm text-emerald-100/90 flex items-center justify-center md:justify-start gap-2">
                        <i class="fas fa-map-marked-alt text-gold-shimmer"></i> 
                        <?= htmlspecialchars($director['district_name']) ?> জেলা, <?= htmlspecialchars($director['division_name']) ?> বিভাগ
                    </p>

                    <p class="text-xs md:text-sm text-emerald-100/70">
                        <i class="fas fa-graduation-cap text-gold-shimmer mr-1"></i>
                        <?= htmlspecialchars($director['qualification'] ?? 'অভিজ্ঞ ইসলামিক শিক্ষক') ?>
                    </p>
                </div>

                <!-- Direct Contact Buttons -->
                <div class="flex flex-col gap-2 w-full md:w-auto">
                    <a href="tel:<?= preg_replace('/[^0-9+]/', '', $director['phone']) ?>" 
                        class="bg-gold-rich hover:bg-gold-deep text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow transition flex items-center justify-center">
                        <i class="fas fa-phone-alt mr-2"></i> সরাসরি কল করুন
                    </a>
                    
                    <?php if (!empty($director['whatsapp'])): ?>
                        <a href="https://wa.me/88<?= preg_replace('/[^0-9]/', '', $director['whatsapp']) ?>" target="_blank"
                            class="bg-emerald-600 hover:bg-emerald-500 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow transition flex items-center justify-center">
                            <i class="fab fa-whatsapp mr-2 text-base"></i> হোয়াটসঅ্যাপ মেসেজ
                        </a>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <!-- Director Information & Bio -->
        <div class="p-6 md:p-8 bg-slate-50 border-b border-slate-200">
            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-2">পরিচিতি ও দায়িত্ব</h3>
            <p class="text-sm text-slate-600 leading-relaxed">
                <?= nl2br(htmlspecialchars($director['bio'] ?? 'কারিয়ানা কুরআন শিক্ষা সোসাইটির কার্যক্রম সারা বাংলাদেশে ছড়িয়ে দিতে নিবেদিতভাবে কাজ করছেন এই সম্মানিত জেলা পরিচালক।')) ?>
            </p>
            
            <?php if (!empty($director['address'])): ?>
                <div class="mt-4 pt-3 border-t border-slate-200 flex items-center text-xs text-slate-500">
                    <i class="fas fa-building mr-2 text-emerald-night"></i>
                    <span><strong>যোগাযোগের ঠিকানা:</strong> <?= htmlspecialchars($director['address']) ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Subordinate Instructors / Teachers Section -->
    <div class="max-w-5xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-200 mb-6">
            <div>
                <span class="text-xs font-bold text-gold-deep uppercase">কুরআন মুয়াল্লিম নেটওয়ার্ক</span>
                <h2 class="text-2xl font-bold text-emerald-night">
                    <?= htmlspecialchars($director['district_name']) ?> জেলার আওতাধীন শিক্ষক ও মুয়াল্লিমবৃন্দ
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    মোট <?= \Core\BengaliHelper::toBengaliNumber(count($teachers ?? [])) ?> জন শিক্ষক বাসা, মসজিদ ও মাদ্রাসায় কুরআন শিক্ষা প্রদান করছেন
                </p>
            </div>
            
            <div class="mt-3 sm:mt-0">
                <a href="<?= $baseUrl ?>/admission" class="bg-emerald-night hover:bg-emerald-deep text-white px-4 py-2 rounded-xl text-xs font-bold shadow transition inline-flex items-center">
                    <i class="fas fa-user-plus mr-1.5 text-gold-shimmer"></i> শিক্ষক নিয়োগ বা প্রশিক্ষণে আবেদন
                </a>
            </div>
        </div>

        <?php if (empty($teachers)): ?>
            <div class="bg-[#fffefb] p-8 rounded-2xl text-center border border-[#e5ddcb] text-slate-400">
                <i class="fas fa-chalkboard-teacher text-3xl mb-2"></i>
                <p>এই পরিচালকের আওতাধীন নতুন শিক্ষকদের তালিকা শীঘ্রই যুক্ত করা হবে।</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($teachers as $idx => $t): ?>
                    <div class="bg-[#fffefb] p-5 rounded-2xl border border-[#e5ddcb] hover:border-gold-rich/60 shadow-sm hover:shadow-md transition">
                        <div class="flex items-start space-x-3 space-x-reverse">
                            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-night flex items-center justify-center font-bold text-base flex-shrink-0 border border-emerald-100">
                                <?= mb_substr($t['name'], 0, 1, 'UTF-8') ?>
                            </div>
                            <div class="overflow-hidden flex-1">
                                <h4 class="font-bold text-slate-800 text-sm truncate"><?= htmlspecialchars($t['name']) ?></h4>
                                <p class="text-xs text-emerald-vibrant font-medium mt-0.5"><i class="fas fa-certificate mr-1"></i><?= htmlspecialchars($t['qualification'] ?? 'কুরআন শিক্ষক') ?></p>
                                <p class="text-xs text-slate-400 mt-1 truncate"><i class="fas fa-map-pin mr-1"></i><?= htmlspecialchars($t['area_name']) ?></p>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                            <div>
                                <span class="bg-slate-100 px-2 py-0.5 rounded text-[11px] font-semibold text-slate-700">
                                    <?php 
                                    $loc = [
                                        'home' => 'হোম টিউশন / বাসা',
                                        'mosque' => 'মসজিদ ভিত্তিক',
                                        'madrasa' => 'মাদ্রাসা ভিত্তিক',
                                        'institution' => 'প্রাতিষ্ঠানিক'
                                    ];
                                    echo $loc[$t['location_type']] ?? 'কুরআন ক্লাস';
                                    ?>
                                </span>
                            </div>
                            <div>
                                <span class="text-gold-deep font-bold"><?= \Core\BengaliHelper::toBengaliNumber($t['total_students']) ?> জন শিক্ষার্থী</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Inquiry Callout Card -->
        <div class="mt-10 bg-emerald-50 p-6 md:p-8 rounded-3xl border border-emerald-200 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="space-y-2 text-center md:text-right">
                <h3 class="text-lg font-bold text-emerald-night">আপনার এলাকায় বিশ্বস্ত কুরআন শিক্ষক পেতে চান?</h3>
                <p class="text-xs md:text-sm text-slate-600 max-w-xl">
                    সরাসরি <?= htmlspecialchars($director['district_name']) ?> জেলা পরিচালকের সাথে যোগাযোগ করুন অথবা অনলাইনে ভর্তি ফরম পূরণ করুন।
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <a href="tel:<?= preg_replace('/[^0-9+]/', '', $director['phone']) ?>" 
                    class="bg-emerald-night hover:bg-emerald-deep text-white px-5 py-2.5 rounded-xl font-bold text-xs shadow transition flex items-center justify-center">
                    <i class="fas fa-phone mr-1.5 text-gold-shimmer"></i> পরিচালকের সাথে কথা বলুন
                </a>
                <a href="<?= $baseUrl ?>/admission" 
                    class="bg-white border-2 border-emerald-night text-emerald-night hover:bg-emerald-100/50 px-5 py-2.5 rounded-xl font-bold text-xs transition flex items-center justify-center">
                    অনলাইন ভর্তি আবেদন
                </a>
            </div>
        </div>

    </div>
</div>
