<?php
/**
 * Public District Directors Directory - Kariana Quran
 */
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';
?>
<div class="container mx-auto px-4 py-10">
    <!-- Header Section -->
    <div class="max-w-4xl mx-auto text-center space-y-4 mb-10">
        <span class="inline-flex items-center px-4 py-1.5 rounded-full bg-emerald-100 text-emerald-night text-xs font-bold tracking-wider">
            <i class="fas fa-sitemap text-gold-rich mr-2"></i> সাংগঠনিক নেটওয়ার্ক ও জেলা নেতৃত্ব
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
        <div class="bg-[#fffefb] p-4 rounded-2xl border border-[#e4dccb] shadow-sm">
            <p class="text-xs text-slate-500 font-bold uppercase">মোট জেলা পরিচালক</p>
            <h3 class="text-2xl font-black text-emerald-night mt-1"><?= \Core\BengaliHelper::toBengaliNumber(count($directors ?? [])) ?> জন</h3>
        </div>
        <div class="bg-[#fffefb] p-4 rounded-2xl border border-[#e4dccb] shadow-sm">
            <p class="text-xs text-slate-500 font-bold uppercase">সক্রিয় জেলা</p>
            <h3 class="text-2xl font-black text-emerald-night mt-1"><?= \Core\BengaliHelper::toBengaliNumber($activeDistrictsCount ?? 64) ?> টি</h3>
        </div>
        <div class="bg-[#fffefb] p-4 rounded-2xl border border-[#e4dccb] shadow-sm">
            <p class="text-xs text-slate-500 font-bold uppercase">নিবন্ধিত শিক্ষক/মুয়াল্লিম</p>
            <h3 class="text-2xl font-black text-gold-deep mt-1"><?= \Core\BengaliHelper::toBengaliNumber($totalTeachersCount ?? 0) ?> জন</h3>
        </div>
        <div class="bg-[#fffefb] p-4 rounded-2xl border border-[#e4dccb] shadow-sm">
            <p class="text-xs text-slate-500 font-bold uppercase">বর্তমান শিক্ষার্থী</p>
            <h3 class="text-2xl font-black text-emerald-vibrant mt-1"><?= \Core\BengaliHelper::toBengaliNumber($totalStudentsCount ?? 0) ?>+</h3>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-[#fffefb] p-4 md:p-6 rounded-2xl shadow-sm border border-[#e4dccb] max-w-5xl mx-auto mb-10" x-data="{ search: '', division: 'all' }">
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="w-full md:w-1/2 relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" x-model="search" placeholder="জেলা বা পরিচালকের নাম দিয়ে খুঁজুন..." 
                    class="w-full pl-10 pr-4 py-3 border border-[#e2d8c3] rounded-xl text-sm focus:ring-2 focus:ring-emerald-vibrant focus:outline-none bg-[#fdfcf8]">
            </div>

            <div class="w-full md:w-auto flex flex-wrap gap-2">
                <select x-model="division" class="px-4 py-3 border border-[#e2d8c3] rounded-xl text-sm focus:ring-2 focus:ring-emerald-vibrant bg-[#fdfcf8]">
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
                    <i class="fas fa-user-shield mr-2 text-gold-shimmer"></i> পরিচালক পোর্টাল
                </a>
            </div>
        </div>

        <!-- Directors Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
            <?php if (empty($directors)): ?>
                <div class="col-span-full text-center py-12 text-slate-400">
                    <i class="fas fa-user-friends text-4xl mb-3"></i>
                    <p>কোনো জেলা পরিচালকের তথ্য পাওয়া যায়নি।</p>
                </div>
            <?php else: ?>
                <?php foreach ($directors as $dir): ?>
                    <div x-show="(division === 'all' || division === '<?= $dir['division_name'] ?>') && (search === '' || '<?= addslashes($dir['name'] . ' ' . $dir['district_name']) ?>'.toLowerCase().includes(search.toLowerCase()))"
                        class="bg-[#fffefb] rounded-2xl border border-[#e5ddcb] hover:border-gold-rich/60 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden">
                        
                        <!-- Top Banner / Badge -->
                        <div class="p-5 pb-0">
                            <div class="flex items-center justify-between mb-3">
                                <span class="inline-block px-3 py-1 bg-emerald-50 text-emerald-night text-xs font-bold rounded-full border border-emerald-200">
                                    <i class="fas fa-map-marker-alt text-gold-rich mr-1"></i> <?= htmlspecialchars($dir['district_name']) ?> জেলা
                                </span>
                                
                                <?php if ($dir['status'] === 'active'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 mr-1.5 animate-pulse"></span> কর্মরত
                                    </span>
                                <?php elseif ($dir['status'] === 'suspended'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                        <i class="fas fa-pause-circle mr-1"></i> স্থগিত
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                        <i class="fas fa-ban mr-1"></i> বহিষ্কৃত
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="flex items-start space-x-3.5 space-x-reverse">
                                <div class="w-14 h-14 rounded-2xl bg-emerald-night text-gold-shimmer flex items-center justify-center font-bold text-xl flex-shrink-0 shadow-md">
                                    <?= mb_substr($dir['name'], 0, 1, 'UTF-8') ?>
                                </div>
                                <div class="overflow-hidden">
                                    <h3 class="font-bold text-slate-800 text-base leading-snug truncate hover:text-emerald-night">
                                        <a href="<?= $baseUrl ?>/directors/<?= $dir['slug'] ?>">
                                            <?= htmlspecialchars($dir['name']) ?>
                                        </a>
                                    </h3>
                                    <p class="text-xs text-gold-deep font-semibold mt-0.5"><?= htmlspecialchars($dir['designation']) ?></p>
                                    <p class="text-xs text-slate-400 mt-1 truncate"><i class="fas fa-graduation-cap mr-1"></i><?= htmlspecialchars($dir['qualification'] ?? 'প্রশিক্ষক') ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics Box inside card -->
                        <div class="px-5 py-3 my-3 bg-slate-50 border-y border-slate-100 flex items-center justify-between text-xs">
                            <div class="flex items-center text-slate-600">
                                <i class="fas fa-chalkboard-teacher text-emerald-night mr-1.5"></i>
                                <span>মুয়াল্লিম/শিক্ষক:</span>
                                <strong class="ml-1 text-slate-900 font-bold"><?= \Core\BengaliHelper::toBengaliNumber($dir['teachers_count'] ?? 0) ?> জন</strong>
                            </div>
                            <div class="text-slate-400">
                                <span class="text-[11px]"><?= htmlspecialchars($dir['division_name']) ?> বিভাগ</span>
                            </div>
                        </div>

                        <!-- Actions / Contact Buttons -->
                        <div class="p-5 pt-0 flex items-center gap-2">
                            <a href="tel:<?= preg_replace('/[^0-9+]/', '', $dir['phone']) ?>" 
                                class="flex-1 bg-emerald-night hover:bg-emerald-deep text-white py-2 px-3 rounded-xl text-xs font-bold text-center transition flex items-center justify-center shadow-sm">
                                <i class="fas fa-phone-alt mr-1.5 text-gold-shimmer"></i> কল করুন
                            </a>

                            <?php if (!empty($dir['whatsapp'])): ?>
                                <a href="https://wa.me/88<?= preg_replace('/[^0-9]/', '', $dir['whatsapp']) ?>" target="_blank"
                                    class="bg-emerald-500 hover:bg-emerald-600 text-white p-2 rounded-xl text-xs font-bold transition flex items-center justify-center w-9 h-9" title="WhatsApp">
                                    <i class="fab fa-whatsapp text-sm"></i>
                                </a>
                            <?php endif; ?>

                            <a href="<?= $baseUrl ?>/directors/<?= $dir['slug'] ?>" 
                                class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 py-2 px-3 rounded-xl text-xs font-bold text-center transition flex items-center justify-center">
                                শিক্ষক তালিকা <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
