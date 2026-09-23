<?php
/**
 * Dedicated District Director Dashboard (Non-Technical, Clean) - Kariana Quran
 */
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';
$success = \Core\Session::getFlash('success');
$error = \Core\Session::getFlash('error');
?>
<div class="container mx-auto px-4 py-8">
    <?php if (\Core\Session::get('admin_impersonating')): ?>
        <!-- Super Admin Impersonation Control Banner -->
        <div class="bg-gradient-to-r from-amber-600 via-amber-500 to-amber-700 text-white p-4 rounded-2xl shadow-lg mb-6 flex flex-col sm:flex-row items-center justify-between gap-3 border border-amber-300">
            <div class="flex items-center space-x-3">
                <span class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-xl shrink-0">🛡️</span>
                <div>
                    <p class="font-bold text-sm text-white">সুপার এডমিন ভিউ মোড সক্রিয়</p>
                    <p class="text-xs text-amber-100">আপনি বর্তমানে <strong><?= htmlspecialchars($director['name']) ?></strong> (<?= htmlspecialchars($director['district_name']) ?>)-এর একক পরিচালক ড্যাশবোর্ড পরিচালনা করছেন।</p>
                </div>
            </div>
            <a href="<?= $baseUrl ?>/admin/directors/exit-impersonation" class="bg-white text-emerald-night hover:bg-emerald-50 px-5 py-2.5 rounded-xl text-xs font-bold transition shadow-md shrink-0 flex items-center">
                <svg class="w-4 h-4 mr-1.5 text-emerald-night" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                মূল অ্যাডমিন কন্ট্রোল প্যানেলে ফিরে যান
            </a>
        </div>
    <?php endif; ?>

    <!-- Director Header -->
    <div class="bg-gradient-to-r from-emerald-night via-emerald-deep to-emerald-night rounded-3xl p-6 md:p-8 text-white shadow-lg mb-8 flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="space-y-2 text-center md:text-right">
            <div class="flex items-center justify-center md:justify-start gap-2">
                <span class="px-3 py-1 bg-gold-rich text-white text-xs font-bold rounded-full">
                    <?= htmlspecialchars($director['designation']) ?>
                </span>
                <span class="px-3 py-1 bg-emerald-800 text-emerald-100 text-xs font-bold rounded-full border border-emerald-600">
                    <i class="fas fa-map-pin text-gold-rich mr-1"></i> <?= htmlspecialchars($director['district_name']) ?> জেলা
                </span>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-white">
                <?= htmlspecialchars($director['name']) ?>
            </h1>
            <p class="text-xs text-emerald-100/80">
                মোবাইল: <?= htmlspecialchars($director['phone']) ?> | ইউজার আইডি: <?= htmlspecialchars($director['username']) ?>
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="<?= $baseUrl ?>/directors/<?= $director['slug'] ?>" target="_blank" class="bg-white/10 hover:bg-white/20 border border-white/20 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center">
                <i class="fas fa-eye mr-1.5 text-gold-rich"></i> পাবলিক প্রোফাইল
            </a>
            <a href="<?= $baseUrl ?>/director/logout" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center shadow">
                <i class="fas fa-sign-out-alt mr-1.5"></i> প্রস্থান / লগআউট
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

    <!-- Summary Statistics Grid (Warm Ivory Cards) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-[#fffefb] p-5 rounded-2xl border border-[#e4dccb] shadow-sm text-center">
            <p class="text-xs font-bold text-slate-500 uppercase">আমার আওতাধীন শিক্ষক</p>
            <h3 class="text-3xl font-extrabold text-emerald-night mt-1"><?= \Core\BengaliHelper::toBengaliNumber(count($teachers)) ?> জন</h3>
        </div>

        <div class="bg-[#fffefb] p-5 rounded-2xl border border-[#e4dccb] shadow-sm text-center">
            <p class="text-xs font-bold text-slate-500 uppercase">জেলায় মোট শিক্ষার্থী</p>
            <?php 
            $totStudents = array_sum(array_column($teachers, 'total_students'));
            ?>
            <h3 class="text-3xl font-extrabold text-gold-deep mt-1"><?= \Core\BengaliHelper::toBengaliNumber($totStudents) ?> জন</h3>
        </div>

        <div class="bg-[#fffefb] p-5 rounded-2xl border border-[#e4dccb] shadow-sm text-center">
            <p class="text-xs font-bold text-slate-500 uppercase">বিতরণকৃত কুরআন/বই</p>
            <h3 class="text-3xl font-extrabold text-emerald-night mt-1"><?= \Core\BengaliHelper::toBengaliNumber($director['total_books_ordered'] ?? 0) ?> কপি</h3>
        </div>

        <div class="bg-[#fffefb] p-5 rounded-2xl border border-[#e4dccb] shadow-sm text-center">
            <p class="text-xs font-bold text-slate-500 uppercase">দায়িত্বপ্রাপ্ত বিভাগ</p>
            <h3 class="text-2xl font-extrabold text-slate-700 mt-1"><?= htmlspecialchars($director['division_name']) ?></h3>
        </div>
    </div>

    <!-- Operational Flow & Duties Reminder Box -->
    <div class="bg-[#f7f3e8] border border-[#e6dcce] rounded-3xl p-6 mb-8 shadow-sm">
        <div class="flex items-center space-x-3 mb-3">
            <div class="w-9 h-9 rounded-xl bg-gold-rich text-white flex items-center justify-center font-bold text-sm shadow">
                <i class="fas fa-sitemap"></i>
            </div>
            <div>
                <h3 class="font-bold text-emerald-night text-base">কারিয়ানা কুরআন সাংগঠনিক কার্যপ্রণালী ও পরিচালকের মূল দায়িত্বসমূহ</h3>
                <p class="text-xs text-slate-600">কেন্দ্রীয় কার্যালয় — জেলা পরিচালক — শিক্ষক ও শিক্ষার্থী সমন্বয় কাঠামো</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4 text-xs text-slate-700">
            <div class="bg-[#fffefb] p-3.5 rounded-xl border border-[#e5ddcb]">
                <strong class="text-emerald-night block mb-1 font-bold"><i class="fas fa-chalkboard-teacher text-gold-deep mr-1"></i> ১. শিক্ষক প্রশিক্ষণ</strong>
                নতুন শিক্ষক খুঁজে বের করে কেন্দ্রীয় ট্রেনিং সেন্টারে প্রেরণের মাধ্যমে পাঠদানের যোগ্য করে তোলা।
            </div>
            <div class="bg-[#fffefb] p-3.5 rounded-xl border border-[#e5ddcb]">
                <strong class="text-emerald-night block mb-1 font-bold"><i class="fas fa-boxes-packing text-emerald-vibrant mr-1"></i> ২. কিতাব ও সম্পদ সরবরাহ</strong>
                শিক্ষকদের ক্লাসের যাবতীয় কারিয়ানা কুরআন ও কায়দা সরাসরি কেন্দ্র থেকে এনে নির্দিষ্ট মূল্যে বিতরণ করা।
            </div>
            <div class="bg-[#fffefb] p-3.5 rounded-xl border border-[#e5ddcb]">
                <strong class="text-emerald-night block mb-1 font-bold"><i class="fas fa-hands-helping text-gold-deep mr-1"></i> ৩. সমস্যা সমাধান সাপোর্ট</strong>
                শিক্ষক বা শিক্ষার্থীদের যেকোনো পাঠদানগত বা প্রাতিষ্ঠানিক সমস্যায় পাশে থাকা ও সমাধান নিশ্চিত করা।
            </div>
            <div class="bg-[#fffefb] p-3.5 rounded-xl border border-[#e5ddcb]">
                <strong class="text-emerald-night block mb-1 font-bold"><i class="fas fa-award text-emerald-vibrant mr-1"></i> ৪. সবক ক্লাস পরিচালনা</strong>
                প্রতিটি ব্যাচের পাঠদান শেষে সমাপনী "সবক ক্লাস"-এর দিন সশরীরে উপস্থিত থেকে তা সম্পন্ন করা।
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left 2 Cols: Teachers List -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-[#fffefb] rounded-3xl border border-[#e5ddcb] shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-[#ece4d6] bg-[#f9f6ef] flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-emerald-night text-base">আমার আওতাধীন শিক্ষক ও মুয়াল্লিম তালিকা</h3>
                        <p class="text-xs text-slate-600">আপনার জেলায় কর্মরত সকল শিক্ষকের তালিকা ও ক্লাসের তথ্য</p>
                    </div>
                    <span class="text-xs font-bold bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full">
                        মোট: <?= \Core\BengaliHelper::toBengaliNumber(count($teachers)) ?>
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-[#f7f3e8] text-[11px] font-bold text-slate-600 uppercase border-b border-[#e8dfcf]">
                            <tr>
                                <th class="px-5 py-3">শিক্ষকের নাম</th>
                                <th class="px-5 py-3">মোবাইল</th>
                                <th class="px-5 py-3">এলাকা ও ক্ষেত্র</th>
                                <th class="px-5 py-3 text-center">ছাত্র-ছাত্রী</th>
                                <th class="px-5 py-3">স্ট্যাটাস</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#ece4d6]">
                            <?php if (empty($teachers)): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                        এখনও কোনো শিক্ষক নিবন্ধিত হয়নি।
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($teachers as $t): ?>
                                    <tr class="hover:bg-[#fbf8f2] transition">
                                        <td class="px-5 py-3.5 font-bold text-slate-800"><?= htmlspecialchars($t['name']) ?></td>
                                        <td class="px-5 py-3.5 font-mono"><?= htmlspecialchars($t['phone']) ?></td>
                                        <td class="px-5 py-3.5">
                                            <span class="font-medium text-slate-700"><?= htmlspecialchars($t['area_name']) ?></span>
                                            <span class="block text-[10px] text-slate-500"><?= htmlspecialchars($t['qualification'] ?? '') ?></span>
                                        </td>
                                        <td class="px-5 py-3.5 text-center font-bold text-gold-deep">
                                            <?= \Core\BengaliHelper::toBengaliNumber($t['total_students']) ?> জন
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">সক্রিয়</span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Incoming Teacher Activities & Sabak Class Schedules -->
            <div class="bg-[#fffefb] rounded-3xl border border-[#e5ddcb] shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-[#ece4d6] bg-[#f9f6ef] flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-emerald-night text-base flex items-center">
                            <i class="fas fa-calendar-check text-gold-rich mr-2"></i> মাঠপর্যায়ের শিক্ষকদের আবেদন ও সবক ক্লাস শিডিউল
                        </h3>
                        <p class="text-xs text-slate-600">শিক্ষকদের পাঠানো সবক ক্লাস আবেদন, বই চাহিদা ও সমস্যা সমাধান ব্যবস্থাপনা</p>
                    </div>
                    <span class="text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300 px-3 py-1 rounded-full">
                        মোট আবেদন: <?= \Core\BengaliHelper::toBengaliNumber(count($teacherActivities ?? [])) ?>
                    </span>
                </div>

                <div class="p-6">
                    <?php if (empty($teacherActivities)): ?>
                        <div class="text-center py-8 text-slate-400">
                            <i class="fas fa-inbox text-3xl mb-2 text-slate-300"></i>
                            <p class="text-xs">আপনার আওতাধীন কোনো শিক্ষক এখনও কোনো আবেদন বা সবক ক্লাস শিডিউল পাঠাননি।</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($teacherActivities as $ta): ?>
                                <div class="bg-[#fbf9f4] border border-[#e6decf] rounded-2xl p-4 shadow-sm hover:border-gold-rich/50 transition">
                                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-3 pb-3 border-b border-[#ece3d4]">
                                        <div>
                                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                                <?php
                                                $badgeClasses = [
                                                    'sabak_class'    => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                                    'book_order'     => 'bg-amber-100 text-amber-900 border-amber-300',
                                                    'problem_report' => 'bg-rose-100 text-rose-800 border-rose-300',
                                                    'general'        => 'bg-slate-100 text-slate-800 border-slate-300',
                                                ];
                                                $badgeLabels = [
                                                    'sabak_class'    => 'সবক ক্লাস শিডিউল',
                                                    'book_order'     => 'বই/কুরআন অর্ডার',
                                                    'problem_report' => 'সমস্যা রিপোর্ট',
                                                    'general'        => 'সাধারণ আবেদন',
                                                ];
                                                $actType = $ta['activity_type'] ?? 'general';
                                                ?>
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border <?= $badgeClasses[$actType] ?? 'bg-slate-100 text-slate-700' ?>">
                                                    <?= $badgeLabels[$actType] ?? 'আবেদন' ?>
                                                </span>

                                                <span class="text-xs font-bold text-emerald-night">
                                                    <?= htmlspecialchars($ta['teacher_name']) ?>
                                                </span>
                                                <span class="text-[11px] text-slate-500 font-mono">
                                                    (<?= htmlspecialchars($ta['teacher_phone']) ?>)
                                                </span>
                                                <span class="text-[11px] text-slate-600 bg-white px-2 py-0.5 rounded border border-[#e4dccb]">
                                                    <i class="fas fa-map-marker-alt text-gold-deep mr-1"></i> <?= htmlspecialchars($ta['area_name'] ?? '') ?>
                                                </span>
                                            </div>

                                            <h4 class="font-extrabold text-sm text-slate-800 mt-1">
                                                <?= htmlspecialchars($ta['title']) ?>
                                            </h4>
                                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                                <?= nl2br(htmlspecialchars($ta['details'])) ?>
                                            </p>

                                            <div class="flex flex-wrap items-center gap-4 mt-2 text-[11px] text-slate-500">
                                                <?php if (!empty($ta['preferred_date'])): ?>
                                                    <span class="font-semibold text-emerald-deep">
                                                        <i class="fas fa-calendar-day mr-1"></i> প্রস্তাবিত তারিখ: <?= htmlspecialchars($ta['preferred_date']) ?>
                                                    </span>
                                                <?php endif; ?>
                                                <?php if (!empty($ta['quantity'])): ?>
                                                    <span class="font-semibold text-gold-deep">
                                                        <i class="fas fa-book mr-1"></i> সংখ্যা: <?= \Core\BengaliHelper::toBengaliNumber($ta['quantity']) ?> কপি
                                                    </span>
                                                <?php endif; ?>
                                                <span>
                                                    <i class="fas fa-clock mr-1"></i> প্রাপ্তি: <?= date('d M Y, h:i A', strtotime($ta['created_at'])) ?>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="text-right flex-shrink-0">
                                            <?php
                                            $stMap = [
                                                'pending'   => ['bg-amber-100 text-amber-800 border-amber-300', 'অপেক্ষমাণ'],
                                                'approved'  => ['bg-emerald-100 text-emerald-800 border-emerald-300', 'অনুমোদিত'],
                                                'scheduled' => ['bg-blue-100 text-blue-800 border-blue-300', 'শিডিউল নিশ্চিত'],
                                                'completed' => ['bg-purple-100 text-purple-800 border-purple-300', 'সম্পন্ন'],
                                                'cancelled' => ['bg-rose-100 text-rose-800 border-rose-300', 'বাতিল'],
                                            ];
                                            $currSt = $ta['status'] ?? 'pending';
                                            [$stClass, $stLabel] = $stMap[$currSt] ?? ['bg-slate-100 text-slate-800', 'অপেক্ষমাণ'];
                                            ?>
                                            <span class="px-3 py-1 rounded-full text-xs font-bold border <?= $stClass ?>">
                                                <?= $stLabel ?>
                                            </span>
                                        </div>
                                    </div>

                                    <?php if (!empty($ta['director_notes'])): ?>
                                        <div class="mt-2 p-2.5 bg-amber-50/70 border border-amber-200/80 rounded-xl text-xs text-amber-950">
                                            <strong class="font-bold text-gold-deep"><i class="fas fa-comment-dots mr-1"></i> পরিচালকের মন্তব্য:</strong>
                                            <?= htmlspecialchars($ta['director_notes']) ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Quick Action Form for Director -->
                                    <form action="<?= $baseUrl ?>/director/activity/update" method="POST" class="mt-3 pt-3 border-t border-[#ece4d6] flex flex-wrap items-center gap-2">
                                        <?= $csrfField ?>
                                        <input type="hidden" name="activity_id" value="<?= (int)$ta['id'] ?>">

                                        <span class="text-[11px] font-bold text-slate-600">সিদ্ধান্ত হালনাগাদ:</span>
                                        <select name="status" class="text-xs px-2.5 py-1.5 rounded-lg border border-[#d8cfbe] bg-white font-medium focus:ring-1 focus:ring-emerald-vibrant">
                                            <option value="pending" <?= $currSt === 'pending' ? 'selected' : '' ?>>অপেক্ষমাণ</option>
                                            <option value="approved" <?= $currSt === 'approved' ? 'selected' : '' ?>>অনুমোদন করুন</option>
                                            <option value="scheduled" <?= $currSt === 'scheduled' ? 'selected' : '' ?>>সবক ক্লাস শিডিউল নিশ্চিত</option>
                                            <option value="completed" <?= $currSt === 'completed' ? 'selected' : '' ?>>সম্পন্ন / বিতরণকৃত</option>
                                            <option value="cancelled" <?= $currSt === 'cancelled' ? 'selected' : '' ?>>বাতিল</option>
                                        </select>

                                        <input type="text" name="director_notes" value="<?= htmlspecialchars($ta['director_notes'] ?? '') ?>" 
                                            placeholder="পরিচালকের নির্দেশ বা সময় (ঐচ্ছিক)..." 
                                            class="text-xs px-3 py-1.5 rounded-lg border border-[#d8cfbe] bg-white flex-1 min-w-[200px] focus:ring-1 focus:ring-emerald-vibrant">

                                        <button type="submit" class="bg-emerald-deep hover:bg-emerald-night text-white text-xs font-bold px-3 py-1.5 rounded-lg transition shadow-sm flex items-center">
                                            <i class="fas fa-save mr-1.5 text-gold-shimmer"></i> সংরক্ষণ
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Previous Requests Log -->
            <div class="bg-[#fffefb] rounded-3xl border border-[#e5ddcb] shadow-sm p-6">
                <h3 class="font-bold text-emerald-night text-base mb-4">আমার পূর্ববর্তী আবেদন ও রিকোয়েস্ট হিস্ট্রি</h3>
                <?php if (empty($requests)): ?>
                    <p class="text-xs text-slate-400">এখনও কোনো রিকোয়েস্ট বা আবেদন করেননি।</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($requests as $r): ?>
                            <div class="p-3.5 rounded-xl border border-[#ece3d4] bg-[#fbf8f2] flex items-center justify-between text-xs">
                                <div>
                                    <span class="font-bold text-slate-800">
                                        <?php 
                                        $types = [
                                            'book_order'      => 'কুরআন ও কায়দা কিতাব অর্ডার',
                                            'training'        => 'নতুন শিক্ষককে কেন্দ্রীয় ট্রেনিংয়ে প্রেরণ',
                                            'problem_support' => 'শিক্ষক/ছাত্র সমস্যায় কেন্দ্রীয় সহায়তা',
                                            'sabak_class'     => 'সমাপনী সবক ক্লাস শিডিউলিং',
                                            'id_card'         => 'শিক্ষকের পরিচয়পত্র ইস্যু',
                                            'general'         => 'সাধারণ আবেদন'
                                        ];
                                        echo $types[$r['request_type']] ?? 'আবেদন';
                                        ?>
                                    </span>
                                    <p class="text-slate-600 text-[11px] mt-0.5"><?= htmlspecialchars($r['details']) ?></p>
                                    <span class="text-[10px] text-slate-400"><?= date('d M Y, h:i A', strtotime($r['created_at'])) ?></span>
                                </div>
                                <div>
                                    <?php if ($r['status'] === 'approved'): ?>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">অনুমোদিত</span>
                                    <?php elseif ($r['status'] === 'rejected'): ?>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800">বাতিল</span>
                                    <?php else: ?>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">অপেক্ষমাণ</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right 1 Col: Submit Request to Head Office -->
        <div class="space-y-6">
            <div class="bg-[#fffefb] rounded-3xl border border-[#e5ddcb] shadow-sm p-6">
                <div class="flex items-center space-x-2 space-x-reverse mb-4">
                    <div class="w-8 h-8 rounded-xl bg-gold-rich/10 text-gold-deep flex items-center justify-center font-bold">
                        <i class="fas fa-paper-plane text-xs"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm">কেন্দ্রে নতুন আবেদন / অর্ডার পাঠান</h3>
                        <p class="text-[11px] text-slate-500">বই সংগ্রহ, শিক্ষক ট্রেনিং, সমস্যা সমাধান বা সবক ক্লাস</p>
                    </div>
                </div>

                <form action="<?= $baseUrl ?>/director/request" method="POST" class="space-y-4 text-xs">
                    <?= $csrfField ?>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">আবেদনের বিষয় / ক্যাটাগরি</label>
                        <select name="request_type" required class="w-full px-3 py-2.5 border border-[#e2d8c3] rounded-xl focus:ring-2 focus:ring-emerald-vibrant bg-[#fdfcf8]">
                            <option value="book_order">১. শিক্ষকদের জন্য কুরআন ও কায়েদা অর্ডার</option>
                            <option value="training">২. নতুন শিক্ষককে কেন্দ্রীয় ট্রেনিং সেন্টারে প্রেরণ</option>
                            <option value="problem_support">৩. শিক্ষক/ছাত্র সমস্যা সমাধানে কেন্দ্রীয় সহায়তা</option>
                            <option value="sabak_class">৪. কোর্সের সমাপনী 'সবক ক্লাস' শিডিউল ও সনদ</option>
                            <option value="id_card">৫. শিক্ষকের অফিশিয়াল আইডি কার্ড ইস্যু</option>
                            <option value="general">৬. অন্যান্য সাংগঠনিক বিষয়</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">পরিমাণ / সংখ্যা (বই, সনদ বা কার্ডের ক্ষেত্রে)</label>
                        <input type="number" name="quantity" min="0" value="1" 
                            class="w-full px-3 py-2 border border-[#e2d8c3] rounded-xl focus:ring-2 focus:ring-emerald-vibrant bg-[#fdfcf8]">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">বিস্তারিত বিবরণ (শিক্ষকের নাম, সমস্যার বিবরণ বা তারিখ)</label>
                        <textarea name="details" rows="4" required
                            class="w-full px-3 py-2 border border-[#e2d8c3] rounded-xl focus:ring-2 focus:ring-emerald-vibrant bg-[#fdfcf8]"
                            placeholder="সংশ্লিষ্ট শিক্ষকের নাম, মোবাইল নম্বর, ক্লাসের স্থান ও সুনির্দিষ্ট প্রয়োজনীয়তা বিস্তারিত লিখুন..."></textarea>
                    </div>

                    <button type="submit" 
                        class="w-full bg-emerald-night hover:bg-emerald-deep text-white py-2.5 rounded-xl font-bold transition shadow-md flex items-center justify-center">
                        <i class="fas fa-paper-plane mr-2 text-gold-shimmer"></i> কেন্দ্রে আবেদন জমা দিন
                    </button>
                </form>
            </div>

            <!-- Central Support Box -->
            <div class="bg-[#fbf6ea] rounded-3xl border border-[#e8ddc7] p-6 text-xs text-amber-950">
                <h4 class="font-bold text-sm mb-2 flex items-center text-amber-900">
                    <i class="fas fa-headset mr-2 text-gold-rich"></i> কেন্দ্রীয় কার্যালয় যোগাযোগ
                </h4>
                <p class="leading-relaxed text-[11px] mb-3 text-slate-700">
                    যেকোনো জরুরি সিদ্ধান্ত, কুরআন ডেলিভারি বা আর্থিক হিসাব সংক্রান্ত বিষয়ে সরাসরি প্রধান কার্যালয়ে যোগাযোগ করুন।
                </p>
                <div class="space-y-1 font-bold text-slate-800">
                    <p><i class="fas fa-phone mr-1.5 text-emerald-vibrant"></i> হটলাইন: 01700-000000</p>
                    <p><i class="fab fa-whatsapp mr-1.5 text-emerald-vibrant"></i> হোয়াটসঅ্যাপ: 01700-000000</p>
                </div>
            </div>
        </div>

    </div>
</div>
