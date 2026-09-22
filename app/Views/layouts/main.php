<!DOCTYPE html>
<html lang="bn" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title><?= isset($title) ? htmlspecialchars($title) : 'কারিয়ানা কুরআন শিক্ষা সোসাইটি' ?></title>
    
    <!-- Enterprise Global Maximum-Level SEO Stack (Schema.org JSON-LD, OpenGraph, Twitter Cards, Canonical) -->
    <?= \Core\SeoHelper::renderHeadStack([
        'title'       => $title ?? null,
        'description' => $description ?? null,
        'keywords'    => $keywords ?? null,
        'canonical'   => $canonical ?? null,
        'schema'      => $schemaJsonLd ?? null,
    ]) ?>

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
    
    <!-- Alpine.js Declarative Reactive Engine -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $baseUrl ?? '' ?>/assets/css/main.css">
    
    <style>
        [x-cloak] { display: none !important; }
        /* Bottom Nav Styles */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #022c22;
            display: flex;
            justify-content: space-around;
            padding: 0.75rem 0;
            padding-bottom: calc(0.75rem + env(safe-area-inset-bottom, 0px));
            z-index: 50;
            box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        @media (min-width: 768px) {
            .bottom-nav { display: none; }
            body { padding-bottom: 0 !important; }
        }
        body { padding-bottom: calc(70px + env(safe-area-inset-bottom, 0px)); }
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
    <div class="bg-emerald-night text-white text-sm py-1.5 px-4 border-b border-gold-deep/30">
        <div class="container mx-auto flex items-center justify-between">
            <div class="flex items-center space-x-2 space-x-reverse overflow-hidden whitespace-nowrap">
                <span class="bg-gold-rich text-white px-2 py-0.5 rounded text-xs font-bold shrink-0 animate-pulse">আপডেট</span>
                <marquee scrollamount="4" class="text-emerald-light ml-3 text-[13px]">
                    আজকের নামাজের সময়সূচি: ফজর - ৪:৪০ | যোহর - ১২:১৫ | আসর - ৪:৩০ | মাগরিব - ৬:১০ | ইশা - ৭:৩০ (ঢাকা) &nbsp;&nbsp;|&nbsp;&nbsp; কারিয়ানা কুরআন বাংলা অর্থসহ - স্পেশাল অফার ৳১০০০
                </marquee>
            </div>
            <div class="hidden md:flex items-center space-x-4 text-xs">
                <span class="text-gold-shimmer"><i class="fas fa-moon mr-1"></i> ১৪ রবিউল আউয়াল, ১৪৪৬ হিজরী</span>
                <span class="text-emerald-300">|</span>
                <a href="<?= $baseUrl ?? '' ?>/login" 
                   class="text-gold-shimmer hover:text-white font-bold flex items-center bg-emerald-900/80 px-3 py-1 rounded-full border border-gold-rich/40 transition text-xs shadow-sm">
                    <i class="fas fa-user-circle mr-1.5 text-xs text-gold-shimmer"></i> প্রবেশ / লগইন
                </a>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="bg-emerald-deep islamic-pattern sticky top-0 z-40 shadow-lg border-b-2 border-gold-rich">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <a href="<?= $baseUrl ?? '' ?>/" class="flex items-center group">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center p-1 border-2 border-gold-shimmer shadow-[0_0_15px_rgba(251,191,36,0.3)] transition transform group-hover:scale-105">
                        <img src="<?= $baseUrl ?? '' ?>/assets/images/logo.png" alt="Logo" class="w-full h-full object-contain rounded-full" onerror="this.src='https://ui-avatars.com/api/?name=Kariana&background=064e3b&color=fff'">
                    </div>
                    <div class="ml-3">
                        <h1 class="text-2xl font-bold text-white tracking-tight drop-shadow-md">ক্ব-রিয়ানা</h1>
                        <p class="text-[11px] text-gold-shimmer font-medium tracking-wider">কুরআন শিক্ষা সোসাইটি</p>
                    </div>
                </a>

                <!-- Desktop Nav -->
                <nav class="hidden md:flex items-center space-x-5 text-white font-medium text-sm">
                    <a href="<?= $baseUrl ?? '' ?>/" class="hover:text-gold-shimmer transition py-2 border-b-2 border-transparent hover:border-gold-shimmer">হোম</a>
                    <a href="<?= $baseUrl ?? '' ?>/courses" class="hover:text-gold-shimmer transition py-2 border-b-2 border-transparent hover:border-gold-shimmer">কোর্সসমূহ</a>
                    <a href="<?= $baseUrl ?? '' ?>/books" class="hover:text-gold-shimmer transition py-2 border-b-2 border-transparent hover:border-gold-shimmer">আমাদের বই</a>
                    <a href="<?= $baseUrl ?? '' ?>/directors" class="hover:text-gold-shimmer transition py-2 border-b-2 border-transparent hover:border-gold-shimmer flex items-center">
                        <i class="fas fa-users-rectangle mr-1 text-gold-shimmer"></i> জেলা পরিচালকবৃন্দ
                    </a>
                    
                    <!-- Islamic Tools Dropdown -->
                    <div class="relative group py-2">
                        <button class="hover:text-gold-shimmer transition flex items-center focus:outline-none">
                            <span>ইসলামী টুলস</span> <i class="fas fa-chevron-down text-[10px] ml-1.5 transition-transform group-hover:rotate-180"></i>
                        </button>
                        <div class="absolute left-0 mt-2 w-56 rounded-2xl bg-[#fffefb] text-slate-800 shadow-2xl border-2 border-gold-rich/40 py-2 hidden group-hover:block z-50">
                            <a href="<?= $baseUrl ?? '' ?>/prayer-times" class="flex items-center px-4 py-2.5 hover:bg-amber-50 text-xs font-bold text-emerald-night transition">
                                <i class="fas fa-clock text-gold-rich w-5 mr-2"></i> নামাজের সময়সূচি (৬৪ জেলা)
                            </a>
                            <a href="<?= $baseUrl ?? '' ?>/zakat" class="flex items-center px-4 py-2.5 hover:bg-amber-50 text-xs font-bold text-emerald-night transition">
                                <i class="fas fa-coins text-gold-rich w-5 mr-2"></i> রূপার নিসাব যাকাত হিসাব
                            </a>
                            <a href="<?= $baseUrl ?? '' ?>/tasbeeh" class="flex items-center px-4 py-2.5 hover:bg-amber-50 text-xs font-bold text-emerald-night transition">
                                <i class="fas fa-fingerprint text-gold-rich w-5 mr-2"></i> ডিজিটাল তাসবীহ কাউন্টার
                            </a>
                            <a href="<?= $baseUrl ?? '' ?>/scan" class="flex items-center px-4 py-2.5 hover:bg-amber-50 text-xs font-bold text-emerald-night transition">
                                <i class="fas fa-qrcode text-gold-rich w-5 mr-2"></i> কিউআর কোড লেসন স্ক্যানার
                            </a>
                            <a href="<?= $baseUrl ?? '' ?>/quran" class="flex items-center px-4 py-2.5 hover:bg-amber-50 text-xs font-bold text-emerald-night transition border-t border-slate-100">
                                <i class="fas fa-book-quran text-gold-rich w-5 mr-2"></i> কুরআন ওয়েব রিডার
                            </a>
                        </div>
                    </div>

                    <a href="<?= $baseUrl ?? '' ?>/blog" class="hover:text-gold-shimmer transition py-2 border-b-2 border-transparent hover:border-gold-shimmer">ব্লগ</a>

                    <!-- Direct Universal Single Login Link -->
                    <a href="<?= $baseUrl ?? '' ?>/login" 
                       class="bg-emerald-night/80 hover:bg-emerald-night border border-gold-rich/50 text-gold-shimmer px-4 py-2 rounded-full font-bold text-xs flex items-center transition shadow-sm">
                        <i class="fas fa-user-circle mr-1.5 text-xs"></i> প্রবেশ / লগইন
                    </a>
                    
                    <a href="<?= $baseUrl ?? '' ?>/books" class="bg-gold-rich hover:bg-gold-deep text-white px-5 py-2 rounded-full font-bold text-xs shadow-lg shadow-gold-rich/30 transition transform hover:-translate-y-0.5 flex items-center">
                        <i class="fas fa-shopping-cart mr-1.5"></i> বই অর্ডার
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
                <h3 class="text-xl font-bold text-gold-shimmer mb-4 border-b border-emerald-deep pb-2 inline-block">ক্ব-রিয়ানা কুরআন শিক্ষা সোসাইটি</h3>
                <p class="text-sm leading-relaxed text-emerald-100/80 mb-4 pr-4">
                    সহজ ও সহীহ পদ্ধতিতে কুরআন শিক্ষার এক অনন্য প্রতিষ্ঠান। ১২টি বিশেষ সাংকেতিক চিহ্ন ও তাজবীদ কালার কোড সহ আমাদের বিশেষ প্রকাশনাগুলো আপনার কুরআন শিক্ষাকে করবে আরও সহজ।
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="w-8 h-8 rounded-full bg-emerald-deep flex items-center justify-center hover:bg-gold-rich hover:text-white transition"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="w-8 h-8 rounded-full bg-emerald-deep flex items-center justify-center hover:bg-gold-rich hover:text-white transition"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            
            <div>
                <h4 class="text-lg font-bold text-white mb-4">প্রয়োজনীয় লিংক</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="<?= $baseUrl ?? '' ?>/directors" class="hover:text-gold-shimmer transition flex items-center"><i class="fas fa-chevron-right text-xs text-gold-deep mr-2"></i>জেলা পরিচালক তালিকা</a></li>
                    <li><a href="<?= $baseUrl ?? '' ?>/login" class="hover:text-gold-shimmer transition flex items-center"><i class="fas fa-chevron-right text-xs text-gold-deep mr-2"></i>প্রবেশ ও সেবা পোর্টাল</a></li>
                    <li><a href="<?= $baseUrl ?? '' ?>/blog" class="hover:text-gold-shimmer transition flex items-center"><i class="fas fa-chevron-right text-xs text-gold-deep mr-2"></i>ইসলামী ব্লগ ও ফিচার</a></li>
                    <li><a href="<?= $baseUrl ?? '' ?>/prayer-times" class="hover:text-gold-shimmer transition flex items-center"><i class="fas fa-chevron-right text-xs text-gold-deep mr-2"></i>নামাজের সময়সূচি</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="text-lg font-bold text-white mb-4">যোগাযোগ</h4>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-start"><i class="fas fa-map-marker-alt mt-1 mr-3 text-gold-shimmer"></i> ঢাকা, বাংলাদেশ</li>
                    <li class="flex items-start"><i class="fas fa-phone mt-1 mr-3 text-gold-shimmer"></i> +880 1234 567 890</li>
                    <li class="flex items-start"><i class="fas fa-envelope mt-1 mr-3 text-gold-shimmer"></i> info@karianaquran.com</li>
                </ul>
            </div>
        </div>
        
        <div class="container mx-auto px-4 mt-8 pt-6 border-t border-emerald-deep/50 text-center text-xs text-emerald-100/50">
            &copy; <?= date('Y') ?> কারিয়ানা কুরআন শিক্ষা সোসাইটি. সর্বস্বত্ব সংরক্ষিত।
        </div>
    </footer>

    <!-- Bottom Navigation Bar (Mobile) -->
    <div class="bottom-nav">
        <a href="<?= $baseUrl ?? '' ?>/" class="flex flex-col items-center justify-center w-full text-emerald-100 hover:text-gold-shimmer <?= (isset($currentPath) && $currentPath == '/') ? 'text-gold-shimmer' : '' ?>">
            <i class="fas fa-home text-lg mb-0.5"></i>
            <span class="text-[10px]">হোম</span>
        </a>
        <a href="<?= $baseUrl ?? '' ?>/courses" class="flex flex-col items-center justify-center w-full text-emerald-100 hover:text-gold-shimmer">
            <i class="fas fa-book-open text-lg mb-0.5"></i>
            <span class="text-[10px]">কোর্স</span>
        </a>
        <a href="<?= $baseUrl ?? '' ?>/prayer-times" class="flex flex-col items-center justify-center w-full text-emerald-100 hover:text-gold-shimmer relative -top-3">
            <div class="bg-gold-rich text-white w-11 h-11 rounded-full flex items-center justify-center shadow-[0_0_15px_rgba(217,119,6,0.5)] border-4 border-emerald-night">
                <i class="fas fa-clock text-lg"></i>
            </div>
            <span class="text-[10px] mt-0.5 font-bold">নামাজ</span>
        </a>
        <a href="<?= $baseUrl ?? '' ?>/books" class="flex flex-col items-center justify-center w-full text-emerald-100 hover:text-gold-shimmer">
            <i class="fas fa-shopping-bag text-lg mb-0.5"></i>
            <span class="text-[10px]">বই</span>
        </a>
        <a href="<?= $baseUrl ?? '' ?>/login" class="flex flex-col items-center justify-center w-full text-emerald-100 hover:text-gold-shimmer <?= (isset($currentPath) && $currentPath == '/login') ? 'text-gold-shimmer' : '' ?>">
            <i class="fas fa-user-circle text-lg mb-0.5"></i>
            <span class="text-[10px]">লগইন</span>
        </a>
    </div>

    <!-- Dynamic Custom Footer Scripts (e.g. Chat widgets, WhatsApp buttons) -->
    <?php if (!empty($mkt['custom_body_end_scripts'])): ?>
        <?= $mkt['custom_body_end_scripts'] ?>
    <?php endif; ?>
</body>
</html>
