<?php
/**
 * Digital Tasbeeh View
 */
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';
?>
<section class="max-w-xl mx-auto px-4 py-12" x-data="{ count: 0, target: 33, dhikr: 'সুবহানাল্লাহ' }">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-emerald-950 mb-2">ডিজিটাল তাসবীহ কাউন্টার</h1>
        <p class="text-slate-600 text-sm">দৈনন্দিন যিকির ও তাসবীহ গণনার আধুনিক হাতিয়ার।</p>
    </div>

    <div class="islamic-card p-8 text-center border-t-4 border-emerald-700">
        <div class="mb-4">
            <span class="text-xs uppercase font-bold text-amber-600 tracking-wider" x-text="dhikr"></span>
        </div>

        <div class="w-44 h-44 mx-auto rounded-full bg-emerald-900 border-4 border-amber-500 shadow-xl flex items-center justify-center cursor-pointer select-none active:scale-95 transition transform"
             @click="count++">
            <span class="text-5xl font-bold text-amber-400 font-mono" x-text="count"></span>
        </div>

        <div class="flex items-center justify-center gap-4 mt-8">
            <button @click="count = 0" class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200 transition">
                রিসেট
            </button>
            <button @click="dhikr = 'আলহামদুলিল্লাহ'; count = 0" class="px-3 py-2 rounded-lg bg-emerald-50 text-emerald-800 text-xs font-semibold hover:bg-emerald-100">
                আলহামদুলিল্লাহ
            </button>
            <button @click="dhikr = 'আল্লাহু আকবার'; count = 0" class="px-3 py-2 rounded-lg bg-emerald-50 text-emerald-800 text-xs font-semibold hover:bg-emerald-100">
                আল্লাহু আকবার
            </button>
        </div>
    </div>
</section>
