<?php
/**
 * Admin Books & Hero Section Featured Publications Management
 * Kariana Quran Islamic Educational Portal & CMS
 */
$user = \Core\Session::getUser();
$csrfToken = \Core\Csrf::getToken();
?>
<div class="container mx-auto px-4 py-8">
    <!-- Top Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between pb-6 border-b border-[#e5ddcb] mb-8">
        <div>
            <div class="flex items-center space-x-2">
                <a href="<?= $baseUrl ?>/admin" class="text-xs text-emerald-deep font-bold hover:underline flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1 inline-block shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    ড্যাশবোর্ড
                </a>
                <span class="text-xs text-slate-400">/</span>
                <span class="text-xs text-slate-500">বই ও প্রকাশনা</span>
            </div>
            <h1 class="text-3xl font-extrabold text-emerald-night mt-1">প্রকাশনা ও হিরো সেকশন বই ব্যবস্থাপনা</h1>
            <p class="text-sm text-slate-600">
                হোমপেজের হিরো স্লাইডারে প্রদর্শিত ১ হতে ১০টি পর্যন্ত বই নির্বাচন করুন এবং বইয়ের তথ্য ও মূল্য হালনাগাদ করুন।
            </p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-3">
            <a href="<?= $baseUrl ?>/admin/directors" class="bg-[#f5efe3] hover:bg-[#ede3d0] border border-[#d8cbba] text-emerald-night px-4 py-2.5 rounded-xl text-sm font-bold transition flex items-center">
                <svg class="w-4 h-4 mr-2 inline-block shrink-0 text-emerald-deep" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                জেলা পরিচালক হাব
            </a>
            <a href="<?= $baseUrl ?>/admin/settings" class="bg-gold-rich hover:bg-gold-deep text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow transition flex items-center">
                <svg class="w-4 h-4 mr-2 inline-block shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
                মার্কেটিং সেটিংস
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if ($success = \Core\Session::flash('success')): ?>
    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-900 font-bold flex items-center shadow-sm">
        <svg class="w-5 h-5 text-emerald-600 mr-3 inline-block shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <?= htmlspecialchars($success) ?>
    </div>
    <?php endif; ?>
    <?php if ($error = \Core\Session::flash('error')): ?>
    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-300 text-red-900 font-bold flex items-center shadow-sm">
        <svg class="w-5 h-5 text-red-600 mr-3 inline-block shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <!-- Info Box for Hero Featured Logic -->
    <div class="bg-[#f7f3e8] border-2 border-gold-rich/40 rounded-2xl p-5 mb-8 shadow-sm flex items-start space-x-4">
        <div class="w-12 h-12 rounded-xl bg-gold-rich text-white flex items-center justify-center text-xl shrink-0 shadow-md">
            <svg class="w-6 h-6 text-white inline-block shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
        </div>
        <div>
            <h3 class="font-bold text-emerald-night text-base">হিরো সেকশন স্লাইডার ডিসপ্লে নিয়ম (Dynamic 1-10+ Books)</h3>
            <p class="text-sm text-slate-700 mt-1 leading-relaxed">
                হোমপেজের মূল হিরো সেকশনে যেসকল বইয়ের <strong>"হিরো সেকশনে প্রদর্শন (Featured)"</strong> সক্রিয় থাকবে এবং <strong>সিরিয়াল নম্বর (Sort Order)</strong> অনুযায়ী সাজানো থাকবে, সেগুলো স্মুথ স্লাইডারে প্রদর্শিত হবে (সর্বোচ্চ ১০টি)। প্রতিটি বইয়ের জন্য সরাসরি অফার মূল্য ও WhatsApp অর্ডারের সুবিধা সক্রিয়।
            </p>
            <div class="mt-2 text-xs font-bold text-emerald-800">
                বর্তমানে হিরো সেকশনের জন্য ফিচার্ড বই: <span class="bg-emerald-200/80 px-2 py-0.5 rounded"><?= $featuredCount ?> টি</span>
            </div>
        </div>
    </div>

    <!-- Books Table -->
    <div class="bg-[#fffefb] rounded-2xl border border-[#e4dcce] shadow-sm overflow-hidden mb-8">
        <div class="p-5 border-b border-[#ece4d6] bg-[#fbf9f4] flex items-center justify-between">
            <h3 class="font-bold text-emerald-night text-lg flex items-center">
                <svg class="w-5 h-5 text-gold-rich mr-2 inline-block shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                সকল প্রকাশনা তালিকা (<?= count($books) ?>)
            </h3>
            <span class="text-xs text-slate-500 font-medium">হিরোতে সাজাতে সিরিয়াল নম্বর ১, ২, ৩... প্রদান করুন</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#f4efe4] border-b border-[#e5ddcd] text-xs font-extrabold text-emerald-night uppercase tracking-wider">
                        <th class="py-4 px-6">সিরিয়াল</th>
                        <th class="py-4 px-6">বইয়ের নাম ও বিবরণ</th>
                        <th class="py-4 px-6">মূল্য ও অফার</th>
                        <th class="py-4 px-6">স্টক</th>
                        <th class="py-4 px-6 text-center">হিরো সেকশন ফিচার</th>
                        <th class="py-4 px-6 text-right">কার্যক্রম</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#ece4d6] text-sm">
                    <?php foreach ($books as $b): 
                        $isFeat = (int)$b['is_featured'] === 1;
                    ?>
                    <tr class="hover:bg-[#faf7f0] transition">
                        <!-- Sort Order -->
                        <td class="py-4 px-6 font-bold text-emerald-night whitespace-nowrap">
                            <form action="<?= $baseUrl ?>/admin/books/featured" method="POST" class="flex items-center space-x-2">
                                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                <input type="hidden" name="book_id" value="<?= $b['id'] ?>">
                                <input type="hidden" name="is_featured" value="<?= $isFeat ? '1' : '0' ?>">
                                <input type="number" name="sort_order" value="<?= (int)$b['sort_order'] ?>" min="1" max="99" 
                                       class="w-16 px-2.5 py-1 text-center font-bold border border-[#d6ccb9] rounded-lg bg-white text-slate-800 text-sm focus:ring-2 focus:ring-emerald-deep focus:outline-none">
                                <button type="submit" title="সিরিয়াল সংরক্ষণ করুন" class="text-xs text-slate-500 hover:text-emerald-deep font-bold p-1">
                                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            </form>
                        </td>

                        <!-- Title & Info -->
                        <td class="py-4 px-6">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-14 bg-emerald-950 border border-gold-rich/50 rounded flex items-center justify-center text-gold-shimmer shrink-0 shadow-sm overflow-hidden">
                                    <?php if (!empty($b['cover_image'])): ?>
                                    <img src="<?= $baseUrl ?>/<?= htmlspecialchars(ltrim($b['cover_image'], '/')) ?>" alt="" class="w-full h-full object-cover" onerror="this.style.display='none';">
                                    <?php else: ?>
                                    <svg class="w-5 h-5 text-gold-shimmer" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900 text-base leading-snug">
                                        <?= htmlspecialchars($b['title']) ?>
                                    </h4>
                                    <p class="text-xs text-slate-500 mt-1 line-clamp-1">
                                        <?= htmlspecialchars($b['description'] ?? '') ?>
                                    </p>
                                    <div class="mt-1 flex items-center space-x-2 text-[11px] text-slate-400">
                                        <span>স্লাগ: /books/<?= htmlspecialchars($b['slug']) ?></span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Price & Offer -->
                        <td class="py-4 px-6 whitespace-nowrap">
                            <div class="font-mono">
                                <span class="font-bold text-emerald-night text-base">
                                    ৳<?= \Core\BengaliHelper::toBengaliNumber((int)($b['discount_price'] ?? $b['price'])) ?>
                                </span>
                                <?php if (!empty($b['discount_price']) && $b['discount_price'] < $b['price']): ?>
                                <span class="text-xs text-slate-400 line-through ml-1">
                                    ৳<?= \Core\BengaliHelper::toBengaliNumber((int)$b['price']) ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </td>

                        <!-- Stock Status -->
                        <td class="py-4 px-6 whitespace-nowrap">
                            <?php if ($b['stock_status'] === 'in_stock'): ?>
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 inline-flex items-center">
                                <span class="w-2 h-2 rounded-full bg-emerald-600 mr-1.5 inline-block"></span>
                                ইন স্টক
                            </span>
                            <?php elseif ($b['stock_status'] === 'out_of_stock'): ?>
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-red-100 text-red-800 border border-red-200">
                                স্টক আউট
                            </span>
                            <?php else: ?>
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                                প্রি-অর্ডার
                            </span>
                            <?php endif; ?>
                        </td>

                        <!-- Hero Featured Toggle -->
                        <td class="py-4 px-6 text-center whitespace-nowrap">
                            <form action="<?= $baseUrl ?>/admin/books/featured" method="POST" class="inline-block">
                                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                <input type="hidden" name="book_id" value="<?= $b['id'] ?>">
                                <input type="hidden" name="sort_order" value="<?= $b['sort_order'] ?>">
                                <?php if ($isFeat): ?>
                                <input type="hidden" name="is_featured" value="0">
                                <button type="submit" class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-100 border border-gold-rich text-amber-900 shadow-sm hover:bg-amber-200 transition flex items-center mx-auto">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-gold-deep" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    হিরো সক্রিয় (Featured)
                                </button>
                                <?php else: ?>
                                <input type="hidden" name="is_featured" value="1">
                                <button type="submit" class="px-3 py-1.5 rounded-full text-xs font-bold bg-slate-100 border border-slate-300 text-slate-600 hover:bg-slate-200 transition flex items-center mx-auto">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                    </svg>
                                    নিষ্ক্রিয় (হিরোতে দেখান)
                                </button>
                                <?php endif; ?>
                            </form>
                        </td>

                        <!-- Quick Actions -->
                        <td class="py-4 px-6 text-right whitespace-nowrap">
                            <button type="button" 
                                    onclick="document.getElementById('edit-modal-<?= $b['id'] ?>').classList.remove('hidden')"
                                    class="text-xs bg-[#f4efe4] hover:bg-[#ede5d5] border border-[#d6ccb9] text-emerald-night font-bold px-3 py-1.5 rounded-lg transition inline-flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1 text-gold-deep" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                সম্পাদনা
                            </button>

                            <!-- Quick Edit Modal -->
                            <div id="edit-modal-<?= $b['id'] ?>" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 text-left">
                                <div class="bg-[#fffefb] border-2 border-gold-rich/50 rounded-2xl max-w-md w-full p-6 shadow-2xl relative">
                                    <div class="flex items-center justify-between pb-3 border-b border-[#ede4d4] mb-4">
                                        <h3 class="font-bold text-lg text-emerald-night">বইয়ের মূল্য ও স্টক সম্পাদনা</h3>
                                        <button type="button" onclick="document.getElementById('edit-modal-<?= $b['id'] ?>').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">
                                            <svg class="w-5 h-5 text-slate-500 hover:text-slate-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    <form action="<?= $baseUrl ?>/admin/books/update" method="POST" class="space-y-4">
                                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                        <input type="hidden" name="book_id" value="<?= $b['id'] ?>">

                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">বইয়ের নাম</label>
                                            <input type="text" value="<?= htmlspecialchars($b['title']) ?>" disabled class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-lg text-sm text-slate-600 font-semibold">
                                        </div>

                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 mb-1">নিয়মিত মূল্য (৳)</label>
                                                <input type="number" step="1" name="price" value="<?= (int)$b['price'] ?>" required class="w-full px-3 py-2 border border-[#d6ccb9] rounded-lg text-sm font-bold focus:ring-2 focus:ring-emerald-deep focus:outline-none">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 mb-1">অফার/ছাড়ের মূল্য (৳)</label>
                                                <input type="number" step="1" name="discount_price" value="<?= (int)($b['discount_price'] ?? $b['price']) ?>" class="w-full px-3 py-2 border border-[#d6ccb9] rounded-lg text-sm font-bold focus:ring-2 focus:ring-emerald-deep focus:outline-none">
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">স্টক অবস্থা</label>
                                            <select name="stock_status" class="w-full px-3 py-2 border border-[#d6ccb9] rounded-lg text-sm font-semibold focus:ring-2 focus:ring-emerald-deep focus:outline-none">
                                                <option value="in_stock" <?= $b['stock_status'] === 'in_stock' ? 'selected' : '' ?>>ইন স্টক (In Stock)</option>
                                                <option value="out_of_stock" <?= $b['stock_status'] === 'out_of_stock' ? 'selected' : '' ?>>স্টক শেষ (Out of Stock)</option>
                                                <option value="pre_order" <?= $b['stock_status'] === 'pre_order' ? 'selected' : '' ?>>প্রি-অর্ডার (Pre-order)</option>
                                            </select>
                                        </div>

                                        <div class="pt-3 border-t border-[#ede4d4] flex justify-end space-x-2">
                                            <button type="button" onclick="document.getElementById('edit-modal-<?= $b['id'] ?>').classList.add('hidden')" class="px-4 py-2 border border-slate-300 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-100">
                                                বাতিল
                                            </button>
                                            <button type="submit" class="px-5 py-2 bg-emerald-deep hover:bg-emerald-night text-white rounded-lg text-xs font-bold shadow transition">
                                                সংরক্ষণ করুন
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
