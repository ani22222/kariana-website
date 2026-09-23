<?php
/**
 * Admin Marketing & Integrations Settings View - Kariana Quran
 */
$success = \Core\Session::getFlash('success');
$error = \Core\Session::getFlash('error');
?>
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between pb-6 border-b border-slate-200 mb-8">
        <div>
            <a href="<?= $baseUrl ?>/admin" class="text-xs font-bold text-emerald-vibrant hover:text-emerald-night flex items-center mb-1">
                <i class="fas fa-arrow-left mr-1"></i> ড্যাশবোর্ডে ফিরে যান
            </a>
            <h1 class="text-2xl md:text-3xl font-bold text-emerald-night">মার্কেটিং ও এক্সটার্নাল ইন্টিগ্রেশন সেটিংস</h1>
            <p class="text-sm text-slate-500">Facebook Pixel, Google Search Console, Analytics ও কাস্টম স্ক্রিপ্ট পরিচালনা করুন</p>
        </div>
        <div>
            <a href="<?= $baseUrl ?>/" target="_blank" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-xl text-xs font-bold transition flex items-center">
                <i class="fas fa-external-link-alt mr-2"></i> সাইট দেখুন
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

    <form action="<?= $baseUrl ?>/admin/settings" method="POST" class="space-y-8">
        <?= $csrfField ?>

        <!-- 1. Meta / Facebook Pixel Integration -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center space-x-3 mb-4 pb-3 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                    <i class="fab fa-facebook"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Facebook Pixel ও Meta ট্র্যাকিং</h3>
                    <p class="text-xs text-slate-500">ফেসবুক অ্যাড কনভার্সন এবং ভিজিটর ইভেন্ট ট্র্যাকিং</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Facebook Pixel ID</label>
                    <input type="text" name="fb_pixel_id" value="<?= htmlspecialchars($settings['fb_pixel_id'] ?? '') ?>"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-vibrant focus:border-emerald-vibrant"
                        placeholder="যেমন: 123456789012345">
                    <p class="text-xs text-slate-400 mt-1">শুধুমাত্র আপনার মেটা পিক্সেল আইডি দিন</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">কাস্টম Facebook Pixel কোড (ঐচ্ছিক)</label>
                    <textarea name="fb_pixel_script" rows="2"
                        class="w-full font-mono text-xs px-4 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-vibrant"
                        placeholder="<!-- Meta Pixel Code --> ..."><?= htmlspecialchars($settings['fb_pixel_script'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- 2. Google Search Console & Webmaster Verification -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center space-x-3 mb-4 pb-3 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-vibrant flex items-center justify-center text-xl">
                    <i class="fab fa-google"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Google Search Console ও প্ল্যাটফর্ম ভেরিফিকেশন</h3>
                    <p class="text-xs text-slate-500">সার্চ ইঞ্জিনে সাইটের মালিকানা যাচাইয়ের মেটা ট্যাগ</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Google Site Verification Code / Meta Tag</label>
                    <input type="text" name="google_search_console_tag" value="<?= htmlspecialchars($settings['google_search_console_tag'] ?? '') ?>"
                        class="w-full font-mono text-xs px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-vibrant"
                        placeholder='<meta name="google-site-verification" content="..." /> বা কোড'>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Bing / অন্যান্য Webmaster ভেরিফিকেশন</label>
                    <input type="text" name="bing_webmaster_tag" value="<?= htmlspecialchars($settings['bing_webmaster_tag'] ?? '') ?>"
                        class="w-full font-mono text-xs px-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-vibrant"
                        placeholder='<meta name="msvalidate.01" content="..." />'>
                </div>
            </div>
        </div>

        <!-- 3. Google Analytics & Tag Manager -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center space-x-3 mb-4 pb-3 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Google Analytics (GA4) ও Google Tag Manager</h3>
                    <p class="text-xs text-slate-500">ভিজিটর স্ট্যাটিস্টিকস ও ট্র্যাকিং আইডি</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Google Analytics Measurement ID (G-XXXXXXX)</label>
                    <input type="text" name="google_analytics_id" value="<?= htmlspecialchars($settings['google_analytics_id'] ?? '') ?>"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-vibrant"
                        placeholder="G-XXXXXXXXXX">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Google Tag Manager ID (GTM-XXXXXXX)</label>
                    <input type="text" name="gtm_id" value="<?= htmlspecialchars($settings['gtm_id'] ?? '') ?>"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-vibrant"
                        placeholder="GTM-XXXXXXX">
                </div>
            </div>
        </div>

        <!-- 4. Advanced Header, Body & Footer Custom Code Injection -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center space-x-3 mb-4 pb-3 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
                    <i class="fas fa-code"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">কাস্টম কোড ও এক্সটার্নাল স্ক্রিপ্ট ইনজেকশন</h3>
                    <p class="text-xs text-slate-500">সরাসরি হেড, বডি বা ফুটারে লাইভ চ্যাট, হোয়াটসঅ্যাপ প্লাগিন বা ট্র্যাকিং কোড বসান</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">
                        Header Code (ইনজেকশন হবে: <code class="text-emerald-night bg-slate-100 px-1 py-0.5 rounded">&lt;head&gt;...&lt;/head&gt;</code> এর ভেতর)
                    </label>
                    <textarea name="custom_head_scripts" rows="3"
                        class="w-full font-mono text-xs px-4 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-vibrant"
                        placeholder="<script>...</script> বা কাস্টম মেটা ট্যাগ"><?= htmlspecialchars($settings['custom_head_scripts'] ?? '') ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">
                        Body Start Code (ইনজেকশন হবে: <code class="text-emerald-night bg-slate-100 px-1 py-0.5 rounded">&lt;body&gt;</code> শুরু হওয়ার সাথে সাথে)
                    </label>
                    <textarea name="custom_body_start_scripts" rows="2"
                        class="w-full font-mono text-xs px-4 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-vibrant"
                        placeholder="যেমন: GTM noscript কোড"><?= htmlspecialchars($settings['custom_body_start_scripts'] ?? '') ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">
                        Footer Code (ইনজেকশন হবে: <code class="text-emerald-night bg-slate-100 px-1 py-0.5 rounded">&lt;/body&gt;</code> বন্ধ হওয়ার ঠিক আগে)
                    </label>
                    <textarea name="custom_body_end_scripts" rows="3"
                        class="w-full font-mono text-xs px-4 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-vibrant"
                        placeholder="যেমন: WhatsApp Chat Widget, Tawk.to লাইভ চ্যাট স্ক্রিপ্ট ইত্যাদি"><?= htmlspecialchars($settings['custom_body_end_scripts'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- WhatsApp Gateway & Multi-Role Automation Settings -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-800 to-emerald-950 px-6 py-4 border-b border-emerald-700/50 flex items-center justify-between">
                <h3 class="text-base font-bold text-white flex items-center">
                    <i class="fab fa-whatsapp text-emerald-400 text-xl mr-2.5"></i>
                    WhatsApp গেটওয়ে ও পরিচালক/শিক্ষক অটোমেশন
                </h3>
                <span class="text-xs bg-emerald-500/30 text-emerald-200 px-3 py-1 rounded-full font-mono">/api/whatsapp/webhook</span>
            </div>
            <div class="p-6 space-y-5">
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-900 leading-relaxed">
                    <p class="font-bold mb-1"><i class="fas fa-info-circle mr-1"></i> পরিচালকদের প্রধান সামাজিক মাধ্যম কানেকশন:</p>
                    জেলা পরিচালক ও শিক্ষকগণ এই নম্বরে WhatsApp মেসেজ দিয়ে বই রিকুইজিশন, ছাত্র সংখ্যা আপডেট এবং সবক ক্লাসের তথ্য আদান-প্রদান করতে পারেন।
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">
                            WhatsApp গেটওয়ে স্ট্যাটাস
                        </label>
                        <select name="whatsapp_enabled" class="w-full text-sm px-4 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-vibrant bg-white">
                            <option value="1" <?= ($settings['whatsapp_enabled'] ?? '1') === '1' ? 'selected' : '' ?>>সক্রিয় (Active) 🟢</option>
                            <option value="0" <?= ($settings['whatsapp_enabled'] ?? '') === '0' ? 'selected' : '' ?>>নিষ্ক্রিয় (Disabled) ⚪</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">
                            অফিসিয়াল WhatsApp বট/গেটওয়ে নম্বর
                        </label>
                        <input type="text" name="whatsapp_bot_phone" 
                            value="<?= htmlspecialchars($settings['whatsapp_bot_phone'] ?? '01717056816') ?>"
                            class="w-full text-sm px-4 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-vibrant"
                            placeholder="যেমন: 01717056816">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">
                            WhatsApp Gateway API URL (Meta / Baileys / UltraMsg)
                        </label>
                        <input type="text" name="whatsapp_gateway_url" 
                            value="<?= htmlspecialchars($settings['whatsapp_gateway_url'] ?? 'http://localhost:3000/api/send') ?>"
                            class="w-full font-mono text-xs px-4 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-vibrant"
                            placeholder="http://localhost:3000/api/send">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">
                            Gateway API Token / Secret
                        </label>
                        <input type="password" name="whatsapp_api_token" 
                            value="<?= htmlspecialchars($settings['whatsapp_api_token'] ?? '') ?>"
                            class="w-full font-mono text-xs px-4 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-vibrant"
                            placeholder="Bearer Token বা Secret Key">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">
                        Webhook Verify Token (Meta Cloud API এর জন্য)
                    </label>
                    <input type="text" name="whatsapp_verify_token" 
                        value="<?= htmlspecialchars($settings['whatsapp_verify_token'] ?? 'kariana_webhook_verify_2026') ?>"
                        class="w-full font-mono text-xs px-4 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-vibrant">
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-emerald-night hover:bg-emerald-deep text-white px-8 py-3 rounded-xl font-bold shadow-lg transition flex items-center text-base">
                <i class="fas fa-save mr-2"></i> সেটিংস সংরক্ষণ করুন
            </button>
        </div>
    </form>
</div>
