<?php
/**
 * Quran Web Reader Launchpad & Bridge View - Kariana Quran
 */
?>
<div class="container mx-auto px-4 py-12">
    <!-- Header Hero -->
    <div class="max-w-4xl mx-auto text-center space-y-4 mb-12">
        <span class="inline-block px-4 py-1.5 rounded-full bg-emerald-100 text-emerald-night text-xs font-bold tracking-wider">
            <svg class="w-4 h-4 text-gold-rich mr-2 inline-block shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            ডিজিটাল কুরআনুল কারীম ওয়েব রিডার
        </span>
        <h1 class="text-3xl md:text-5xl font-extrabold text-emerald-night">
            ক্ব-রিয়ানা কুরআন <span class="text-gold-deep">ওয়েব রিডার লঞ্চপ্যাড</span>
        </h1>
        <p class="text-base md:text-lg text-slate-600 max-w-2xl mx-auto leading-relaxed">
            ১২টি বিশেষ সাঙ্কেতিক চিহ্ন, বিশুদ্ধ তাজবীদ কালার কোডিং এবং কারিয়ানা নিজস্ব অফিশিয়াল আরবী ফন্টে অনলাইন কুরআন তিলাওয়াত ও অধ্যায়ন করুন।
        </p>
    </div>

    <!-- Main Quran Reader Preview Card -->
    <div class="max-w-5xl mx-auto bg-white rounded-3xl shadow-xl border border-emerald-100 overflow-hidden mb-12">
        <div class="bg-gradient-to-r from-emerald-night via-emerald-deep to-emerald-night px-6 py-4 flex items-center justify-between text-white border-b-2 border-gold-rich">
            <div class="flex items-center space-x-3">
                <span class="w-8 h-8 rounded-full bg-gold-rich text-white flex items-center justify-center font-bold text-sm">১</span>
                <div>
                    <h3 class="font-bold text-base">সূরা আল-ফাতিহা (পারা ১)</h3>
                    <p class="text-xs text-emerald-100/70">মাক্কী • আয়াত সংখ্যা: ৭</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-xs bg-emerald-800/80 px-3 py-1 rounded-full border border-emerald-600 text-emerald-100 flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1 text-gold-rich inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v18m6-9h6m-3-3v6" />
                    </svg>
                    AAR-SQ-003 ফন্ট
                </span>
            </div>
        </div>

        <div class="p-8 md:p-12 text-center space-y-8 bg-amber-50/20">
            <!-- Bismillah Calligraphy -->
            <div class="py-4">
                <p class="font-arabic text-3xl md:text-4xl text-emerald-night leading-loose">
                    بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ
                </p>
                <p class="text-sm text-slate-500 mt-2 font-medium">শুরু করছি আল্লাহর নামে যিনি পরম করুণাময়, অতি দয়ালু।</p>
            </div>

            <!-- Sample Ayahs with Bengali Translation -->
            <div class="space-y-6 max-w-3xl mx-auto text-right">
                <div class="p-4 rounded-2xl bg-white border border-slate-100 shadow-sm hover:border-emerald-200 transition">
                    <p class="font-arabic text-2xl md:text-3xl text-slate-800 leading-loose">
                        ٱلْحَمْدُ لِلَّهِ رَبِّ ٱلْعَٰلَمِينَ <span class="inline-block text-gold-deep text-lg">﴿১﴾</span>
                    </p>
                    <p class="text-left text-sm text-slate-600 mt-2 font-medium">সমস্ত প্রশংসা আল্লাহ তাআলার যিনি সমগ্র সৃষ্টির পালনকর্তা।</p>
                </div>

                <div class="p-4 rounded-2xl bg-white border border-slate-100 shadow-sm hover:border-emerald-200 transition">
                    <p class="font-arabic text-2xl md:text-3xl text-slate-800 leading-loose">
                        ٱلرَّحْمَٰنِ ٱلرَّحِيمِ <span class="inline-block text-gold-deep text-lg">﴿২﴾</span>
                    </p>
                    <p class="text-left text-sm text-slate-600 mt-2 font-medium">যিনি অত্যন্ত মেহেরবান ও পরম দয়ালু।</p>
                </div>

                <div class="p-4 rounded-2xl bg-white border border-slate-100 shadow-sm hover:border-emerald-200 transition">
                    <p class="font-arabic text-2xl md:text-3xl text-slate-800 leading-loose">
                        مَٰلِكِ يَوْمِ ٱلدِّينِ <span class="inline-block text-gold-deep text-lg">﴿৩﴾</span>
                    </p>
                    <p class="text-left text-sm text-slate-600 mt-2 font-medium">যিনি বিচার দিবসের মালিক।</p>
                </div>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-6 text-left border-t border-slate-200/60">
                <div class="flex items-start space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-night flex items-center justify-center font-bold flex-shrink-0">
                        <svg class="w-5 h-5 text-emerald-deep" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4 5 5 0 015-5c1.1 0 2.1.4 2.8 1.1L12 14.5l1.2-1.4c.7-.7 1.7-1.1 2.8-1.1a5 5 0 015 5 4 4 0 01-4 4H7z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 text-sm">তাজবীদ কালার কোডিং</h4>
                        <p class="text-xs text-slate-500">গুন্নাহ, ইখফা, ইদগাম কালার চিহ্নিত</p>
                    </div>
                </div>

                <div class="flex items-start space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-night flex items-center justify-center font-bold flex-shrink-0">
                        <svg class="w-5 h-5 text-emerald-deep" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 text-sm">১২টি সাঙ্কেতিক চিহ্ন</h4>
                        <p class="text-xs text-slate-500">সহজে পড়া ও থামার স্মার্ট নির্দেশিকা</p>
                    </div>
                </div>

                <div class="flex items-start space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-night flex items-center justify-center font-bold flex-shrink-0">
                        <svg class="w-5 h-5 text-emerald-deep" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 text-sm">সহীহ অডিও তিলাওয়াত</h4>
                        <p class="text-xs text-slate-500">আয়াতভিত্তিক অডিও শোনার ব্যবস্থা</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-slate-50 px-8 py-6 text-center border-t border-slate-100">
            <p class="text-sm text-slate-600 mb-4">পূর্ণাঙ্গ ৩০ পারা কুরআন ওয়েব রিডার ও অডিও অ্যাপ্লিকেশনটি ডেডিকেটেড সাবডোমেনে যুক্ত করা হচ্ছে।</p>
            <div class="flex flex-col sm:flex-row justify-center gap-3">
                <a href="<?= $baseUrl ?>/courses" class="bg-emerald-night hover:bg-emerald-deep text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow transition text-center">
                    কুরআন শিক্ষা কোর্সে ভর্তি হন
                </a>
                <a href="<?= $baseUrl ?>/books" class="bg-gold-rich hover:bg-gold-deep text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow transition text-center">
                    কারিয়ানা কুরআন সংগ্রহ করুন (৳১০০০)
                </a>
            </div>
        </div>
    </div>
</div>
