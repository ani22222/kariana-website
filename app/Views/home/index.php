<!-- ============================================================
     VAPE-INSPIRED SMOOTH HERO SLIDER & 3D BOOK SHOWCASE
     ============================================================ -->
<div class="bg-emerald-deep pt-6 pb-12 px-4 relative overflow-hidden">
    <div class="container mx-auto max-w-4xl">
        
        <!-- Main Vape-Style Hero Shell -->
        <div class="hero-shell relative" id="heroShell">
            <!-- Background Ambient Glow & Geometric Rings -->
            <div class="hero-bg-shapes" aria-hidden="true">
                <div class="hero-shape-orb-1"></div>
                <div class="hero-shape-orb-2"></div>
                <div class="hero-shape-ring"></div>
            </div>

            <!-- Slide Carousel Stack -->
            <div id="heroCarousel" class="relative z-10">
                <?php 
                $booksList = !empty($featuredBooks) ? $featuredBooks : [];
                $slidePills = [
                    0 => ['pill' => '★ ১ম বেস্টসেলার - ফ্ল্যাগশিপ সংস্করণ', 'chipA' => '১২টি তাজবীদ সংকেত', 'chipB' => 'কিউআর ভিডিও লেসন'],
                    1 => ['pill' => '★ ২য় বেস্টসেলার - ৩০তম পারা আমপারা', 'chipA' => 'সূরা নাবা হতে নাস', 'chipB' => 'প্রতিটি পৃষ্ঠায় লেসন'],
                    2 => ['pill' => '★ ৩য় বেস্টসেলার - ৩০ দিনে কুরআন শিক্ষা', 'chipA' => 'বৈজ্ঞানিক নূরানী কায়দা', 'chipB' => 'সহজ তাজবীদ সংকেত'],
                ];

                foreach ($booksList as $idx => $b):
                    $discPrice = (float)($b['discount_price'] ?? $b['price']);
                    $origPrice = (float)$b['price'];
                    $hasDiscount = ($discPrice > 0 && $discPrice < $origPrice);
                    $saveAmount = (int)($origPrice - $discPrice);
                    $pills = $slidePills[$idx] ?? ['pill' => '★ বিশেষ সংস্করণ', 'chipA' => 'নূরানী সংকেত', 'chipB' => 'ভিডিও পাঠ'];
                ?>
                <article class="hero-slide <?= $idx === 0 ? 'is-active' : '' ?>" 
                         data-index="<?= $idx ?>"
                         itemscope itemtype="https://schema.org/Book">
                    <div class="flex flex-col items-center justify-center w-full text-center max-w-2xl mx-auto z-20">
                        
                        <!-- Top Pill: Bestseller Flagship Edition -->
                        <div class="mb-2">
                            <span class="slide-pill">
                                <i class="fas fa-crown mr-1.5 text-gold-shimmer text-xs"></i> <?= $pills['pill'] ?>
                            </span>
                        </div>

                        <!-- Single-Line Clean Headline -->
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-white tracking-tight drop-shadow-md truncate max-w-xl mx-auto mb-4 px-2" itemprop="name">
                            <?= htmlspecialchars($b['title']) ?>
                        </h1>

                        <!-- Center 3D Floating Book Art (Identical to User Reference Screenshot) -->
                        <div class="py-2 relative">
                            <div class="hero-art">
                                <div class="halo"></div>

                                <!-- Orbit Floating Chips -->
                                <span class="orbit-chip chip-a">
                                    <span class="pulse"></span> <?= $pills['chipA'] ?>
                                </span>
                                <span class="orbit-chip chip-b">
                                    <i class="fas fa-qrcode text-gold-shimmer mr-1.5"></i> <?= $pills['chipB'] ?>
                                </span>

                                <!-- 3D Book Mockup Card -->
                                <div class="hero-book-card">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] bg-amber-600 text-white font-bold px-2.5 py-0.5 rounded-full shadow">
                                            কারিয়ানা নূরানী
                                        </span>
                                        <span class="text-[11px] text-gold-shimmer font-mono font-bold">
                                            <?= ($idx + 1) ?>/৩
                                        </span>
                                    </div>

                                    <div class="text-center my-auto py-4">
                                        <div class="w-16 h-16 mx-auto bg-gold-rich/20 rounded-full flex items-center justify-center text-gold-shimmer text-3xl mb-3 shadow-inner border border-gold-rich/40">
                                            <i class="fas fa-book-quran"></i>
                                        </div>
                                        <h3 class="font-extrabold text-base sm:text-lg text-white line-clamp-2 px-2">
                                            <?= htmlspecialchars($b['title']) ?>
                                        </h3>
                                        <span class="text-[11px] sm:text-xs text-emerald-200 block mt-1">তাজবীদ কালার কোডেড</span>
                                    </div>

                                    <div class="pt-3 border-t border-emerald-700/60 flex items-center justify-between text-xs text-emerald-200">
                                        <span>নূরানী প্রকাশনা</span>
                                        <span class="font-bold text-gold-shimmer font-mono text-sm">৳<?= \Core\BengaliHelper::toBengaliNumber((int)$discPrice) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Compact Action Buttons Below Book -->
                        <div class="flex items-center justify-center gap-3 pt-4 pb-2" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                            <meta itemprop="priceCurrency" content="BDT">
                            <meta itemprop="price" content="<?= $discPrice ?>">
                            <link itemprop="availability" href="https://schema.org/InStock">

                            <a href="https://wa.me/8801711756391?text=<?= urlencode('আসসালামু আলাইকুম, আমি কারিয়ানা ওয়েবসাইট থেকে "' . $b['title'] . '" বইটি অফার মূল্যে (৳' . (int)$discPrice . ') অর্ডার করতে চাই।') ?>" 
                               target="_blank"
                               class="bg-gradient-to-r from-gold-rich via-amber-500 to-gold-deep hover:brightness-110 text-white font-bold py-2.5 px-6 rounded-full shadow-[0_4px_20px_rgba(217,119,6,0.35)] transition transform hover:scale-105 flex items-center text-xs sm:text-sm">
                                <i class="fab fa-whatsapp text-base mr-2 text-emerald-200"></i> অর্ডার করুন - ৳<?= \Core\BengaliHelper::toBengaliNumber((int)$discPrice) ?>
                            </a>

                            <a href="<?= $baseUrl ?? '' ?>/books/<?= htmlspecialchars($b['slug']) ?>" 
                               class="bg-emerald-night/80 hover:bg-emerald-night border border-emerald-500/50 hover:border-gold-rich text-emerald-100 hover:text-white font-semibold py-2.5 px-5 rounded-full transition flex items-center text-xs sm:text-sm backdrop-blur-sm">
                                <i class="fas fa-book-open mr-1.5 text-gold-shimmer"></i> বিস্তারিত দেখুন
                            </a>
                        </div>

                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <!-- Slide Indicator Dots -->
            <div class="hero-dots" id="heroDots" role="tablist" aria-label="বই স্লাইডার তালিকা"></div>

            <!-- Prev / Next Navigation Arrows (Flanking the Book Card) -->
            <button class="hero-arrow prev" id="heroPrev" aria-label="পূর্ববর্তী বই"><i class="fas fa-chevron-left"></i></button>
            <button class="hero-arrow next" id="heroNext" aria-label="পরবর্তী বই"><i class="fas fa-chevron-right"></i></button>
        </div>

        <!-- Top 3 Books Interactive Quick-Select Thumbnails Bar (Direct Tab Switcher) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-4">
            <?php foreach ($booksList as $tIdx => $tb): 
                $tPrice = (float)($tb['discount_price'] ?? $tb['price']);
            ?>
            <button type="button" 
                    onclick="window.heroGo(<?= $tIdx ?>, true)" 
                    id="book-tab-btn-<?= $tIdx ?>"
                    class="book-tab-btn p-3 rounded-2xl bg-[#fffefb] border-2 <?= $tIdx === 0 ? 'border-gold-rich shadow-md bg-amber-50/60' : 'border-[#e4dcce]' ?> hover:border-gold-rich text-left transition-all duration-200 flex items-center space-x-3 group">
                <div class="w-9 h-11 bg-emerald-900 border border-gold-rich/60 rounded-lg flex items-center justify-center text-gold-shimmer text-base shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                    <i class="fas fa-book-quran"></i>
                </div>
                <div class="overflow-hidden flex-1">
                    <span class="text-[10px] text-gold-deep font-bold block uppercase tracking-wider">
                        বেস্টসেলার #<?= \Core\BengaliHelper::toBengaliNumber($tIdx + 1) ?>
                    </span>
                    <h4 class="font-bold text-slate-900 text-xs truncate group-hover:text-emerald-deep transition">
                        <?= htmlspecialchars($tb['title']) ?>
                    </h4>
                    <span class="text-xs font-bold text-emerald-800 font-mono">
                        ৳<?= \Core\BengaliHelper::toBengaliNumber((int)$tPrice) ?>
                    </span>
                </div>
            </button>
            <?php endforeach; ?>
        </div>

    </div>
</div>

<!-- Vape-Inspired Smooth Slider JavaScript Engine -->
<script>
(function() {
    var slides = document.querySelectorAll('#heroCarousel .hero-slide');
    var dotsBox = document.getElementById('heroDots');
    if (!slides.length || !dotsBox) return;

    var idx = 0;
    var timer = null;
    var total = slides.length;
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Generate Dots
    slides.forEach(function(_, i) {
        var d = document.createElement('button');
        d.setAttribute('role', 'tab');
        d.setAttribute('aria-label', 'বই স্লাইড ' + (i + 1));
        d.addEventListener('click', function() { go(i, true); });
        dotsBox.appendChild(d);
    });
    var dots = dotsBox.querySelectorAll('button');

    function updateTabs(currentIdx) {
        for (var i = 0; i < total; i++) {
            var btn = document.getElementById('book-tab-btn-' + i);
            if (btn) {
                if (i === currentIdx) {
                    btn.classList.add('border-gold-rich', 'bg-amber-50/80', 'shadow-md');
                    btn.classList.remove('border-[#e4dcce]');
                } else {
                    btn.classList.remove('border-gold-rich', 'bg-amber-50/80', 'shadow-md');
                    btn.classList.add('border-[#e4dcce]');
                }
            }
        }
    }

    function go(n, user) {
        if (!slides[idx]) return;
        slides[idx].classList.add('is-exit-left');
        slides[idx].classList.remove('is-active');

        idx = (n + total) % total;

        slides.forEach(function(s, i) {
            if (i !== idx) s.classList.remove('is-exit-left');
        });

        slides[idx].classList.remove('is-exit-left');
        slides[idx].classList.add('is-active');

        dots.forEach(function(d, i) {
            d.classList.toggle('is-active', i === idx);
        });

        updateTabs(idx);

        if (user) restart();
    }
    window.heroGo = go;

    function restart() {
        if (reduceMotion) return;
        clearInterval(timer);
        timer = setInterval(function() {
            if (!document.hidden) go(idx + 1);
        }, 5500);
    }

    var prevBtn = document.getElementById('heroPrev');
    if (prevBtn) prevBtn.addEventListener('click', function() { go(idx - 1, true); });

    var nextBtn = document.getElementById('heroNext');
    if (nextBtn) nextBtn.addEventListener('click', function() { go(idx + 1, true); });

    // Touch Swipe & Mouse Drag Handling
    var shell = document.getElementById('heroShell');
    if (shell) {
        var startX = null, dragX = 0, dragging = false;
        shell.addEventListener('pointerdown', function(e) {
            if (e.target.closest('button, a')) return;
            startX = e.clientX;
            dragX = 0;
            dragging = true;
            try { shell.setPointerCapture(e.pointerId); } catch (err) {}
            if (slides[idx]) slides[idx].style.transition = 'none';
        });

        shell.addEventListener('pointermove', function(e) {
            if (!dragging || !slides[idx]) return;
            dragX = e.clientX - startX;
            slides[idx].style.transform = 'translateX(' + (dragX * 0.8) + 'px)';
        });

        function endDrag(commit) {
            if (!dragging) return;
            dragging = false;
            if (slides[idx]) {
                slides[idx].style.transition = '';
                slides[idx].style.transform = '';
            }
            if (commit && Math.abs(dragX) > 50) {
                go(idx + (dragX < 0 ? 1 : -1), true);
            } else {
                restart();
            }
            startX = null;
            dragX = 0;
        }

        shell.addEventListener('pointerup', function() { endDrag(true); });
        shell.addEventListener('pointercancel', function() { endDrag(false)); });
        shell.addEventListener('pointerenter', function() { clearInterval(timer); });
        shell.addEventListener('pointerleave', restart);
    }

    if (dots.length) dots[0].classList.add('is-active');
    updateTabs(0);
    restart();
})();
</script>

<!-- Quick Stats / Features Bar (Soft Warm Ivory with Gold Borders) -->
<div class="bg-[#fffdf9] border-2 border-[#e6dcce] py-8 relative z-20 shadow-[0_12px_35px_-8px_rgba(6,78,59,0.08)] -mt-8 mx-4 md:mx-auto max-w-5xl rounded-2xl">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center divide-x divide-[#ece3d4]">
        <div class="px-3">
            <div class="w-12 h-12 mx-auto rounded-full bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-deep text-2xl mb-2.5 shadow-sm">
                <i class="fas fa-book-quran"></i>
            </div>
            <h4 class="font-bold text-[#064e3b] text-base">সহীহ তিলাওয়াত</h4>
            <p class="text-xs text-slate-600 mt-0.5">বিশুদ্ধ তাজবীদ রুলস</p>
        </div>
        <div class="px-3">
            <div class="w-12 h-12 mx-auto rounded-full bg-amber-50 border border-amber-200/60 flex items-center justify-center text-gold-deep text-2xl mb-2.5 shadow-sm">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h4 class="font-bold text-[#064e3b] text-base">দক্ষ শিক্ষক</h4>
            <p class="text-xs text-slate-600 mt-0.5">প্রশিক্ষণপ্রাপ্ত মুয়াল্লিম</p>
        </div>
        <div class="px-3">
            <div class="w-12 h-12 mx-auto rounded-full bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-deep text-2xl mb-2.5 shadow-sm">
                <i class="fas fa-truck-fast"></i>
            </div>
            <h4 class="font-bold text-[#064e3b] text-base">সারা দেশে ডেলিভারি</h4>
            <p class="text-xs text-slate-600 mt-0.5">কুরিয়ারে নির্ভরযোগ্য সেবা</p>
        </div>
        <div class="px-3">
            <div class="w-12 h-12 mx-auto rounded-full bg-amber-50 border border-amber-200/60 flex items-center justify-center text-gold-deep text-2xl mb-2.5 shadow-sm">
                <i class="fas fa-headset"></i>
            </div>
            <h4 class="font-bold text-[#064e3b] text-base">সার্বক্ষণিক সাপোর্ট</h4>
            <p class="text-xs text-slate-600 mt-0.5">জেলা পরিচালক নেটওয়ার্ক</p>
        </div>
    </div>
</div>

<!-- Courses Grid Section (Warm Parchment Backdrop, Soft Ivory Cards) -->
<section class="bg-[#f4efe4]/70 py-16 border-b border-[#e8dfce]">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <div class="inline-flex items-center px-3.5 py-1 rounded-full bg-emerald-deep/10 text-emerald-deep text-xs font-bold mb-2 border border-emerald-deep/20">
                <i class="fas fa-quran mr-1.5 text-gold-deep"></i> নূরানী ও তাজবীদ বিভাগ
            </div>
            <h2 class="text-3xl font-extrabold text-emerald-night mb-3">আমাদের বিশেষ কোর্সসমূহ</h2>
            <div class="w-24 h-1 bg-gold-rich mx-auto rounded-full mb-4"></div>
            <p class="text-slate-700 max-w-2xl mx-auto text-sm leading-relaxed">
                সব বয়সীদের জন্য সহজে কুরআন শিক্ষার নির্ভরযোগ্য মাধ্যম। আজই আপনার উপযুক্ত কোর্সে ভর্তি হোন।
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Course Card 1 -->
            <div class="islamic-card p-6 flex flex-col h-full relative overflow-hidden group">
                <div class="w-14 h-14 bg-emerald-deep text-gold-shimmer rounded-2xl flex items-center justify-center text-2xl mb-4 shadow-md group-hover:bg-gold-rich group-hover:text-white transition-colors">
                    <i class="fas fa-book-open"></i>
                </div>
                <h3 class="text-xl font-bold text-emerald-night mb-2">সহীহ কুরআন শিক্ষা</h3>
                <p class="text-sm text-slate-600 mb-4 flex-grow">বড়দের জন্য শুদ্ধভাবে কুরআন তিলাওয়াত শেখার বিশেষ কোর্স।</p>
                <a href="<?= $baseUrl ?? '' ?>/courses" class="text-emerald-vibrant font-bold text-sm inline-flex items-center group-hover:text-gold-deep transition">
                    বিস্তারিত <i class="fas fa-arrow-right ml-1 text-xs transform group-hover:translate-x-1 transition"></i>
                </a>
            </div>
            
            <!-- Course Card 2 -->
            <div class="islamic-card p-6 flex flex-col h-full relative overflow-hidden group">
                <div class="w-14 h-14 bg-emerald-deep text-gold-shimmer rounded-2xl flex items-center justify-center text-2xl mb-4 shadow-md group-hover:bg-gold-rich group-hover:text-white transition-colors">
                    <i class="fas fa-child"></i>
                </div>
                <h3 class="text-xl font-bold text-emerald-night mb-2">হিফজুল কুরআন</h3>
                <p class="text-sm text-slate-600 mb-4 flex-grow">শিশুদের জন্য আদর্শ হিফজ বিভাগ, আন্তরিক পরিবেশে পাঠদান।</p>
                <a href="<?= $baseUrl ?? '' ?>/courses" class="text-emerald-vibrant font-bold text-sm inline-flex items-center group-hover:text-gold-deep transition">
                    বিস্তারিত <i class="fas fa-arrow-right ml-1 text-xs transform group-hover:translate-x-1 transition"></i>
                </a>
            </div>

            <!-- Course Card 3 -->
            <div class="islamic-card p-6 flex flex-col h-full relative overflow-hidden group">
                <div class="w-14 h-14 bg-emerald-deep text-gold-shimmer rounded-2xl flex items-center justify-center text-2xl mb-4 shadow-md group-hover:bg-gold-rich group-hover:text-white transition-colors">
                    <i class="fas fa-book-reader"></i>
                </div>
                <h3 class="text-xl font-bold text-emerald-night mb-2">নাজেরা বিভাগ</h3>
                <p class="text-sm text-slate-600 mb-4 flex-grow">কুরআন দেখে দ্রুত ও শুদ্ধভাবে পড়ার উন্নত প্রশিক্ষণ।</p>
                <a href="<?= $baseUrl ?? '' ?>/courses" class="text-emerald-vibrant font-bold text-sm inline-flex items-center group-hover:text-gold-deep transition">
                    বিস্তারিত <i class="fas fa-arrow-right ml-1 text-xs transform group-hover:translate-x-1 transition"></i>
                </a>
            </div>

            <!-- Course Card 4 -->
            <div class="islamic-card p-6 flex flex-col h-full relative overflow-hidden group">
                <div class="w-14 h-14 bg-emerald-deep text-gold-shimmer rounded-2xl flex items-center justify-center text-2xl mb-4 shadow-md group-hover:bg-gold-rich group-hover:text-white transition-colors">
                    <i class="fas fa-star-and-crescent"></i>
                </div>
                <h3 class="text-xl font-bold text-emerald-night mb-2">তাজবীদ কোর্স</h3>
                <p class="text-sm text-slate-600 mb-4 flex-grow">মাখরাজ ও সিফাত সহ কুরআন তিলাওয়াতের সৌন্দর্য বৃদ্ধি।</p>
                <a href="<?= $baseUrl ?? '' ?>/courses" class="text-emerald-vibrant font-bold text-sm inline-flex items-center group-hover:text-gold-deep transition">
                    বিস্তারিত <i class="fas fa-arrow-right ml-1 text-xs transform group-hover:translate-x-1 transition"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Prayer Times & Widget Section (Soothing Soft Sage Tint, Zero Harsh Glare) -->
<section class="bg-[#edf4ee] py-16 border-b border-[#dbe7de]">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            
            <!-- Widget Area -->
            <div>
                <div class="bg-[#fffefb] border-2 border-[#d9e4db] rounded-3xl p-6 md:p-8 shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-night opacity-5 rounded-bl-[100px] -z-0"></div>
                    
                    <div class="flex items-center justify-between mb-6 relative z-10 border-b border-[#e7eee8] pb-4">
                        <h3 class="text-2xl font-bold text-emerald-night flex items-center">
                            <i class="fas fa-mosque text-gold-rich mr-3"></i> নামাজের সময়সূচি
                        </h3>
                        <span class="text-xs bg-emerald-50 text-emerald-800 font-bold px-3 py-1 rounded-full border border-emerald-200">
                            ইসলামিক ফাউন্ডেশন
                        </span>
                    </div>
                    
                    <div class="mb-5 relative z-10">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">আপনার জেলা নির্বাচন করুন</label>
                        <select class="w-full px-4 py-2.5 border border-emerald-300/80 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-vibrant bg-[#f9fbf9] text-emerald-night font-semibold text-sm">
                            <option value="dhaka" selected>ঢাকা (Dhaka)</option>
                            <option value="chittagong">চট্টগ্রাম (Chittagong)</option>
                            <option value="sylhet">সিলেট (Sylhet)</option>
                            <option value="rajshahi">রাজশাহী (Rajshahi)</option>
                            <option value="khulna">খুলনা (Khulna)</option>
                            <option value="barisal">বরিশাল (Barisal)</option>
                            <option value="rangpur">রংপুর (Rangpur)</option>
                            <option value="mymensingh">ময়মনসিংহ (Mymensingh)</option>
                        </select>
                    </div>

                    <div class="space-y-2.5 relative z-10 text-sm">
                        <div class="flex justify-between items-center bg-[#f7f9f7] px-4 py-2.5 rounded-xl border border-[#e4ebe5]">
                            <span class="font-bold text-slate-800">ফজর</span>
                            <span class="font-bold text-emerald-vibrant">০৪:৪০ এএম</span>
                        </div>
                        <div class="flex justify-between items-center bg-[#f7f9f7] px-4 py-2.5 rounded-xl border border-[#e4ebe5]">
                            <span class="font-bold text-slate-800">যোহর</span>
                            <span class="font-bold text-slate-700">১২:১৫ পিএম</span>
                        </div>
                        <div class="flex justify-between items-center bg-emerald-deep text-white px-4 py-2.5 rounded-xl border border-gold-rich shadow-md">
                            <span class="font-bold flex items-center text-gold-shimmer">
                                <i class="fas fa-caret-right mr-2 text-gold-rich animate-pulse"></i> আসর (চলমান ওয়াক্ত)
                            </span>
                            <span class="font-bold text-gold-shimmer">০৪:৩০ পিএম</span>
                        </div>
                        <div class="flex justify-between items-center bg-[#f7f9f7] px-4 py-2.5 rounded-xl border border-[#e4ebe5]">
                            <span class="font-bold text-slate-800">মাগরিব / ইফতার</span>
                            <span class="font-bold text-slate-700">০৬:১০ পিএম</span>
                        </div>
                        <div class="flex justify-between items-center bg-[#f7f9f7] px-4 py-2.5 rounded-xl border border-[#e4ebe5]">
                            <span class="font-bold text-slate-800">ইশা</span>
                            <span class="font-bold text-slate-700">০৭:৩০ পিএম</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="space-y-6 lg:pl-8">
                <div class="inline-flex items-center px-3.5 py-1 rounded-full bg-emerald-100 text-emerald-deep text-xs font-bold">
                    <i class="fas fa-moon mr-2 text-gold-deep"></i> রমজান ও দৈনন্দিন ইবাদত
                </div>
                <h2 class="text-3xl md:text-4xl font-extrabold text-emerald-night leading-tight">
                    প্রতিদিনের নামাজের সঠিক সময়সূচি
                </h2>
                <p class="text-slate-700 text-base leading-relaxed">
                    আমাদের ৬৪ জেলার নামাজের সময়সূচি টুল ব্যবহার করে আপনার জেলার সঠিক সময় জেনে নিন। সাথে থাকছে সেহরী, ইফতার ও তাহাজ্জুদের নিখুঁত হিসাব।
                </p>
                <div class="pt-2 flex flex-col sm:flex-row gap-4">
                    <a href="<?= $baseUrl ?? '' ?>/prayer-times" class="bg-emerald-night hover:bg-emerald-deep text-white px-7 py-3 rounded-xl font-bold text-center transition shadow-md shadow-emerald-night/20">
                        <i class="fas fa-calendar-alt mr-2 text-gold-shimmer"></i> সম্পূর্ণ সময়সূচি দেখুন
                    </a>
                    <a href="<?= $baseUrl ?? '' ?>/tasbeeh" class="bg-[#fffdf9] border-2 border-emerald-deep text-emerald-night hover:bg-emerald-50 px-7 py-3 rounded-xl font-bold text-center transition">
                        <i class="fas fa-fingerprint mr-2 text-gold-deep"></i> ডিজিটাল তাসবীহ
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- District Directors Organizational Network Showcase Section (Soft Warm Sand Backdrop) -->
<section class="bg-[#f6f1e7] py-16 border-b border-[#e7ddcf]">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
            <div>
                <div class="inline-flex items-center px-3.5 py-1 rounded-full bg-gold-rich/10 text-gold-deep text-xs font-bold mb-3 border border-gold-rich/30">
                    <i class="fas fa-sitemap mr-2"></i> দেশব্যাপী শক্তিশালী সাংগঠনিক কাঠামো
                </div>
                <h2 class="text-3xl md:text-4xl font-extrabold text-emerald-night">
                    দেশব্যাপী জেলাভিত্তিক পরিচালক ও শিক্ষক নেটওয়ার্ক
                </h2>
                <div class="w-24 h-1 bg-gold-rich rounded-full mt-3 mb-4"></div>
                <p class="text-slate-700 max-w-2xl text-base leading-relaxed">
                    ক্ব-রিয়ানা কুরআন শিক্ষা সোসাইটির কার্যক্রম সারা বাংলাদেশের ৬৪টি জেলায় পরিচালিত হচ্ছে। প্রতিটি জেলায় যোগ্য ও অভিজ্ঞ জেলা পরিচালকবৃন্দের তত্ত্বাবধানে সহীহ কুরআন শিক্ষা কার্যক্রম পরিচালিত হয়।
                </p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="<?= $baseUrl ?? '' ?>/directors" class="inline-flex items-center bg-emerald-deep hover:bg-emerald-night text-white px-6 py-3 rounded-xl font-bold transition shadow-md shadow-emerald-deep/20">
                    <i class="fas fa-list-check mr-2 text-gold-shimmer"></i> সকল পরিচালক তালিকা দেখুন
                </a>
            </div>
        </div>

        <!-- Quick Stats Grid (Soft Ivory Boxes) -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
            <div class="bg-[#fffefb] p-5 rounded-2xl border border-[#e4dccb] shadow-sm flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-vibrant text-2xl shrink-0">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">জেলা পরিচালক</p>
                    <h4 class="text-2xl font-extrabold text-emerald-night"><?= !empty($totalDirectorsCount) ? $totalDirectorsCount : '৫৯' ?>+ জন</h4>
                </div>
            </div>
            <div class="bg-[#fffefb] p-5 rounded-2xl border border-[#e4dccb] shadow-sm flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-200/60 flex items-center justify-center text-gold-deep text-2xl shrink-0">
                    <i class="fas fa-map-location-dot"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">জেলা কভারেজ</p>
                    <h4 class="text-2xl font-extrabold text-slate-800">৬৪ টি জেলা</h4>
                </div>
            </div>
            <div class="bg-[#fffefb] p-5 rounded-2xl border border-[#e4dccb] shadow-sm flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-vibrant text-2xl shrink-0">
                    <i class="fas fa-chalkboard-user"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">প্রত্যয়িত শিক্ষক</p>
                    <h4 class="text-2xl font-extrabold text-emerald-night"><?= !empty($totalTeachersCount) ? $totalTeachersCount : '৩০০' ?>+ জন</h4>
                </div>
            </div>
            <div class="bg-[#fffefb] p-5 rounded-2xl border border-[#e4dccb] shadow-sm flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-200/60 flex items-center justify-center text-gold-deep text-2xl shrink-0">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">অধ্যয়নরত শিক্ষার্থী</p>
                    <h4 class="text-2xl font-extrabold text-slate-800">১০,০০০+</h4>
                </div>
            </div>
        </div>

        <!-- Featured Directors Cards (Soft Warm Ivory Cards) -->
        <?php if (!empty($featuredDirectors)): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($featuredDirectors as $dir): ?>
            <div class="bg-[#fffefb] rounded-2xl p-6 border border-[#e5ddcb] shadow-sm hover:shadow-xl hover:border-gold-rich/60 transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-12 h-12 rounded-full bg-emerald-deep text-gold-shimmer flex items-center justify-center font-bold text-lg border-2 border-gold-rich/40 shadow-inner shrink-0">
                            <?= mb_substr($dir['name'], 0, 1, 'UTF-8') ?>
                        </div>
                        <div class="overflow-hidden">
                            <h4 class="font-bold text-slate-800 text-base group-hover:text-emerald-deep transition line-clamp-1">
                                <?= htmlspecialchars($dir['name']) ?>
                            </h4>
                            <span class="text-xs text-gold-deep font-semibold block">
                                <?= htmlspecialchars($dir['designation']) ?>
                            </span>
                        </div>
                    </div>

                    <div class="bg-[#f7f4ea] rounded-xl p-3 mb-4 border border-[#e8dfcb]">
                        <p class="text-xs text-slate-500 mb-1 font-medium"><i class="fas fa-map-marker-alt text-emerald-vibrant mr-1"></i> দায়িত্বপ্রাপ্ত এলাকা:</p>
                        <p class="text-xs font-bold text-emerald-night line-clamp-2">
                            <?= htmlspecialchars($dir['district_name']) ?>
                        </p>
                    </div>
                </div>

                <div class="pt-3 border-t border-[#ede6d8] flex items-center justify-between">
                    <?php if ($dir['phone'] !== 'প্রযোজ্য নয়'): ?>
                    <a href="tel:<?= htmlspecialchars($dir['phone']) ?>" class="text-xs font-bold text-emerald-vibrant hover:text-emerald-deep flex items-center">
                        <i class="fas fa-phone mr-1"></i> কল করুন
                    </a>
                    <?php else: ?>
                    <span class="text-xs text-slate-400">কেন্দ্রীয় দফতর</span>
                    <?php endif; ?>

                    <a href="<?= $baseUrl ?? '' ?>/directors/<?= !empty($dir['slug']) ? htmlspecialchars($dir['slug']) : $dir['id'] ?>" class="text-xs font-bold text-gold-deep hover:text-gold-rich inline-flex items-center">
                        প্রোফাইল <i class="fas fa-arrow-right ml-1 text-[10px]"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- QR Code Scanning & Direct Lesson Gateway Banner (Royal Emerald & Warm Gold) -->
<div class="bg-gradient-to-r from-emerald-night via-emerald-deep to-emerald-night text-white py-14 relative overflow-hidden border-y-4 border-gold-rich">
    <div class="container mx-auto px-4 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-center">
            <div class="md:col-span-2 space-y-4 text-center md:text-left">
                <div class="inline-flex items-center px-3 py-1 rounded-full bg-gold-rich text-white text-xs font-bold uppercase tracking-wider">
                    <i class="fas fa-qrcode mr-2"></i> ডিজিটাল পাঠ নির্দেশিকা
                </div>
                <h3 class="text-2xl md:text-3xl font-bold text-gold-shimmer">
                    কারিয়ানা কুরআনের পৃষ্ঠার কিউআর কোড স্ক্যান করুন
                </h3>
                <p class="text-emerald-100 text-sm md:text-base max-w-xl leading-relaxed">
                    আমাদের মুদ্রিত কারিয়ানা কুরআন ও কায়দার প্রতিটি পাঠের সাথে রয়েছে বিশেষ কিউআর কোড। মোবাইল দিয়ে স্ক্যান করলেই সরাসরি সেই পৃষ্ঠার বিশুদ্ধ তাজবীদ ও উচ্চারণের ভিডিও পাঠ চালু হবে।
                </p>
            </div>
            <div class="flex flex-col sm:flex-row md:flex-col lg:flex-row items-center justify-center gap-4">
                <a href="<?= $baseUrl ?? '' ?>/scan" class="w-full sm:w-auto bg-gold-rich hover:bg-gold-deep text-white px-6 py-3 rounded-xl font-bold text-center shadow-lg transition flex items-center justify-center">
                    <i class="fas fa-camera mr-2"></i> কিউআর স্ক্যান করুন
                </a>
                <a href="<?= $baseUrl ?? '' ?>/quran" class="w-full sm:w-auto bg-white/10 hover:bg-white/20 border border-white/20 text-white px-6 py-3 rounded-xl font-bold text-center transition flex items-center justify-center">
                    <i class="fas fa-book-quran mr-2 text-gold-shimmer"></i> ওয়েব রিডার চালু করুন
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Latest Islamic Articles & Blog Section (Soft Parchment Background) -->
<?php if (!empty($recentPosts)): ?>
<section class="bg-[#f8f5ee] py-16">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
            <div>
                <div class="inline-flex items-center px-3.5 py-1 rounded-full bg-emerald-100 text-emerald-deep text-xs font-bold mb-3">
                    <i class="fas fa-pen-nib mr-2 text-gold-deep"></i> জ্ঞান ও তিলাওয়াত সহায়িকা
                </div>
                <h2 class="text-3xl md:text-4xl font-extrabold text-emerald-night">
                    সর্বশেষ ইসলামী ব্লগ ও টিপস
                </h2>
                <div class="w-24 h-1 bg-gold-rich rounded-full mt-3 mb-4"></div>
                <p class="text-slate-700 max-w-2xl text-base leading-relaxed">
                    সহীহ কুরআন তিলাওয়াতের নিয়মাবলী, তাজবীদের গুরুত্ব এবং দৈনন্দিন জীবনের প্রয়োজনীয় ইসলামী মাসায়েল সংক্রান্ত আর্টিকেলে সমৃদ্ধ আমাদের ব্লগ।
                </p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="<?= $baseUrl ?? '' ?>/blog" class="inline-flex items-center text-emerald-vibrant hover:text-emerald-deep font-bold transition">
                    সকল ব্লগ পড়ুন <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($recentPosts as $post): ?>
            <article class="bg-[#fffefb] rounded-2xl overflow-hidden border border-[#e5ddcb] shadow-sm hover:shadow-xl hover:border-gold-rich/40 transition-all duration-300 flex flex-col group">
                <div class="h-48 bg-emerald-deep relative overflow-hidden">
                    <img src="<?= htmlspecialchars($post['thumbnail_url'] ?? 'https://via.placeholder.com/600x400/064e3b/ffffff?text=Kariana+Quran+Blog') ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" onerror="this.src='https://via.placeholder.com/600x400/064e3b/ffffff?text=Kariana+Quran+Blog'">
                    <div class="absolute top-3 left-3 bg-emerald-night/80 backdrop-blur-sm text-gold-shimmer text-xs px-2.5 py-1 rounded-full font-bold">
                        <i class="fas fa-calendar-alt mr-1"></i> <?= date('d M, Y', strtotime($post['created_at'])) ?>
                    </div>
                </div>
                <div class="p-6 flex flex-col flex-grow justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-emerald-night mb-3 group-hover:text-gold-deep transition line-clamp-2">
                            <a href="<?= $baseUrl ?? '' ?>/blog/<?= htmlspecialchars($post['slug']) ?>">
                                <?= htmlspecialchars($post['title']) ?>
                            </a>
                        </h3>
                        <p class="text-sm text-slate-600 line-clamp-3 mb-4 leading-relaxed">
                            <?= htmlspecialchars($post['excerpt'] ?? mb_substr(strip_tags($post['content'] ?? ''), 0, 120, 'UTF-8') . '...') ?>
                        </p>
                    </div>
                    <div class="pt-4 border-t border-[#ede6d8] flex items-center justify-between">
                        <span class="text-xs text-slate-500 font-medium">কারিয়ানা রিসার্চ উইং</span>
                        <a href="<?= $baseUrl ?? '' ?>/blog/<?= htmlspecialchars($post['slug']) ?>" class="text-emerald-vibrant hover:text-emerald-deep font-bold text-sm inline-flex items-center">
                            সম্পূর্ণ পড়ুন <i class="fas fa-arrow-right ml-1 text-xs"></i>
                        </a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
