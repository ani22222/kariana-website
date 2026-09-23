<!DOCTYPE html>
<html lang="bn" dir="ltr" translate="no" class="notranslate">
<head>
    <meta charset="UTF-8">
    <meta name="google" content="notranslate">
    <meta http-equiv="Content-Language" content="bn">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title><?= isset($title) ? htmlspecialchars($title) : 'কারিয়ানা কুরআন শিক্ষা সোসাইটি' ?></title>
    
    <!-- Anti-Flash Dark Mode Script (Immediately applies or removes .dark before DOM renders) -->
    <script>
        (function() {
            try {
                var savedTheme = localStorage.getItem('kariana_theme');
                var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {}
        })();
    </script>
    
    <!-- Enterprise Global Maximum-Level SEO Stack (Schema.org JSON-LD, OpenGraph, Twitter Cards, Canonical) -->
    <?= \Core\SeoHelper::renderHeadStack([
        'title'       => $title ?? null,
        'description' => $description ?? null,
        'keywords'    => $keywords ?? null,
        'canonical'   => $canonical ?? null,
        'schema'      => $schemaJsonLd ?? null,
    ]) ?>

    <!-- PWA & Mobile Web App Meta Tags -->
    <link rel="manifest" href="<?= $baseUrl ?? '' ?>/manifest.json">
    <meta name="theme-color" content="#022c22">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="কারিয়ানা">
    <link rel="apple-touch-icon" href="<?= $baseUrl ?? '' ?>/assets/images/icon-192.png">

    <!-- Dynamic Marketing & External Integrations Hub -->
    <?php
    $mkt = [];
    try {
        $dbRows = \Core\Database::getInstance()->query("SELECT `setting_key`, `setting_value` FROM `site_settings` WHERE `group_name` = 'marketing'")->fetchAll();
        foreach ($dbRows as $r) {
            $mkt[$r['setting_key']] = $r['setting_value'];
        }
    } catch (\Throwable $e) {}
    ?>

    <!-- Facebook Pixel -->
    <?php if (!empty($mkt['fb_pixel_id'])): ?>
    <script>
      !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
      n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
      n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
      t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
      document,'script','https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', '<?= htmlspecialchars($mkt['fb_pixel_id']) ?>');
      fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?= htmlspecialchars($mkt['fb_pixel_id']) ?>&ev=PageView&noscript=1"/></noscript>
    <?php endif; ?>
    <?php if (!empty($mkt['fb_pixel_script'])): ?>
        <?= $mkt['fb_pixel_script'] ?>
    <?php endif; ?>

    <!-- Google Search Console & Webmaster Verification -->
    <?php if (!empty($mkt['google_search_console_tag'])): ?>
        <?= $mkt['google_search_console_tag'] ?>
    <?php endif; ?>
    <?php if (!empty($mkt['bing_webmaster_tag'])): ?>
        <?= $mkt['bing_webmaster_tag'] ?>
    <?php endif; ?>

    <!-- Google Analytics (GA4) -->
    <?php if (!empty($mkt['google_analytics_id'])): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($mkt['google_analytics_id']) ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '<?= htmlspecialchars($mkt['google_analytics_id']) ?>');
    </script>
    <?php endif; ?>

    <!-- Custom Header Scripts -->
    <?php if (!empty($mkt['custom_head_scripts'])): ?>
        <?= $mkt['custom_head_scripts'] ?>
    <?php endif; ?>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js Core for Reactive UI & Dropdowns -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        emerald: {
                            deep: '#064e3b',
                            vibrant: '#047857',
                            light: '#ecfdf5',
                            night: '#022c22'
                        },
                        gold: {
                            rich: '#d97706',
                            deep: '#b45309',
                            shimmer: '#fbbf24'
                        },
                        parchment: {
                            DEFAULT: '#f8f5ee',
                            warm: '#f3eee3',
                            card: '#fffefb'
                        },
                        borderSoft: '#e7dec6'
                    },
                    fontFamily: {
                        sans: ['Hind Siliguri', 'sans-serif'],
                        arabic: ['Amiri', 'serif'],
                        kariana: ['KarianaQuran', 'Amiri', 'serif']
                    }
                }
            }
        }
    </script>
    
    <!-- Custom CSS with Dynamic Cache-Buster -->
    <link rel="stylesheet" href="<?= $baseUrl ?? '' ?>/assets/css/main.css?v=<?= time() ?>">
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Enforce Royal Islamic Emerald & Gold Capsule Dock on Mobile */
        .bottom-nav-wrapper {
            position: fixed !important;
            bottom: 8px !important;
            left: 0 !important;
            right: 0 !important;
            padding: 0 12px !important;
            z-index: 50 !important;
            pointer-events: none !important;
            display: flex !important;
            justify-content: center !important;
        }
        .bottom-nav {
            pointer-events: auto !important;
            width: 100% !important;
            max-width: 440px !important;
            background: linear-gradient(135deg, rgba(3, 44, 34, 0.98) 0%, rgba(1, 26, 20, 0.99) 100%) !important;
            backdrop-filter: blur(20px) !important;
            -webkit-backdrop-filter: blur(20px) !important;
            border-radius: 26px !important;
            border: 1.5px solid rgba(251, 191, 36, 0.5) !important;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.65), 0 0 16px rgba(251, 191, 36, 0.2), inset 0 1px 1px rgba(255, 255, 255, 0.15) !important;
            padding: 0.45rem 0.55rem 0.2rem !important;
            padding-bottom: calc(0.2rem + env(safe-area-inset-bottom, 0px)) !important;
        }
        @media (min-width: 768px) {
            .bottom-nav-wrapper {
                display: none !important;
            }
        }

        /* ============================================================
           ROYAL ISLAMIC 100% CONTRAST PALETTE (LIGHT & DARK DUAL ENGINE)
           ============================================================ */

        /* 1. LIGHT MODE SPECIFICATION (Clean, Warm Ivory & Regal Accents) */
        html:not(.dark) body {
            background-color: #f8f5ee !important;
            color: #1e293b !important;
        }
        html:not(.dark) .islamic-card,
        html:not(.dark) .bg-white {
            background-color: #ffffff !important;
            color: #1e293b !important;
            border-color: #e2e8f0;
        }
        html:not(.dark) input,
        html:not(.dark) select,
        html:not(.dark) textarea {
            background-color: #ffffff !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }

        /* 2. DARK MODE SPECIFICATION (Pure Deep Obsidian Emerald & Royal Gold — Zero Light Patches Anywhere) */
        html.dark,
        html.dark body {
            background-color: #02150f !important;
            color: #f8fafc !important;
        }

        /* Eradicate any light linear gradients & parchment section backdrops */
        html.dark #mainContentSection,
        html.dark .bg-gradient-to-b,
        html.dark [class*="from-[#fbf8f1]"],
        html.dark [class*="via-[#fffdf9]"],
        html.dark [class*="to-[#f6f1e5]"],
        html.dark section.bg-\[\#f4efe4\]\/70,
        html.dark section.bg-\[\#edf4ee\],
        html.dark section.bg-\[\#f6f1e7\],
        html.dark section.bg-\[\#f8f5ee\],
        html.dark .bg-parchment,
        html.dark .bg-parchment-warm {
            background-image: none !important;
            background-color: #02150f !important;
            border-color: #0a3d2e !important;
        }

        /* Alternating dark section backdrops for smooth contrast rhythm */
        html.dark section.bg-\[\#edf4ee\],
        html.dark section.bg-\[\#f8f5ee\] {
            background-color: #031c15 !important;
        }

        /* All Cards, Boxes, Modals, Dropdowns & Container Surfaces */
        html.dark .bg-white,
        html.dark .bg-\[\#fbf8f1\],
        html.dark .bg-\[\#fffdf9\],
        html.dark .bg-\[\#fffefb\],
        html.dark .bg-\[\#fdfcf8\],
        html.dark .bg-\[\#fdfbf7\],
        html.dark .bg-\[\#f6f1e5\],
        html.dark .bg-\[\#f4efe4\],
        html.dark .bg-\[\#f6f1e7\],
        html.dark .bg-slate-50,
        html.dark .bg-gray-50,
        html.dark .islamic-card,
        html.dark .card,
        html.dark article {
            background-color: #062c20 !important;
            color: #f8fafc !important;
            border-color: #0e4837 !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6) !important;
        }

        /* Sub-surfaces, feature boxes, and inner row items */
        html.dark .bg-\[\#f7f9f7\],
        html.dark .bg-\[\#f7f4ea\],
        html.dark .bg-\[\#f9fbf9\],
        html.dark .bg-\[\#faf7f0\],
        html.dark .bg-emerald-50,
        html.dark .bg-amber-50,
        html.dark .bg-emerald-50\/70,
        html.dark .bg-amber-50\/70,
        html.dark .bg-emerald-100,
        html.dark .bg-amber-100 {
            background-color: #031e16 !important;
            color: #e2e8f0 !important;
            border-color: #0e4837 !important;
        }

        /* Dark Mode Icon Badges */
        html.dark .bg-emerald-100\/80,
        html.dark .bg-amber-100\/80 {
            background-color: #083829 !important;
            color: #fbbf24 !important;
        }

        /* Card Hover States in Dark Mode */
        html.dark a.group:hover,
        html.dark .islamic-card:hover,
        html.dark article:hover {
            background-color: #083c2d !important;
            border-color: rgba(251, 191, 36, 0.5) !important;
        }

        /* Border Overrides in Dark Mode */
        html.dark [class*="border-[#e"],
        html.dark [class*="border-[#d"],
        html.dark .border-slate-100,
        html.dark .border-slate-200,
        html.dark .border-slate-300,
        html.dark .border-gray-200,
        html.dark .border-emerald-100,
        html.dark .border-emerald-200,
        html.dark .border-emerald-300,
        html.dark .divide-\[\#ece3d4\] > * + *,
        html.dark .divide-\[\#e4ebe5\] > * + * {
            border-color: #0a3d2e !important;
        }

        /* High-Contrast Typography in Dark Mode (Warm Golds, Mint & Crisp Silvers) */
        html.dark .text-emerald-night,
        html.dark .text-emerald-950,
        html.dark .text-emerald-900,
        html.dark .text-slate-900,
        html.dark .text-slate-800,
        html.dark .text-gray-900,
        html.dark .text-gray-800,
        html.dark .text-\[\#064e3b\] {
            color: #fef3c7 !important; /* Golden/cream glow for titles */
        }

        html.dark .text-emerald-800,
        html.dark .text-emerald-700,
        html.dark .text-emerald-vibrant {
            color: #6ee7b7 !important; /* Mint emerald */
        }

        html.dark .text-slate-700,
        html.dark .text-slate-600,
        html.dark .text-gray-700,
        html.dark .text-gray-600 {
            color: #cbd5e1 !important; /* Crisp silver */
        }

        html.dark .text-slate-500,
        html.dark .text-gray-500,
        html.dark .text-slate-400 {
            color: #94a3b8 !important; /* Soft legible slate */
        }

        html.dark .text-amber-800,
        html.dark .text-amber-900,
        html.dark .text-amber-950,
        html.dark .text-gold-deep {
            color: #fbbf24 !important; /* Shimmer gold */
        }

        /* Form Controls & Inputs */
        html.dark input,
        html.dark select,
        html.dark textarea {
            background-color: #022017 !important;
            color: #f8fafc !important;
            border-color: #0a3d2e !important;
        }

        html.dark input::placeholder,
        html.dark textarea::placeholder {
            color: #64748b !important;
        }

        html.dark input:focus,
        html.dark select:focus,
        html.dark textarea:focus {
            border-color: #fbbf24 !important;
            box-shadow: 0 0 0 2px rgba(251, 191, 36, 0.25) !important;
        }

        /* Table Elements */
        html.dark thead,
        html.dark thead th {
            background-color: #04271c !important;
            color: #fef3c7 !important;
            border-color: #0a3d2e !important;
        }

        html.dark tbody td {
            border-color: #0a3d2e !important;
            color: #f1f5f9 !important;
        }

        html.dark tbody tr:hover {
            background-color: #083428 !important;
        }

        /* Pills, Badges & Tickers */
        html.dark .bg-emerald-100 {
            background-color: rgba(6, 78, 59, 0.6) !important;
            color: #a7f3d0 !important;
        }

        html.dark .bg-amber-100 {
            background-color: rgba(180, 83, 9, 0.3) !important;
            color: #fde68a !important;
        }
    </style>
</head>
<body class="bg-[#f8f5ee] text-slate-800 antialiased flex flex-col min-h-screen">
    <!-- Dynamic GTM / Custom Body Start Scripts -->
    <?php if (!empty($mkt['gtm_id'])): ?>
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= htmlspecialchars($mkt['gtm_id']) ?>"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <?php endif; ?>
    <?php if (!empty($mkt['custom_body_start_scripts'])): ?>
        <?= $mkt['custom_body_start_scripts'] ?>
    <?php endif; ?>

    <!-- Top Breaking News / Prayer Ticker -->
    <?php
    $authLabel = 'প্রবেশ / লগইন';
    $authUrl = ($baseUrl ?? '') . '/login';
    $authRoleBadge = '';
    $isAuthed = false;

    if (\Core\Session::isLoggedIn()) {
        $currUser = \Core\Session::getUser();
        $isAuthed = true;
        if (($currUser['role'] ?? '') === 'admin') {
            $authLabel = 'অ্যাডমিন কন্ট্রোল';
            $authUrl = ($baseUrl ?? '') . '/admin';
            $authRoleBadge = 'এডমিন';
        } else {
            $authLabel = htmlspecialchars($currUser['name'] ?? 'শিক্ষার্থী প্রোফাইল');
            $authUrl = ($baseUrl ?? '') . '/profile';
            $authRoleBadge = 'শিক্ষার্থী';
        }
    } elseif (\Core\Session::has('director_id')) {
        $isAuthed = true;
        $authLabel = htmlspecialchars(\Core\Session::get('director_name') ?? 'পরিচালক ড্যাশবোর্ড');
        $authUrl = ($baseUrl ?? '') . '/director/dashboard';
        $authRoleBadge = 'পরিচালক';
    } elseif (\Core\Session::has('teacher')) {
        $isAuthed = true;
        $tSess = \Core\Session::get('teacher');
        $authLabel = htmlspecialchars($tSess['name'] ?? 'শিক্ষক ড্যাশবোর্ড');
        $authUrl = ($baseUrl ?? '') . '/teacher/dashboard';
        $authRoleBadge = 'মুয়াল্লিম';
    } elseif (\Core\Session::has('manager')) {
        $isAuthed = true;
        $authLabel = 'ম্যানেজার ডেস্ক';
        $authUrl = ($baseUrl ?? '') . '/manager/dashboard';
        $authRoleBadge = 'ব্যবস্থাপক';
    }
    ?>
    <!-- Real-time Offline / Online Network Alert Bar -->
    <div id="networkAlertBar" class="hidden text-xs py-1.5 px-3 text-center font-bold z-50 transition-all duration-300"></div>

    <!-- Comprehensive Multi-Platform Kariana Application & Portal Hub Modal -->
    <div id="pwaGuideModal" class="hidden fixed inset-0 z-[100] bg-black/85 backdrop-blur-md flex items-center justify-center p-3 sm:p-4">
        <div class="bg-gradient-to-b from-[#022c22] via-[#04392b] to-[#011a14] border-2 border-amber-400/80 rounded-3xl p-5 sm:p-7 max-w-xl w-full text-white shadow-2xl relative">
            <!-- Modal Close Button -->
            <button id="closeGuideModalBtn" type="button" class="absolute top-4 right-4 text-slate-300 hover:text-amber-300 p-1.5 rounded-full hover:bg-white/10 transition" title="বন্ধ করুন">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <!-- Header Section -->
            <div class="text-center mb-5">
                <div class="inline-flex items-center justify-center w-13 h-13 rounded-2xl bg-amber-400/20 border border-amber-400/50 text-amber-300 mb-2.5 shadow-inner">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2zM9 3h6M4 6h16" />
                    </svg>
                </div>
                <h3 class="text-xl sm:text-2xl font-black text-amber-300 tracking-tight">
                    কারিয়ানা ডিজিটাল অ্যাপ্লিকেশন হাব
                </h3>
                <p class="text-xs sm:text-sm text-emerald-200/90 mt-1 max-w-md mx-auto">
                    আপনার ডিভাইসের জন্য উপযুক্ত সংস্করণটি নির্বাচন করুন — মোবাইল, কম্পিউটার বা সরাসরি ওয়েব
                </p>
            </div>

            <!-- Platform Switcher Tabs -->
            <div class="flex items-center p-1 bg-emerald-950/80 rounded-2xl border border-amber-400/30 mb-5 gap-1">
                <button type="button" id="tabBtnMobile" onclick="switchAppHubTab('mobile')" 
                        class="app-hub-tab flex-1 py-2 sm:py-2.5 px-2 rounded-xl text-xs sm:text-sm font-black transition flex items-center justify-center gap-1.5 bg-amber-400 text-emerald-950 shadow">
                    <span>📱</span>
                    <span>মোবাইল অ্যাপ</span>
                </button>
                <button type="button" id="tabBtnDesktop" onclick="switchAppHubTab('desktop')" 
                        class="app-hub-tab flex-1 py-2 sm:py-2.5 px-2 rounded-xl text-xs sm:text-sm font-bold text-slate-300 hover:text-white transition flex items-center justify-center gap-1.5">
                    <span>💻</span>
                    <span>কম্পিউটার ও পিসি</span>
                </button>
                <button type="button" id="tabBtnWeb" onclick="switchAppHubTab('web')" 
                        class="app-hub-tab flex-1 py-2 sm:py-2.5 px-2 rounded-xl text-xs sm:text-sm font-bold text-slate-300 hover:text-white transition flex items-center justify-center gap-1.5">
                    <span>🌐</span>
                    <span>ওয়েব পোর্টাল</span>
                </button>
            </div>

            <!-- TAB 1: MOBILE APPS (Android APK & PWA) -->
            <div id="tabContentMobile" class="space-y-3.5">
                <!-- Option A: Instant PWA Mobile App -->
                <div class="p-3.5 sm:p-4 rounded-2xl bg-emerald-900/60 border border-emerald-600/40 hover:border-amber-400/60 transition flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left">
                    <div class="space-y-1">
                        <div class="flex items-center justify-center sm:justify-start gap-2">
                            <span class="text-xs font-black text-amber-300 uppercase tracking-wide">১-ক্লিক মোবাইল ওয়েব অ্যাপ</span>
                            <span class="text-[10px] bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 px-1.5 py-0.2 rounded-full font-bold">ইনস্ট্যান্ট</span>
                        </div>
                        <p class="text-xs text-slate-200">কোনো ডাউনলোড ছাড়াই ফোনে অ্যাপের মতো চালু হবে, অফলাইনে কুরআন ও নামাজের সময় পাওয়া যাবে।</p>
                    </div>
                    <button type="button" onclick="triggerPwaPromptDirectly()" class="w-full sm:w-auto shrink-0 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-bold py-2.5 px-4 rounded-xl shadow text-xs flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>ফোনে ইনস্টল</span>
                    </button>
                </div>

                <!-- Option B: Direct Android APK Download -->
                <div class="p-3.5 sm:p-4 rounded-2xl bg-emerald-950/80 border border-amber-400/40 hover:border-amber-400 transition flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left">
                    <div class="space-y-1">
                        <div class="flex items-center justify-center sm:justify-start gap-2">
                            <span class="text-xs font-black text-amber-300 uppercase tracking-wide">অ্যান্ড্রয়েড সরাসরি ইনস্টলার (.APK)</span>
                            <span class="text-[10px] bg-amber-400/20 text-amber-300 border border-amber-400/40 px-1.5 py-0.2 rounded-full font-bold">v1.0 APK</span>
                        </div>
                        <p class="text-xs text-slate-200">যেকোনো স্মার্টফোনে সরাসরি ফাইল ডাউনলোড করে ইনস্টল করার জন্য প্যাকেজ ফাইল।</p>
                    </div>
                    <a href="<?= $baseUrl ?? '' ?>/download/apk" download="kariana-quran-v1.0.apk" class="w-full sm:w-auto shrink-0 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-emerald-950 font-black py-2.5 px-4 rounded-xl shadow text-xs flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>APK ডাউনলোড</span>
                    </a>
                </div>

                <!-- Option C: iPhone / iPad iOS Quick Instructions -->
                <div class="p-3 rounded-xl bg-black/30 border border-white/10 text-left text-[11px] text-slate-300">
                    <p class="font-bold text-amber-300 mb-1 flex items-center gap-1">
                        <span>🍎 আইফোন / আইপ্যাড (Safari) ব্যবহারকারীরা:</span>
                    </p>
                    <p>নিচের শেয়ার আইকনে ( <strong class="text-white">Share</strong> ) চাপুন ➔ এরপর <strong class="text-amber-300">"Add to Home Screen"</strong> চাপলেই ফোনের হোমস্ক্রিনে কারিয়ানা অ্যাপ যুক্ত হয়ে যাবে।</p>
                </div>
            </div>

            <!-- TAB 2: DESKTOP & PC (Windows .EXE & Chrome App) -->
            <div id="tabContentDesktop" class="space-y-3.5 hidden">
                <!-- Option A: Windows .EXE Desktop Launcher -->
                <div class="p-3.5 sm:p-4 rounded-2xl bg-emerald-950/80 border border-amber-400/40 hover:border-amber-400 transition flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left">
                    <div class="space-y-1">
                        <div class="flex items-center justify-center sm:justify-start gap-2">
                            <span class="text-xs font-black text-amber-300 uppercase tracking-wide">উইন্ডোজ ডেস্কটপ অ্যাপ্লিকেশন (.EXE)</span>
                            <span class="text-[10px] bg-amber-400/20 text-amber-300 border border-amber-400/40 px-1.5 py-0.2 rounded-full font-bold">Windows 10/11</span>
                        </div>
                        <p class="text-xs text-slate-200">কম্পিউটারে বড় স্ক্রিনে কুরআন ও প্রকাশনা পরিচালনার জন্য ডেডিকেটেড ডেস্কটপ সফটওয়্যার সেটআপ।</p>
                    </div>
                    <a href="<?= $baseUrl ?? '' ?>/download/exe" download="Kariana-Quran-Setup-v1.0.exe" class="w-full sm:w-auto shrink-0 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-emerald-950 font-black py-2.5 px-4 rounded-xl shadow text-xs flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span>Windows .EXE ডাউনলোড</span>
                    </a>
                </div>

                <!-- Option B: Desktop Web App (Chrome / Edge PWA) -->
                <div class="p-3.5 sm:p-4 rounded-2xl bg-emerald-900/60 border border-emerald-600/40 hover:border-amber-400/60 transition flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left">
                    <div class="space-y-1">
                        <div class="flex items-center justify-center sm:justify-start gap-2">
                            <span class="text-xs font-black text-amber-300 uppercase tracking-wide">ক্রোম ও এজ ডেস্কটপ অ্যাপ</span>
                            <span class="text-[10px] bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 px-1.5 py-0.2 rounded-full font-bold">স্টার্ট মেনু ও টাস্কবার</span>
                        </div>
                        <p class="text-xs text-slate-200">কম্পিউটারের ব্রাউজার থেকে সরাসরি উইন্ডোজ অ্যাপ হিসেবে ইনস্টল করুন ও টাস্কবারে পিন করে রাখুন।</p>
                    </div>
                    <button type="button" onclick="triggerPwaPromptDirectly()" class="w-full sm:w-auto shrink-0 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-bold py-2.5 px-4 rounded-xl shadow text-xs flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>পিসিতে অ্যাপ যুক্ত করুন</span>
                    </button>
                </div>
            </div>

            <!-- TAB 3: DIRECT WEB & ADMIN PORTALS -->
            <div id="tabContentWeb" class="space-y-2.5 hidden">
                <p class="text-xs text-slate-300 text-center mb-3">যেকোনো ব্রাউজার থেকে সরাসরি সংশ্লিষ্ট প্রশাসনিক প্যানেলে প্রবেশ করুন:</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <a href="<?= $baseUrl ?? '' ?>/admin" target="_blank" class="p-3 rounded-xl bg-emerald-900/70 hover:bg-emerald-800 border border-amber-400/30 hover:border-amber-400 transition flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-amber-400/20 text-amber-300 flex items-center justify-center shrink-0">
                            👑
                        </div>
                        <div class="text-left">
                            <h4 class="font-bold text-white text-xs">সুপার অ্যাডমিন ড্যাশবোর্ড</h4>
                            <p class="text-[10px] text-emerald-200/80">কেন্দ্রীয় প্রকাশনা ও নিয়ন্ত্রণ</p>
                        </div>
                    </a>

                    <a href="<?= $baseUrl ?? '' ?>/manager/login" target="_blank" class="p-3 rounded-xl bg-emerald-900/70 hover:bg-emerald-800 border border-amber-400/30 hover:border-amber-400 transition flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-amber-400/20 text-amber-300 flex items-center justify-center shrink-0">
                            👔
                        </div>
                        <div class="text-left">
                            <h4 class="font-bold text-white text-xs">ম্যানেজার ও পরিচালক পোর্টাল</h4>
                            <p class="text-[10px] text-emerald-200/80">আঞ্চলিক কার্যক্রম ও লেজার</p>
                        </div>
                    </a>

                    <a href="<?= $baseUrl ?? '' ?>/teacher/login" target="_blank" class="p-3 rounded-xl bg-emerald-900/70 hover:bg-emerald-800 border border-amber-400/30 hover:border-amber-400 transition flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-amber-400/20 text-amber-300 flex items-center justify-center shrink-0">
                            👨‍🏫
                        </div>
                        <div class="text-left">
                            <h4 class="font-bold text-white text-xs">মুয়াল্লিম ও শিক্ষক প্যানেল</h4>
                            <p class="text-[10px] text-emerald-200/80">শিক্ষক ক্লাসরুম ও উপস্থিতি</p>
                        </div>
                    </a>

                    <a href="<?= $baseUrl ?? '' ?>/verify/kyc" target="_blank" class="p-3 rounded-xl bg-emerald-900/70 hover:bg-emerald-800 border border-amber-400/30 hover:border-amber-400 transition flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-amber-400/20 text-amber-300 flex items-center justify-center shrink-0">
                            🪪
                        </div>
                        <div class="text-left">
                            <h4 class="font-bold text-white text-xs">সদস্য ও ছাত্র আইডি যাচাই</h4>
                            <p class="text-[10px] text-emerald-200/80">ডিজিটাল সনদ ও আইডি কার্ড</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="mt-5 pt-3 border-t border-emerald-800/60 flex items-center justify-between text-[11px] text-emerald-200/70">
                <span>কারিয়ানা কুরআন অফিসিয়াল সিস্টেম</span>
                <button type="button" id="gotItGuideBtn" class="text-amber-300 hover:text-white font-bold transition">বন্ধ করুন ✕</button>
            </div>
        </div>
    </div>

    <div id="topTickerBar" class="bg-emerald-night text-white text-xs sm:text-sm py-1 sm:py-1.5 px-3 sm:px-4 border-b border-gold-deep/30 flex items-center h-[26px] sm:h-auto overflow-hidden">
        <div class="container mx-auto flex items-center justify-between">
            <div class="flex items-center space-x-2 space-x-reverse overflow-hidden whitespace-nowrap">
                <span class="bg-gold-rich text-white px-2 py-0.5 rounded text-[11px] font-bold shrink-0 animate-pulse">আপডেট</span>
                <marquee scrollamount="4" class="text-emerald-light ml-3 text-[12px] sm:text-[13px]">
                    আজকের নামাজের সময়সূচি: ফজর - ৪:৪০ | যোহর - ১২:১৫ | আসর - ৪:৩০ | মাগরিব - ৬:১০ | ইশা - ৭:৩০ (ঢাকা) &nbsp;&nbsp;|&nbsp;&nbsp; কারিয়ানা কুরআন বাংলা অর্থসহ - স্পেশাল অফার ৳১০০০
                </marquee>
            </div>
            <div class="hidden md:flex items-center space-x-4 text-xs">
                <span class="text-amber-300 flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1 text-amber-300" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                    </svg>
                    ১৪ রবিউল আউয়াল, ১৪৪৬ হিজরী
                </span>
                <span class="text-emerald-600">|</span>
                <a href="<?= $authUrl ?>" 
                   class="text-amber-300 hover:text-white font-bold flex items-center bg-emerald-900/80 px-3 py-1 rounded-full border border-gold-rich/40 transition text-xs shadow-sm">
                    <svg class="w-3.5 h-3.5 mr-1.5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <?php if ($authRoleBadge): ?>
                        <span class="px-1.5 py-0.2 bg-gold-rich text-white text-[9px] font-bold rounded mr-1.5"><?= $authRoleBadge ?></span>
                    <?php endif; ?>
                    <?= $authLabel ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header id="mainHeaderBar" class="bg-emerald-deep islamic-pattern sticky top-0 z-40 shadow-lg border-b-2 border-gold-rich">
        <div class="container mx-auto px-3 sm:px-4 py-1.5 sm:py-3">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <a href="<?= $baseUrl ?? '' ?>/" class="flex items-center group">
                    <div class="w-9 h-9 sm:w-12 sm:h-12 bg-white rounded-full flex items-center justify-center p-1 border-2 border-gold-shimmer shadow-[0_0_15px_rgba(251,191,36,0.3)] transition transform group-hover:scale-105">
                        <img src="<?= $baseUrl ?? '' ?>/assets/images/logo.png" alt="Logo" class="w-full h-full object-contain rounded-full" onerror="this.src='https://ui-avatars.com/api/?name=Kariana&background=064e3b&color=fff'">
                    </div>
                    <div class="ml-2.5 sm:ml-3">
                        <h1 class="text-lg sm:text-2xl font-bold text-white tracking-tight drop-shadow-md leading-tight">ক্ব-রিয়ানা</h1>
                        <p class="text-[10px] sm:text-[11px] text-amber-300 font-medium tracking-wider">কুরআন শিক্ষা সোসাইটি</p>
                    </div>
                </a>

                <!-- Header Actions (Visible on Mobile & Desktop): Dark/Light Toggle + App Download Button -->
                <div class="flex items-center space-x-2 md:order-last">
                    <!-- Dark / Light Mode Switcher Button -->
                    <button id="themeToggleBtn" type="button" 
                            class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-emerald-950/80 hover:bg-emerald-900 border border-gold-rich/40 text-amber-300 flex items-center justify-center transition shadow-sm"
                            title="ডার্ক / লাইট মোড পরিবর্তন">
                        <!-- Sun Icon (shown in dark mode) -->
                        <svg id="themeSunIcon" class="w-4 h-4 hidden text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        <!-- Moon Icon (shown in light mode) -->
                        <svg id="themeMoonIcon" class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                    </button>

                    <!-- App Download / Install Action Button -->
                    <button id="headerAppInstallBtn" type="button" 
                            class="flex items-center space-x-1.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-emerald-950 font-black px-2.5 sm:px-3.5 py-1.5 rounded-xl shadow-md transition transform active:scale-95 text-xs border border-amber-300/40"
                            title="কারিয়ানা মোবাইল অ্যাপ ফোনে ইনস্টল করুন">
                        <svg class="w-3.5 h-3.5 text-emerald-950 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span class="text-[11px] sm:text-xs">অ্যাপ ডাউনলোড</span>
                    </button>
                </div>

                <!-- Desktop Nav -->
                <nav class="hidden md:flex items-center space-x-5 text-white font-medium text-sm">
                    <a href="<?= $baseUrl ?? '' ?>/" class="hover:text-amber-300 transition py-2 border-b-2 border-transparent hover:border-amber-300">হোম</a>
                    <a href="<?= $baseUrl ?? '' ?>/courses" class="hover:text-amber-300 transition py-2 border-b-2 border-transparent hover:border-amber-300">কোর্সসমূহ</a>
                    <a href="<?= $baseUrl ?? '' ?>/books" class="hover:text-amber-300 transition py-2 border-b-2 border-transparent hover:border-amber-300">আমাদের বই</a>
                    <a href="<?= $baseUrl ?? '' ?>/directors" class="hover:text-amber-300 transition py-2 border-b-2 border-transparent hover:border-amber-300 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        জেলা পরিচালকবৃন্দ
                    </a>
                    
                    <!-- Islamic Tools Dropdown -->
                    <div class="relative group py-2">
                        <button class="hover:text-amber-300 transition flex items-center focus:outline-none">
                            <span>ইসলামী টুলস</span> 
                            <svg class="w-3.5 h-3.5 ml-1 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="absolute left-0 mt-2 w-60 rounded-2xl bg-[#fffefb] text-slate-800 shadow-2xl border-2 border-gold-rich/40 py-2 hidden group-hover:block z-50">
                            <a href="<?= $baseUrl ?? '' ?>/prayer-times" class="flex items-center px-4 py-2.5 hover:bg-amber-50 text-xs font-bold text-emerald-night transition">
                                <svg class="w-4 h-4 text-amber-600 mr-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                নামাজের সময়সূচি (৬৪ জেলা)
                            </a>
                            <a href="<?= $baseUrl ?? '' ?>/zakat" class="flex items-center px-4 py-2.5 hover:bg-amber-50 text-xs font-bold text-emerald-night transition">
                                <svg class="w-4 h-4 text-amber-600 mr-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                রূপার নিসাব যাকাত হিসাব
                            </a>
                            <a href="<?= $baseUrl ?? '' ?>/tasbeeh" class="flex items-center px-4 py-2.5 hover:bg-amber-50 text-xs font-bold text-emerald-night transition">
                                <svg class="w-4 h-4 text-amber-600 mr-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" /></svg>
                                ডিজিটাল তাসবীহ কাউন্টার
                            </a>
                            <a href="<?= $baseUrl ?? '' ?>/scan" class="flex items-center px-4 py-2.5 hover:bg-amber-50 text-xs font-bold text-emerald-night transition">
                                <svg class="w-4 h-4 text-amber-600 mr-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                                কিউআর কোড লেসন স্ক্যানার
                            </a>
                            <a href="<?= $baseUrl ?? '' ?>/quran" class="flex items-center px-4 py-2.5 hover:bg-amber-50 text-xs font-bold text-emerald-night transition border-t border-slate-100">
                                <svg class="w-4 h-4 text-amber-600 mr-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                                কুরআন ওয়েব রিডার
                            </a>
                        </div>
                    </div>

                    <a href="<?= $baseUrl ?? '' ?>/blog" class="hover:text-amber-300 transition py-2 border-b-2 border-transparent hover:border-amber-300">ব্লগ</a>

                    <!-- Dynamic Multi-Role Profile / Login Link -->
                    <a href="<?= $authUrl ?>" 
                       class="bg-emerald-night/80 hover:bg-emerald-night border border-gold-rich/50 text-amber-300 px-4 py-2 rounded-full font-bold text-xs flex items-center transition shadow-sm">
                        <svg class="w-3.5 h-3.5 mr-1.5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <?php if ($authRoleBadge): ?>
                            <span class="px-1.5 py-0.2 bg-gold-rich text-white text-[9px] font-bold rounded mr-1.5"><?= $authRoleBadge ?></span>
                        <?php endif; ?>
                        <?= $authLabel ?>
                    </a>
                    
                    <a href="<?= $baseUrl ?? '' ?>/books" class="bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white px-5 py-2 rounded-full font-bold text-xs shadow-lg shadow-gold-rich/30 transition transform hover:-translate-y-0.5 flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        বই অর্ডার
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        <?= $content ?? '' ?>
    </main>

    <!-- Footer -->
    <footer class="bg-emerald-night text-emerald-light pt-12 pb-6 border-t-4 border-gold-rich mt-12">
        <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="col-span-1 md:col-span-2">
                <h3 class="text-xl font-bold text-amber-300 mb-4 border-b border-emerald-deep pb-2 inline-block">ক্ব-রিয়ানা কুরআন শিক্ষা সোসাইটি</h3>
                <p class="text-sm leading-relaxed text-emerald-100/80 mb-4 pr-4">
                    সহজ ও সহীহ পদ্ধতিতে কুরআন শিক্ষার এক অনন্য প্রতিষ্ঠান। ১২টি বিশেষ সাংকেতিক চিহ্ন ও তাজবীদ কালার কোড সহ আমাদের বিশেষ প্রকাশনাগুলো আপনার কুরআন শিক্ষাকে করবে আরও সহজ।
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="w-8 h-8 rounded-full bg-emerald-deep flex items-center justify-center hover:bg-gold-rich hover:text-white transition">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8H6v4h3v12h5V12h3.642L18 8h-4V6.333C14 5.374 14.5 5 15.5 5H18V0h-3.808C10.595 0 9 1.583 9 4.615V8z"/></svg>
                    </a>
                    <a href="#" class="w-8 h-8 rounded-full bg-emerald-deep flex items-center justify-center hover:bg-gold-rich hover:text-white transition">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                </div>
            </div>
            
            <div>
                <h4 class="text-lg font-bold text-white mb-4">প্রয়োজনীয় লিংক</h4>
                <ul class="space-y-2 text-sm">
                    <li>
                        <a href="<?= $baseUrl ?? '' ?>/directors" class="hover:text-amber-300 transition flex items-center">
                            <svg class="w-3.5 h-3.5 text-amber-500 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            জেলা পরিচালক তালিকা
                        </a>
                    </li>
                    <li>
                        <a href="<?= $baseUrl ?? '' ?>/login" class="hover:text-amber-300 transition flex items-center">
                            <svg class="w-3.5 h-3.5 text-amber-500 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            প্রবেশ ও সেবা পোর্টাল
                        </a>
                    </li>
                    <li>
                        <a href="<?= $baseUrl ?? '' ?>/blog" class="hover:text-amber-300 transition flex items-center">
                            <svg class="w-3.5 h-3.5 text-amber-500 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            ইসলামী ব্লগ ও ফিচার
                        </a>
                    </li>
                    <li>
                        <a href="<?= $baseUrl ?? '' ?>/prayer-times" class="hover:text-amber-300 transition flex items-center">
                            <svg class="w-3.5 h-3.5 text-amber-500 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            নামাজের সময়সূচি
                        </a>
                    </li>
                </ul>
            </div>
            
            <div>
                <h4 class="text-lg font-bold text-white mb-4">যোগাযোগ</h4>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mt-0.5 mr-2.5 text-amber-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>ঢাকা, বাংলাদেশ</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mt-0.5 mr-2.5 text-amber-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span>+880 1711 756 391</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mt-0.5 mr-2.5 text-amber-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>m.rasel.g@gmail.com</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="container mx-auto px-4 mt-8 pt-6 border-t border-emerald-deep/50 text-center text-xs text-emerald-100/50">
            &copy; <?= date('Y') ?> কারিয়ানা কুরআন শিক্ষা সোসাইটি. সর্বস্বত্ব সংরক্ষিত।
        </div>
    </footer>

    <!-- Modern Mobile Bottom Navigation (Matching Reference UI: media_1790103764775.png) -->
    <?php
    $currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    $isHome = ($currentUri === '' || $currentUri === '/' || $currentUri === '/index.php');
    $isCourses = str_starts_with($currentUri, '/courses');
    $isBooks = str_starts_with($currentUri, '/books');
    $isPrayer = str_starts_with($currentUri, '/prayer-times') || str_starts_with($currentUri, '/tasbeeh') || str_starts_with($currentUri, '/zakat');
    $isProfileActive = str_starts_with($currentUri, '/profile') || str_starts_with($currentUri, '/director/dashboard') || str_starts_with($currentUri, '/teacher/dashboard') || str_starts_with($currentUri, '/admin') || str_starts_with($currentUri, '/login') || str_starts_with($currentUri, '/manager/dashboard');
    ?>
    <div class="bottom-nav-wrapper" role="navigation" aria-label="মোবাইল নেভিগেশন">
        <nav class="bottom-nav">
            <div class="bottom-nav-items">
                <!-- 1. Home (হোম) -->
                <a href="<?= $baseUrl ?? '' ?>/" class="bottom-nav-item <?= $isHome ? 'is-active' : '' ?>">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>হোম</span>
                </a>

                <!-- 2. Messages / Courses (কোর্স) -->
                <a href="<?= $baseUrl ?? '' ?>/courses" class="bottom-nav-item <?= $isCourses ? 'is-active' : '' ?>">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h8M8 14h5m-5 8l-3-3H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-7l-4 4z" />
                    </svg>
                    <span>কোর্স</span>
                </a>

                <!-- 3. Search / Books (বই) -->
                <a href="<?= $baseUrl ?? '' ?>/books" class="bottom-nav-item <?= $isBooks ? 'is-active' : '' ?>">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>বই</span>
                </a>

                <!-- 4. History / Prayer Times (নামাজ) -->
                <a href="<?= $baseUrl ?? '' ?>/prayer-times" class="bottom-nav-item <?= $isPrayer ? 'is-active' : '' ?>">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>নামাজ</span>
                </a>

                <!-- 5. Profile / Multi-Role Portal (প্রোফাইল) -->
                <a href="<?= $authUrl ?>" class="bottom-nav-item <?= $isProfileActive ? 'is-active' : '' ?>">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span><?= $authRoleBadge ?: 'প্রোফাইল' ?></span>
                </a>
            </div>

            <!-- Modern iOS Indicator Bar -->
            <div class="bottom-nav-indicator" aria-hidden="true"></div>
        </nav>
    </div>

    <!-- Dynamic Custom Footer Scripts (e.g. Chat widgets, WhatsApp buttons) -->
    <?php if (!empty($mkt['custom_body_end_scripts'])): ?>
        <?= $mkt['custom_body_end_scripts'] ?>
    <?php endif; ?>

    <!-- Progressive Web App Engine & Real-Time Network Health Observer -->
    <script>
    (function() {
        // Theme Toggle Controller (Dark / Light Mode)
        const themeToggleBtn = document.getElementById('themeToggleBtn');
        const themeSunIcon = document.getElementById('themeSunIcon');
        const themeMoonIcon = document.getElementById('themeMoonIcon');

        function syncThemeIcons() {
            const isDark = document.documentElement.classList.contains('dark');
            if (themeSunIcon && themeMoonIcon) {
                if (isDark) {
                    themeSunIcon.classList.remove('hidden');
                    themeMoonIcon.classList.add('hidden');
                } else {
                    themeSunIcon.classList.add('hidden');
                    themeMoonIcon.classList.remove('hidden');
                }
            }
        }

        syncThemeIcons();

        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', function() {
                const isDark = document.documentElement.classList.toggle('dark');
                try {
                    localStorage.setItem('kariana_theme', isDark ? 'dark' : 'light');
                } catch (e) {}
                syncThemeIcons();
            });
        }

        // 1. Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('<?= $baseUrl ?? '' ?>/sw.js').catch(function() {});
            });
        }

        // 2. PWA In-App Install Prompt Handling
        let deferredPrompt = null;
        const installBanner = document.getElementById('pwaInstallBanner');
        const installBtn = document.getElementById('pwaInstallBtn');
        const headerAppInstallBtn = document.getElementById('headerAppInstallBtn');
        const dismissBtn = document.getElementById('pwaDismissBtn');
        const networkBar = document.getElementById('networkAlertBar');
        const guideModal = document.getElementById('pwaGuideModal');
        const closeGuideModalBtn = document.getElementById('closeGuideModalBtn');
        const gotItGuideBtn = document.getElementById('gotItGuideBtn');

        const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;
        const isDismissed = sessionStorage.getItem('pwa_banner_dismissed');

        // Auto display banner on mobile devices if not standalone and not dismissed
        if (!isStandalone && !isDismissed && installBanner) {
            if (window.innerWidth <= 768 || /Android|iPhone|iPad|iPod|Mobile/i.test(navigator.userAgent)) {
                installBanner.classList.remove('hidden');
                installBanner.classList.add('flex');
            }
        }

        const isMobileUser = /Android|iPhone|iPad|iPod|Mobile/i.test(navigator.userAgent) || window.innerWidth <= 768;
        if (headerAppInstallBtn) {
            const btnText = headerAppInstallBtn.querySelector('span');
            if (btnText) {
                btnText.textContent = isMobileUser ? '📱 অ্যাপ ডাউনলোড' : '💻 পিসি ও মোবাইল অ্যাপ';
            }
        }

        window.switchAppHubTab = function(tabName) {
            const tabs = ['mobile', 'desktop', 'web'];
            tabs.forEach(t => {
                const btn = document.getElementById('tabBtn' + t.charAt(0).toUpperCase() + t.slice(1));
                const content = document.getElementById('tabContent' + t.charAt(0).toUpperCase() + t.slice(1));
                if (btn && content) {
                    if (t === tabName) {
                        btn.className = 'app-hub-tab flex-1 py-2 sm:py-2.5 px-2 rounded-xl text-xs sm:text-sm font-black transition flex items-center justify-center gap-1.5 bg-amber-400 text-emerald-950 shadow';
                        content.classList.remove('hidden');
                    } else {
                        btn.className = 'app-hub-tab flex-1 py-2 sm:py-2.5 px-2 rounded-xl text-xs sm:text-sm font-bold text-slate-300 hover:text-white transition flex items-center justify-center gap-1.5';
                        content.classList.add('hidden');
                    }
                }
            });
        };

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            if (!isStandalone && !isDismissed && installBanner) {
                installBanner.classList.remove('hidden');
                installBanner.classList.add('flex');
            }
            document.querySelectorAll('.btn-pwa-install').forEach(btn => {
                btn.style.display = 'inline-flex';
            });
        });

        window.triggerPwaPromptDirectly = function() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then(({ outcome }) => {
                    if (outcome === 'accepted') {
                        if (installBanner) {
                            installBanner.classList.add('hidden');
                            installBanner.classList.remove('flex');
                        }
                        closeGuideModal();
                    }
                    deferredPrompt = null;
                });
            } else {
                alert('আপনার ব্রাউজার থেকে অ্যাপটি হোমস্ক্রিন বা পিসিতে যুক্ত করতে ব্রাউজার মেনু (⋮) থেকে "Add to Home screen" বা "Install" নির্বাচন করুন।');
            }
        };

        function openAppHubModal() {
            if (guideModal) {
                switchAppHubTab(isMobileUser ? 'mobile' : 'desktop');
                guideModal.classList.remove('hidden');
                guideModal.classList.add('flex');
            }
        }

        function triggerInstallAction() {
            openAppHubModal();
        }

        if (installBtn) {
            installBtn.addEventListener('click', triggerInstallAction);
        }

        if (headerAppInstallBtn) {
            headerAppInstallBtn.addEventListener('click', triggerInstallAction);
        }

        document.addEventListener('click', (e) => {
            const targetBtn = e.target.closest('.btn-pwa-install');
            if (targetBtn) {
                triggerInstallAction();
            }
        });

        function closeGuideModal() {
            if (guideModal) {
                guideModal.classList.add('hidden');
                guideModal.classList.remove('flex');
            }
        }

        if (closeGuideModalBtn) closeGuideModalBtn.addEventListener('click', closeGuideModal);
        if (gotItGuideBtn) gotItGuideBtn.addEventListener('click', closeGuideModal);
        if (guideModal) {
            guideModal.addEventListener('click', (e) => {
                if (e.target === guideModal) closeGuideModal();
            });
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && guideModal && !guideModal.classList.contains('hidden')) {
                closeGuideModal();
            }
        });

        if (dismissBtn) {
            dismissBtn.addEventListener('click', () => {
                if (installBanner) {
                    installBanner.classList.add('hidden');
                    installBanner.classList.remove('flex');
                }
                sessionStorage.setItem('pwa_banner_dismissed', '1');
            });
        }

        window.addEventListener('appinstalled', () => {
            if (installBanner) {
                installBanner.classList.add('hidden');
                installBanner.classList.remove('flex');
            }
            deferredPrompt = null;
        });

        // 3. Real-Time Network Status Observer (Offline Graceful Degradation)
        function updateNetworkStatus() {
            if (!networkBar) return;
            if (!navigator.onLine) {
                networkBar.className = 'bg-rose-900 border-b-2 border-rose-500 text-rose-100 text-xs py-1.5 px-3 text-center font-bold block z-50 animate-pulse';
                networkBar.innerHTML = '⚠️ ইন্টারনেট সংযোগ বিচ্ছিন্ন রয়েছে — পূর্বে লোড করা তথ্য দৃশ্যমান আছে, নতুন তথ্য পেতে ইন্টারনেট চালু করুন।';
            } else {
                if (networkBar.classList.contains('bg-rose-900')) {
                    networkBar.className = 'bg-emerald-800 border-b-2 border-emerald-400 text-emerald-100 text-xs py-1.5 px-3 text-center font-bold block z-50';
                    networkBar.innerHTML = '✅ ইন্টারনেট সংযোগ পুনঃস্থাপিত হয়েছে!';
                    setTimeout(() => {
                        networkBar.className = 'hidden';
                    }, 3500);
                } else {
                    networkBar.className = 'hidden';
                }
            }
        }

        window.addEventListener('online', updateNetworkStatus);
        window.addEventListener('offline', updateNetworkStatus);
        if (!navigator.onLine) updateNetworkStatus();

        // 4. Smooth PWA Back Navigation
        if (window.matchMedia('(display-mode: standalone)').matches) {
            window.addEventListener('popstate', function() {
                // Preserves in-app history stack
            });
        }
    })();
    </script>
</body>
</html>

