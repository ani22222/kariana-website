<?php
/**
 * Super Admin - District Directors Management & SMS Hub - Kariana Quran
 */
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';
$success = \Core\Session::getFlash('success');
$error = \Core\Session::getFlash('error');
?>
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between pb-6 border-b border-slate-200 mb-8">
        <div>
            <a href="<?= $baseUrl ?>/admin" class="text-xs font-bold text-emerald-vibrant hover:text-emerald-night flex items-center mb-1">
                <i class="fas fa-arrow-left mr-1"></i> ড্যাশবোর্ডে ফিরে যান
            </a>
            <h1 class="text-2xl md:text-3xl font-bold text-emerald-night">জেলা পরিচালকবৃন্দ ও এসএমএস ব্রডকাস্ট হাব</h1>
            <p class="text-xs md:text-sm text-slate-500">সারা বাংলাদেশের ৫৯+ জেলা পরিচালক, মুয়াল্লিম শিক্ষক এবং মোবাইল এসএমএস নোটিফিকেশন ব্যবস্থাপনা</p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-3">
            <a href="<?= $baseUrl ?>/directors" target="_blank" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-xl text-xs font-bold transition flex items-center">
                <i class="fas fa-external-link-alt mr-1.5"></i> পাবলিক ডিরেক্টরি
            </a>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r text-emerald-700 text-sm mb-6">
            <i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r text-red-700 text-sm mb-6">
            <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
        
        <!-- SMS Broadcast Box -->
        <div class="lg:col-span-1 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
            <div class="flex items-center space-x-2 space-x-reverse mb-4 pb-3 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-gold-100 text-gold-deep flex items-center justify-center text-lg">
                    <i class="fas fa-sms"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">পরিচালকদের কাছে এসএমএস পাঠান</h3>
                    <p class="text-[11px] text-slate-400">মোবাইল গেটওয়ে এপিআই এর মাধ্যমে</p>
                </div>
            </div>

            <form action="<?= $baseUrl ?>/admin/directors/sms" method="POST" class="space-y-4 text-xs">
                <?= $csrfField ?>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">প্রাপক নির্বাচন করুন</label>
                    <select name="recipient_type" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-vibrant">
                        <option value="all">সারা বাংলাদেশের সকল জেলা পরিচালক (৫৯ জন)</option>
                        <option value="ঢাকা">ঢাকা বিভাগ</option>
                        <option value="চট্টগ্রাম">চট্টগ্রাম বিভাগ</option>
                        <option value="রাজশাহী">রাজশাহী বিভাগ</option>
                        <option value="খুলনা">খুলনা বিভাগ</option>
                        <option value="সিলেট">সিলেট বিভাগ</option>
                        <option value="রংপুর">রংপুর বিভাগ</option>
                        <option value="বরিশাল">বরিশাল বিভাগ</option>
                        <option value="ময়মনসিংহ">ময়মনসিংহ বিভাগ</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">বার্তা / মেসেজের বিষয়বস্তু</label>
                    <textarea name="sms_message" rows="4" required maxlength="160"
                        class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-vibrant"
                        placeholder="জরুরি মিটিং বা নির্দেশনা লিখে পাঠান (১৬০ অক্ষরের মধ্যে)..."></textarea>
                    <p class="text-[10px] text-slate-400 mt-1">সব পরিচালকদের মোবাইলে সরাসরি এসএমএস পৌঁছাবে।</p>
                </div>

                <button type="submit" 
                    class="w-full bg-emerald-night hover:bg-emerald-deep text-white py-2.5 rounded-xl font-bold transition shadow-md flex items-center justify-center">
                    <i class="fas fa-paper-plane mr-2 text-gold-shimmer"></i> বাল্ক এসএমএস পাঠান
                </button>
            </form>
        </div>

        <!-- Directors Overview Metrics -->
        <div class="lg:col-span-2 space-y-6">
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm text-center">
                    <p class="text-[11px] font-bold text-slate-400 uppercase">মোট জেলা পরিচালক</p>
                    <h3 class="text-3xl font-extrabold text-emerald-night mt-1"><?= \Core\BengaliHelper::toBengaliNumber(count($directors)) ?></h3>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm text-center">
                    <p class="text-[11px] font-bold text-slate-400 uppercase">সক্রিয় মুয়াল্লিম শিক্ষক</p>
                    <h3 class="text-3xl font-extrabold text-gold-deep mt-1"><?= \Core\BengaliHelper::toBengaliNumber($totalTeachers ?? 0) ?></h3>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm text-center">
                    <p class="text-[11px] font-bold text-slate-400 uppercase">অপেক্ষমাণ রিকোয়েস্ট</p>
                    <h3 class="text-3xl font-extrabold text-amber-600 mt-1"><?= \Core\BengaliHelper::toBengaliNumber($pendingRequests ?? 0) ?></h3>
                </div>
            </div>

            <!-- Pending Requests Quick Action -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                <h3 class="font-bold text-emerald-night text-sm mb-3">পরিচালকদের সর্বশেষ প্রাতিষ্ঠানিক রিকোয়েস্ট</h3>
                <?php if (empty($requests)): ?>
                    <p class="text-xs text-slate-400">বর্তমানে কোনো অপেক্ষমাণ আবেদন নেই।</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($requests as $req): ?>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                                <div>
                                    <span class="font-bold text-slate-800"><?= htmlspecialchars($req['director_name']) ?> (<?= htmlspecialchars($req['district_name']) ?>)</span>
                                    <p class="text-[11px] text-slate-500"><?= htmlspecialchars($req['details']) ?></p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">অপেক্ষমাণ</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- Directors List Table with Status Control -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden" x-data="{ search: '' }">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-emerald-night text-base">সকল জেলা পরিচালকের পূর্ণাঙ্গ তালিকা (৫৯ জন)</h3>
                <p class="text-xs text-slate-500">স্ট্যাটাস পরিবর্তন (কর্মরত / স্থগিত / বহিষ্কৃত) ও তথ্য নিরীক্ষণ</p>
            </div>
            <div class="w-full md:w-64">
                <input type="text" x-model="search" placeholder="নাম বা জেলা দিয়ে ফিল্টার..." 
                    class="w-full px-3 py-1.5 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-emerald-vibrant">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">ক্রমিক</th>
                        <th class="px-5 py-3">পরিচালকের নাম</th>
                        <th class="px-5 py-3">জেলা / এরিয়া</th>
                        <th class="px-5 py-3">মোবাইল</th>
                        <th class="px-5 py-3 text-center">শিক্ষক</th>
                        <th class="px-5 py-3">স্ট্যাটাস</th>
                        <th class="px-5 py-3 text-center">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($directors as $d): ?>
                        <tr x-show="search === '' || '<?= addslashes($d['name'] . ' ' . $d['district_name']) ?>'.toLowerCase().includes(search.toLowerCase())" class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3 font-mono font-bold text-slate-400"><?= sprintf('%02d', $d['id']) ?></td>
                            <td class="px-5 py-3 font-bold text-slate-800">
                                <a href="<?= $baseUrl ?>/directors/<?= $d['slug'] ?>" target="_blank" class="hover:text-emerald-night">
                                    <?= htmlspecialchars($d['name']) ?>
                                </a>
                                <?php if (!empty($d['bio']) && str_contains($d['bio'], 'মিটিং')): ?>
                                    <span class="block text-[10px] text-red-600 font-normal"><?= htmlspecialchars($d['bio']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-3 font-semibold text-emerald-night">
                                <?= htmlspecialchars($d['district_name']) ?>
                                <span class="block text-[10px] text-slate-400 font-normal"><?= htmlspecialchars($d['division_name']) ?> বিভাগ</span>
                            </td>
                            <td class="px-5 py-3 font-mono">
                                <?= htmlspecialchars($d['phone']) ?>
                            </td>
                            <td class="px-5 py-3 text-center font-bold text-gold-deep">
                                <?= \Core\BengaliHelper::toBengaliNumber($d['teachers_count'] ?? 0) ?>
                            </td>
                            <td class="px-5 py-3">
                                <?php if ($d['status'] === 'active'): ?>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">কর্মরত</span>
                                <?php elseif ($d['status'] === 'suspended'): ?>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">স্থগিত</span>
                                <?php else: ?>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-800">বহিষ্কৃত</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <form action="<?= $baseUrl ?>/admin/directors/status" method="POST" class="inline-flex">
                                        <?= $csrfField ?>
                                        <input type="hidden" name="director_id" value="<?= $d['id'] ?>">
                                        <select name="status" onchange="this.form.submit()" class="text-[10px] px-2 py-1 rounded border border-slate-200 bg-white">
                                            <option value="active" <?= $d['status'] === 'active' ? 'selected' : '' ?>>কর্মরত</option>
                                            <option value="suspended" <?= $d['status'] === 'suspended' ? 'selected' : '' ?>>স্থগিত</option>
                                            <option value="expelled" <?= $d['status'] === 'expelled' ? 'selected' : '' ?>>বহিষ্কৃত</option>
                                        </select>
                                    </form>
                                    <a href="<?= $baseUrl ?>/admin/id-card/director/<?= $d['id'] ?>" target="_blank" class="px-2 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded text-[10px] font-bold shadow-xs flex items-center gap-1" title="অফিসিয়াল আইডি কার্ড">
                                        <span>🪪</span> আইডি
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
