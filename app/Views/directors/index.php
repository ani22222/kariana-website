<?php
/**
 * Public District Directors Directory - Kariana Quran
 * 100% Crisp Inline SVGs - Zero Emojis - High-Resolution Demo Assets
 */
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';

// High quality demo scholar / educator portrait assets with standard responsive dimensions (320x320)
$demoAvatars = [
    1 => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=320&h=320&fit=crop&crop=faces&q=80',
    2 => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=320&h=320&fit=crop&crop=faces&q=80',
    3 => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=320&h=320&fit=crop&crop=faces&q=80',
    4 => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=320&h=320&fit=crop&crop=faces&q=80',
];
?>

<div class="container mx-auto px-4 py-10">
    <!-- Header Section -->
    <div class="max-w-4xl mx-auto text-center space-y-4 mb-10">
        <span class="inline-flex items-center px-4 py-1.5 rounded-full bg-emerald-100 text-emerald-950 text-xs font-bold tracking-wider border border-emerald-300/60 shadow-sm">
            <svg class="w-4 h-4 text-amber-600 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            সাংগঠনিক নেটওয়ার্ক ও জেলা নেতৃত্ব
        </span>
        <h1 class="text-3xl md:text-5xl font-extrabold text-emerald-night">
            জেলা পরিচালক <span class="text-gold-deep">তালিকা ও তথ্যকোষ</span>
        </h1>
        <p class="text-base md:text-lg text-slate-600 max-w-2xl mx-auto leading-relaxed">
            সারা বাংলাদেশে ঘরে ঘরে ও বিভিন্ন প্রতিষ্ঠানে সহীহ কুরআন শিক্ষা পৌঁছে দিতে দায়িত্ব পালন করছেন আমাদের সম্মানীয় জেলা পরিচালক ও তাঁদের আওতাধীন মুয়াল্লিমবৃন্দ।
        </p>
    </div>

    <!-- Quick Stats Bar (Warm Ivory Cards) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto mb-10 text-center">
        <div class="bg-[#fffefb] p-4 rounded-2xl border border-[#e4dccb] shadow-sm hover:border-amber-400 transition">
            <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">মোট জেলা পরিচালক</p>
            <h3 class="text-2xl font-black text-emerald-night mt-1"><?= \Core\BengaliHelper::toBengaliNumber(count($directors ?? [])) ?> জন</h3>
        </div>
        <div class="bg-[#fffefb] p-4 rounded-2xl border border-[#e4dccb] shadow-sm hover:border-amber-400 transition">
            <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">সক্রিয় জেলা</p>
            <h3 class="text-2xl font-black text-emerald-night mt-1"><?= \Core\BengaliHelper::toBengaliNumber($activeDistrictsCount ?? 64) ?> টি</h3>
        </div>
        <div class="bg-[#fffefb] p-4 rounded-2xl border border-[#e4dccb] shadow-sm hover:border-amber-400 transition">
            <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">নিবন্ধিত শিক্ষক/মুয়াল্লিম</p>
            <h3 class="text-2xl font-black text-gold-deep mt-1"><?= \Core\BengaliHelper::toBengaliNumber($totalTeachersCount ?? 0) ?> জন</h3>
        </div>
        <div class="bg-[#fffefb] p-4 rounded-2xl border border-[#e4dccb] shadow-sm hover:border-amber-400 transition">
            <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">বর্তমান শিক্ষার্থী</p>
            <h3 class="text-2xl font-black text-emerald-vibrant mt-1"><?= \Core\BengaliHelper::toBengaliNumber($totalStudentsCount ?? 0) ?>+</h3>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-[#fffefb] p-4 md:p-6 rounded-2xl shadow-sm border border-[#e4dccb] max-w-5xl mx-auto mb-10" x-data="{ search: '', division: 'all' }">
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="w-full md:w-1/2 relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" x-model="search" placeholder="জেলা বা পরিচালকের নাম দিয়ে খুঁজুন..." 
                    class="w-full pl-10 pr-4 py-3 border border-[#e2d8c3] rounded-xl text-sm focus:ring-2 focus:ring-emerald-vibrant focus:outline-none bg-[#fdfcf8] text-slate-800">
            </div>

            <div class="w-full md:w-auto flex flex-wrap gap-2">
                <select x-model="division" class="px-4 py-3 border border-[#e2d8c3] rounded-xl text-sm focus:ring-2 focus:ring-emerald-vibrant bg-[#fdfcf8] text-slate-800">
                    <option value="all">সকল বিভাগ</option>
                    <option value="ঢাকা">ঢাকা</option>
                    <option value="চট্টগ্রাম">চট্টগ্রাম</option>
                    <option value="রাজশাহী">রাজশাহী</option>
                    <option value="খুলনা">খুলনা</option>
                    <option value="সিলেট">সিলেট</option>
                    <option value="বরিশাল">বরিশাল</option>
                    <option value="রংপুর">রংপুর</option>
                    <option value="ময়মনসিংহ">ময়মনসিংহ</option>
                </select>
                <a href="<?= $baseUrl ?>/director/login" class="bg-emerald-night hover:bg-emerald-deep text-white px-5 py-3 rounded-xl text-sm font-bold shadow transition flex items-center">
                    <svg class="w-4 h-4 mr-2 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    পরিচালক পোর্টাল
                </a>
            </div>
        </div>

        <!-- Directors Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
            <?php if (empty($directors)): ?>
                <div class="col-span-full text-center py-12 text-slate-400">
                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <p class="font-medium text-sm">কোনো জেলা পরিচালকের তথ্য পাওয়া যায়নি।</p>
                </div>
            <?php else: ?>
                <?php foreach ($directors as $dir): 
                    $avatarUrl = !empty($dir['photo']) ? $dir['photo'] : ($demoAvatars[$dir['id']] ?? $demoAvatars[1]);
                ?>
                    <div x-show="(division === 'all' || division === '<?= $dir['division_name'] ?>') && (search === '' || '<?= addslashes($dir['name'] . ' ' . $dir['district_name']) ?>'.toLowerCase().includes(search.toLowerCase()))"
                        class="bg-[#fffefb] rounded-2xl border border-[#e5ddcb] hover:border-gold-rich/80 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden group">
                        
                        <!-- Top Banner / Badge -->
                        <div class="p-5 pb-0">
                            <div class="flex items-center justify-between mb-3">
                                <span class="inline-flex items-center px-3 py-1 bg-emerald-50 text-emerald-night text-xs font-bold rounded-full border border-emerald-200 shadow-sm">
                                    <svg class="w-3.5 h-3.5 text-amber-600 mr-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <?= htmlspecialchars($dir['district_name']) ?>
                                </span>
                                
                                <?php if ($dir['status'] === 'active'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 mr-1.5 animate-pulse"></span> কর্মরত
                                    </span>
                                <?php elseif ($dir['status'] === 'suspended'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                        স্থগিত
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                        বহিষ্কৃত
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="flex items-start space-x-3.5 space-x-reverse">
                                <!-- High Resolution Scholar Demo Avatar with Verified Badge -->
                                <div class="relative shrink-0">
                                    <img src="<?= htmlspecialchars($avatarUrl) ?>" 
                                         alt="<?= htmlspecialchars($dir['name']) ?>" 
                                         loading="lazy"
                                         title="ডেমো ছবি সাইজ: ৩২০x৩২০ পিক্সেল (রেসপন্সিভ অবজেক্ট-ফিট)"
                                         class="w-16 h-16 rounded-2xl object-cover border-2 border-amber-400/60 shadow-md group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute -bottom-1 -right-1 bg-amber-500 text-white rounded-full p-0.5 shadow-sm border border-white" title="ভেরিফাইড পরিচালক">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>

                                <div class="overflow-hidden flex-1">
                                    <h3 class="font-bold text-slate-900 text-base leading-snug truncate group-hover:text-emerald-deep transition">
                                        <a href="<?= $baseUrl ?>/directors/<?= $dir['slug'] ?>">
                                            <?= htmlspecialchars($dir['name']) ?>
                                        </a>
                                    </h3>
                                    <p class="text-xs text-amber-700 font-semibold mt-0.5 flex items-center">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                        <?= htmlspecialchars($dir['designation']) ?>
                                    </p>
                                    <p class="text-xs text-slate-500 mt-1 truncate flex items-center">
                                        <svg class="w-3.5 h-3.5 text-slate-400 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                        </svg>
                                        <?= htmlspecialchars($dir['qualification'] ?? 'প্রশিক্ষক') ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics Box inside card -->
                        <div class="px-5 py-3 my-3 bg-slate-50/80 border-y border-slate-100 flex items-center justify-between text-xs">
                            <div class="flex items-center text-slate-600">
                                <svg class="w-4 h-4 text-emerald-800 mr-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <span>মুয়াল্লিম/শিক্ষক:</span>
                                <strong class="ml-1 text-slate-900 font-bold"><?= \Core\BengaliHelper::toBengaliNumber($dir['teachers_count'] ?? 0) ?> জন</strong>
                            </div>
                            <div class="text-slate-500 font-medium">
                                <span class="text-[11px] bg-white px-2 py-0.5 rounded border border-slate-200"><?= htmlspecialchars($dir['division_name']) ?> বিভাগ</span>
                            </div>
                        </div>

                        <!-- Actions / Contact Buttons (Pure SVGs) -->
                        <div class="p-5 pt-0 flex items-center gap-2">
                            <a href="tel:<?= preg_replace('/[^0-9+]/', '', $dir['phone']) ?>" 
                                class="flex-1 bg-emerald-night hover:bg-emerald-deep text-white py-2.5 px-3 rounded-xl text-xs font-bold text-center transition flex items-center justify-center shadow-sm">
                                <svg class="w-3.5 h-3.5 mr-1.5 text-amber-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                কল করুন
                            </a>

                            <?php if (!empty($dir['whatsapp'])): ?>
                                <a href="https://wa.me/88<?= preg_replace('/[^0-9]/', '', $dir['whatsapp']) ?>" target="_blank"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white p-2.5 rounded-xl text-xs font-bold transition flex items-center justify-center w-9 h-9 shrink-0 shadow-sm" title="WhatsApp বার্তা পাঠান">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                    </svg>
                                </a>
                            <?php endif; ?>

                            <a href="<?= $baseUrl ?>/directors/<?= $dir['slug'] ?>" 
                                class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 py-2.5 px-3 rounded-xl text-xs font-bold text-center transition flex items-center justify-center hover:border-amber-400">
                                <span>শিক্ষক তালিকা</span>
                                <svg class="w-3.5 h-3.5 ml-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
