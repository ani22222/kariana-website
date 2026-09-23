<?php
/**
 * Public District Director Profile & Teachers List - Kariana Quran
 * Redesigned: Modern Royal Islamic Profile Layout
 * 100% Dark/Light Mode Compatible — Zero White Patches
 */
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';

// High quality demo scholar portrait assets with explicit responsive dimensions (400x400)
$demoAvatars = [
    1 => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop&crop=faces&q=85',
    2 => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&h=400&fit=crop&crop=faces&q=85',
    3 => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop&crop=faces&q=85',
    4 => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=400&h=400&fit=crop&crop=faces&q=85',
];
$avatarUrl = !empty($director['photo']) ? $director['photo'] : ($demoAvatars[$director['id']] ?? $demoAvatars[1]);
?>

<div class="container mx-auto px-4 py-10">
    <!-- Breadcrumb -->
    <div class="max-w-5xl mx-auto mb-6">
        <nav class="flex items-center text-xs text-slate-500 dark:text-slate-400 space-x-2">
            <a href="<?= $baseUrl ?>/" class="hover:text-emerald-night dark:hover:text-amber-400 transition">হোম</a>
            <span class="dark:text-slate-600">/</span>
            <a href="<?= $baseUrl ?>/directors" class="hover:text-emerald-night dark:hover:text-amber-400 transition">জেলা পরিচালকবৃন্দ</a>
            <span class="dark:text-slate-600">/</span>
            <span class="text-emerald-night dark:text-amber-300 font-bold truncate"><?= htmlspecialchars($director['name']) ?></span>
        </nav>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════
         MAIN DIRECTOR PROFILE CARD
         ═══════════════════════════════════════════════════════════════ -->
    <div class="max-w-5xl mx-auto bg-white dark:bg-[#082b20] rounded-3xl shadow-sm dark:shadow-2xl dark:shadow-emerald-950/50 border border-slate-200/80 dark:border-emerald-800/50 overflow-hidden mb-10">
        
        <!-- Profile Hero Banner -->
        <div class="bg-gradient-to-r from-emerald-night via-emerald-deep to-emerald-night p-6 md:p-8 text-white relative">
            <div class="flex flex-col md:flex-row items-center md:items-start space-y-4 md:space-y-0 md:space-x-6 text-center md:text-left">
                
                <!-- Scholar High-Resolution Portrait with Verified Emblem -->
                <div class="relative shrink-0">
                    <img src="<?= htmlspecialchars($avatarUrl) ?>" 
                         alt="<?= htmlspecialchars($director['name']) ?>" 
                         loading="lazy"
                         class="w-24 h-24 md:w-32 md:h-32 rounded-3xl object-cover border-4 border-amber-400/80 shadow-2xl">
                    <div class="absolute -bottom-2 -right-2 bg-amber-500 text-white rounded-full p-1 shadow-lg border-2 border-emerald-night" title="ভেরিফাইড জেলা পরিচালক">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>

                <div class="space-y-2 flex-1">
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-2">
                        <span class="px-3 py-1 bg-amber-600 text-white text-xs font-bold rounded-full shadow-sm">
                            <?= htmlspecialchars($director['designation']) ?>
                        </span>
                        
                        <?php if ($director['status'] === 'active'): ?>
                            <span class="px-3 py-1 bg-emerald-700/80 text-emerald-100 text-xs font-bold rounded-full border border-emerald-500 flex items-center">
                                <span class="w-2 h-2 rounded-full bg-emerald-300 inline-block mr-1.5 animate-pulse"></span> দায়িত্বপালনরত
                            </span>
                        <?php elseif ($director['status'] === 'suspended'): ?>
                            <span class="px-3 py-1 bg-amber-600 text-white text-xs font-bold rounded-full">
                                দায়িত্ব স্থগিত
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
                    
                    <p class="text-xs md:text-sm text-emerald-100/90 flex items-center justify-center md:justify-start gap-1.5">
                        <svg class="w-4 h-4 text-amber-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span><?= htmlspecialchars($director['district_name']) ?> জেলা, <?= htmlspecialchars($director['division_name']) ?> বিভাগ</span>
                    </p>

                    <p class="text-xs md:text-sm text-emerald-100/70 flex items-center justify-center md:justify-start gap-1.5">
                        <svg class="w-4 h-4 text-amber-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        </svg>
                        <span><?= htmlspecialchars($director['qualification'] ?? 'অভিজ্ঞ ইসলামিক শিক্ষক') ?></span>
                    </p>
                </div>

                <!-- Direct Contact Buttons -->
                <div class="flex flex-col gap-2.5 w-full md:w-auto">
                    <a href="tel:<?= preg_replace('/[^0-9+]/', '', $director['phone']) ?>" 
                        class="bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-md transition flex items-center justify-center active:scale-[0.97]">
                        <svg class="w-4 h-4 mr-2 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        সরাসরি কল করুন
                    </a>
                    
                    <?php if (!empty($director['whatsapp'])): ?>
                        <a href="https://wa.me/88<?= preg_replace('/[^0-9]/', '', $director['whatsapp']) ?>" target="_blank"
                            class="bg-emerald-600 hover:bg-emerald-500 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-md transition flex items-center justify-center active:scale-[0.97]">
                            <svg class="w-4 h-4 mr-2 text-white shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                            </svg>
                            হোয়াটসঅ্যাপ বার্তা
                        </a>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <!-- Director Information & Bio -->
        <div class="p-6 md:p-8 bg-slate-50 dark:bg-[#041e17] border-b border-slate-200 dark:border-emerald-800/40">
            <h3 class="text-sm font-bold text-slate-800 dark:text-amber-200 uppercase tracking-wider mb-2 flex items-center">
                <svg class="w-4 h-4 text-emerald-800 dark:text-emerald-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                পরিচিতি ও সাংগঠনিক দায়িত্ব
            </h3>
            <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                <?= nl2br(htmlspecialchars($director['bio'] ?? 'কারিয়ানা কুরআন শিক্ষা সোসাইটির কার্যক্রম সারা বাংলাদেশে ছড়িয়ে দিতে নিবেদিতভাবে কাজ করছেন এই সম্মানিত জেলা পরিচালক।')) ?>
            </p>
            
            <?php if (!empty($director['address'])): ?>
                <div class="mt-4 pt-3 border-t border-slate-200 dark:border-emerald-800/40 flex items-center text-xs text-slate-600 dark:text-slate-400">
                    <svg class="w-4 h-4 text-emerald-800 dark:text-emerald-400 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span><strong class="dark:text-amber-300">যোগাযোগের প্রাতিষ্ঠানিক ঠিকানা:</strong> <?= htmlspecialchars($director['address']) ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════
         SUBORDINATE TEACHERS SECTION
         ═══════════════════════════════════════════════════════════════ -->
    <div class="max-w-5xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-200 dark:border-emerald-800/40 mb-6">
            <div>
                <span class="text-xs font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider">কুরআন মুয়াল্লিম নেটওয়ার্ক</span>
                <h2 class="text-2xl font-bold text-emerald-night dark:text-amber-100">
                    <?= htmlspecialchars($director['district_name']) ?> জেলার আওতাধীন শিক্ষক ও মুয়াল্লিমবৃন্দ
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    মোট <?= \Core\BengaliHelper::toBengaliNumber(count($teachers ?? [])) ?> জন শিক্ষক বাসা, মসজিদ ও মাদ্রাসায় কুরআন শিক্ষা প্রদান করছেন
                </p>
            </div>
            
            <div class="mt-3 sm:mt-0">
                <a href="<?= $baseUrl ?>/admission" class="bg-emerald-night hover:bg-emerald-deep text-white px-4 py-2.5 rounded-xl text-xs font-bold shadow transition inline-flex items-center active:scale-[0.97]">
                    <svg class="w-4 h-4 mr-1.5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    শিক্ষক নিয়োগ বা প্রশিক্ষণে আবেদন
                </a>
            </div>
        </div>

        <?php if (empty($teachers)): ?>
            <div class="bg-white dark:bg-[#082b20] p-8 rounded-2xl text-center border border-slate-200/80 dark:border-emerald-800/50 text-slate-400 dark:text-slate-500">
                <svg class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <p class="font-medium text-sm">এই পরিচালকের আওতাধীন নতুন শিক্ষকদের তালিকা শীঘ্রই যুক্ত করা হবে।</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($teachers as $idx => $t): ?>
                    <div class="bg-white dark:bg-[#082b20] p-5 rounded-2xl border border-slate-200/80 dark:border-emerald-800/50 hover:border-amber-400/80 dark:hover:border-amber-500/50 shadow-sm hover:shadow-md dark:hover:shadow-emerald-900/50 transition-all duration-300">
                        <div class="flex items-start space-x-3">
                            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/40 text-emerald-950 dark:text-emerald-300 flex items-center justify-center font-bold text-base shrink-0 border border-emerald-200 dark:border-emerald-700/50">
                                <?= mb_substr($t['name'], 0, 1, 'UTF-8') ?>
                            </div>
                            <div class="overflow-hidden flex-1">
                                <h4 class="font-bold text-slate-900 dark:text-amber-100 text-sm truncate"><?= htmlspecialchars($t['name']) ?></h4>
                                <p class="text-xs text-emerald-800 dark:text-emerald-400 font-medium mt-0.5 flex items-center truncate">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-500 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <?= htmlspecialchars($t['qualification'] ?? 'কুরআন শিক্ষক') ?>
                                </p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 truncate flex items-center">
                                    <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    </svg>
                                    <?= htmlspecialchars($t['area_name']) ?>
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-emerald-800/40 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                            <div>
                                <span class="bg-slate-100 dark:bg-emerald-900/40 px-2 py-0.5 rounded text-[11px] font-semibold text-slate-700 dark:text-slate-300 border border-transparent dark:border-emerald-700/40">
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
                                <span class="text-amber-800 dark:text-amber-400 font-bold"><?= \Core\BengaliHelper::toBengaliNumber($t['total_students']) ?> জন শিক্ষার্থী</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- ═══════════════════════════════════════════════════════════
             INQUIRY CALLOUT CARD
             ═══════════════════════════════════════════════════════════ -->
        <div class="mt-10 bg-emerald-50/80 dark:bg-[#0a2e22] p-6 md:p-8 rounded-3xl border border-emerald-200/90 dark:border-emerald-800/50 flex flex-col md:flex-row items-center justify-between gap-6 shadow-sm dark:shadow-emerald-950/50">
            <div class="space-y-2 text-center md:text-left">
                <h3 class="text-lg font-bold text-emerald-night dark:text-amber-200">আপনার এলাকায় বিশ্বস্ত কুরআন শিক্ষক পেতে চান?</h3>
                <p class="text-xs md:text-sm text-slate-600 dark:text-slate-300 max-w-xl">
                    সরাসরি <?= htmlspecialchars($director['district_name']) ?> জেলা পরিচালকের সাথে যোগাযোগ করুন অথবা অনলাইনে ভর্তি ফরম পূরণ করুন।
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <a href="tel:<?= preg_replace('/[^0-9+]/', '', $director['phone']) ?>" 
                    class="bg-emerald-night hover:bg-emerald-deep text-white px-5 py-2.5 rounded-xl font-bold text-xs shadow transition flex items-center justify-center active:scale-[0.97]">
                    <svg class="w-4 h-4 mr-2 text-amber-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    পরিচালকের সাথে কথা বলুন
                </a>
                <a href="<?= $baseUrl ?>/admission" 
                    class="bg-white dark:bg-emerald-900/40 border-2 border-emerald-night dark:border-emerald-600 text-emerald-night dark:text-emerald-200 hover:bg-emerald-100/50 dark:hover:bg-emerald-800/40 px-5 py-2.5 rounded-xl font-bold text-xs transition flex items-center justify-center active:scale-[0.97]">
                    অনলাইন ভর্তি আবেদন
                </a>
            </div>
        </div>

    </div>
</div>
