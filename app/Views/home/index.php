<!-- ============================================================
     VAPE-INSPIRED SMOOTH HERO SLIDER & 3D BOOK SHOWCASE (100VH IMMERSIVE)
     ============================================================ -->
<div id="heroSectionWrapper" class="bg-emerald-deep px-3 sm:px-4 relative overflow-hidden">
    <div class="container mx-auto max-w-4xl relative z-10 flex flex-col items-center w-full">
        
        <!-- Main Vape-Style Hero Shell -->
        <div class="hero-shell relative w-full flex flex-col items-center" id="heroShell">
            <!-- Background Ambient Glow & Geometric Rings -->
            <div class="hero-bg-shapes" aria-hidden="true">
                <div class="hero-shape-orb-1"></div>
                <div class="hero-shape-ring"></div>
            </div>

            <!-- Slide Carousel Stack -->
            <div id="heroCarousel" class="relative z-10">
                <?php 
                $booksList = !empty($featuredBooks) ? $featuredBooks : [];
                $slidePills = [
                    0 => ['pill' => '১ম বেস্টসেলার - ফ্ল্যাগশিপ সংস্করণ', 'chipA' => '১২টি তাজবীদ সংকেত', 'chipB' => 'কিউআর ভিডিও লেসন'],
                    1 => ['pill' => '২য় বেস্টসেলার - ৩০তম পারা আমপারা', 'chipA' => '১২টি তাজবীদ সংকেত', 'chipB' => 'কিউআর ভিডিও লেসন'],
                    2 => ['pill' => '৩য় বেস্টসেলার - ৩০ দিনে কুরআন শিক্ষা', 'chipA' => '১২টি তাজবীদ সংকেত', 'chipB' => 'কিউআর ভিডিও লেসন'],
                    3 => ['pill' => '৪র্থ বেস্টসেলার - মুয়াল্লিম গাইডবুক', 'chipA' => '১২টি তাজবীদ সংকেত', 'chipB' => 'কিউআর ভিডিও লেসন'],
                    4 => ['pill' => '৫ম বেস্টসেলার - হিফজুল কুরআন সহায়িকা', 'chipA' => '১২টি তাজবীদ সংকেত', 'chipB' => 'কিউআর ভিডিও লেসন'],
                ];

                foreach ($booksList as $idx => $b):
                    $discPrice = (float)($b['discount_price'] ?? $b['price']);
                    $origPrice = (float)$b['price'];
                    $hasDiscount = ($discPrice > 0 && $discPrice < $origPrice);
                    $saveAmount = (int)($origPrice - $discPrice);
                    $bNumber = \Core\BengaliHelper::toBengaliNumber($idx + 1);
                    $pills = $slidePills[$idx] ?? [
                        'pill'  => 'বেস্টসেলার #' . $bNumber . ' - বিশেষ প্রকাশনা',
                        'chipA' => 'নূরানী সংকেত',
                        'chipB' => 'ভিডিও পাঠ'
                    ];
                    $coverImg = !empty($b['cover_image']) ? $b['cover_image'] : 'assets/images/kariana_quran_flagship.jpg';
                ?>
                <article class="hero-slide <?= $idx === 0 ? 'is-active' : '' ?>" 
                         data-index="<?= $idx ?>"
                         itemscope itemtype="https://schema.org/Book">
                    <div class="flex flex-col items-center justify-center w-full text-center max-w-2xl mx-auto z-20">
                        
                        <!-- Top Pill: Bestseller Flagship Edition (Pure SVG, Zero Emojis) -->
                        <div class="mb-1.5 sm:mb-2">
                            <span class="slide-pill">
                                <svg class="w-3.5 h-3.5 mr-1.5 text-amber-300 inline-block shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <?= htmlspecialchars($pills['pill']) ?>
                            </span>
                        </div>

                        <!-- Single-Line Clean Headline -->
                        <h1 class="text-base sm:text-2xl md:text-3xl font-black text-white tracking-tight drop-shadow-md truncate max-w-xl mx-auto mb-1.5 sm:mb-3 px-2" itemprop="name">
                            <?= htmlspecialchars($b['title']) ?>
                        </h1>

                        <!-- Center 3D Floating Book Art (Generated AI Realistic 3D Mockup) -->
                        <div class="py-1 relative">
                            <div class="hero-art relative">
                                <div class="halo"></div>

                                <!-- Orbit Floating Chips with SVGs -->
                                <span class="orbit-chip chip-a">
                                    <span class="pulse"></span>
                                    <svg class="w-3.5 h-3.5 text-emerald-400 mr-1.5 inline-block shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <?= htmlspecialchars($pills['chipA']) ?>
                                </span>
                                <span class="orbit-chip chip-b">
                                    <svg class="w-3.5 h-3.5 text-amber-300 mr-1.5 inline-block shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                    <?= htmlspecialchars($pills['chipB']) ?>
                                </span>

                                <!-- Authentic 3D Book Shape Card (Exact Match: media_1790102812320.png) -->
                                <div class="hero-book-card relative flex flex-col justify-between select-none">
                                    <!-- Top Row: Category Tag & Book Index -->
                                    <div class="flex items-center justify-between z-10">
                                        <span class="text-[10px] sm:text-[11px] bg-gradient-to-r from-amber-600 to-amber-700 text-white font-bold px-2.5 py-0.5 rounded-full shadow-sm border border-amber-300/30">
                                            কারিয়ানা নূরানী
                                        </span>
                                        <span class="text-[11px] sm:text-xs text-amber-300 font-mono font-bold tracking-wider">
                                            <?= ($idx + 1) ?>/<?= count($booksList) ?>
                                        </span>
                                    </div>

                                    <!-- Center Emblem & Dynamic Title -->
                                    <div class="text-center my-auto py-2 sm:py-3 z-10 px-1">
                                        <div class="w-12 h-12 sm:w-14 sm:h-14 mx-auto bg-amber-400/15 border-2 border-amber-400/60 rounded-full flex items-center justify-center text-amber-300 mb-1.5 sm:mb-2 shadow-[inset_0_0_10px_rgba(251,191,36,0.3)]">
                                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                        </div>
                                        <h3 class="font-black text-xs sm:text-sm md:text-base text-white line-clamp-2 px-1 leading-snug drop-shadow-sm">
                                            <?= htmlspecialchars($b['title']) ?>
                                        </h3>
                                        <span class="text-[10px] sm:text-[11px] text-emerald-200/90 font-medium block mt-1 tracking-wide">
                                            তাজবীদ কালার কোডেড
                                        </span>
                                    </div>

                                    <!-- Bottom Feature Tag & Price in Gold -->
                                    <div class="pt-1.5 sm:pt-2 border-t border-amber-400/30 flex items-center justify-between text-xs z-10">
                                        <span class="text-emerald-300/90 text-[10px] sm:text-[11px] font-medium">নূরানী পদ্ধতি</span>
                                        <span class="text-amber-300 font-black text-xs sm:text-base font-mono tracking-tight drop-shadow">
                                            ৳<?= (int)$discPrice ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden Microdata for Google Schema.org Offer (SEO Preserved) -->
                        <div class="hidden" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                            <meta itemprop="priceCurrency" content="BDT">
                            <meta itemprop="price" content="<?= $discPrice ?>">
                            <link itemprop="availability" href="https://schema.org/InStock">
                        </div>

                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <!-- Unified Hero Navigation & Action Controls Bar -->
            <div class="hero-controls-bar">
                <!-- Sleek Compact Prev Arrow Button -->
                <button type="button" 
                        class="hero-nav-btn prev group" 
                        id="heroPrev" 
                        onclick="if(window.heroPrev) { window.heroPrev(); } else if(window.heroGo) { window.heroGo(-1, true, true); }" 
                        aria-label="পূর্ববর্তী বই">
                    <svg class="w-4 h-4 text-amber-300 group-hover:text-white transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <!-- Centered Action Buttons (Pure Clean Text, Zero Random Price, Dynamic per Active Book) -->
                <div class="hero-action-group" id="heroActionGroup">
                    <a id="heroOrderBtn" 
                       href="https://wa.me/8801711756391?text=<?= urlencode('আসসালামু আলাইকুম, আমি কারিয়ানা ওয়েবসাইট থেকে "' . ($booksList[0]['title'] ?? 'কারিয়ানা কুরআন') . '" বইটি অর্ডার করতে চাই।') ?>" 
                       target="_blank"
                       rel="noopener noreferrer"
                       class="hero-btn-primary group">
                        <svg class="w-4 h-4 mr-1.5 text-emerald-100 shrink-0 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                        <span>অর্ডার করুন</span>
                    </a>

                    <a id="heroDetailBtn" 
                       href="<?= $baseUrl ?? '' ?>/books/<?= htmlspecialchars($booksList[0]['slug'] ?? 'kariana-quran-flagship') ?>" 
                       class="hero-btn-secondary group">
                        <svg class="w-4 h-4 mr-1.5 text-amber-300 shrink-0 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <span>বিস্তারিত দেখুন</span>
                    </a>
                </div>

                <!-- Sleek Compact Next Arrow Button -->
                <button type="button" 
                        class="hero-nav-btn next group" 
                        id="heroNext" 
                        onclick="if(window.heroNext) { window.heroNext(); } else if(window.heroGo) { window.heroGo(1, true, true); }" 
                        aria-label="পরবর্তী বই">
                    <svg class="w-4 h-4 text-amber-300 group-hover:text-white transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <!-- Discrete, Refined Slide Indicator Dots (Small & Delicate, Positioned Underneath) -->
            <div class="hero-dots-container">
                <div class="hero-dots" id="heroDots" role="tablist" aria-label="বই স্লাইডার তালিকা"></div>
            </div>

            <!-- Refined Touch-Friendly Scroll Down Indicator (Clean & Natural, Zero Awkward Gaps) -->
            <div class="hero-scroll-indicator text-center mt-3 sm:mt-5 z-20">
                <button type="button" 
                        id="heroScrollBtn"
                        onclick="smoothScrollToContent();" 
                        class="group inline-flex flex-col items-center justify-center text-emerald-200 hover:text-amber-300 transition-all focus:outline-none"
                        aria-label="নিচের সেকশনে যান">
                    <span class="text-xs font-bold tracking-wider text-amber-300/90 group-hover:text-amber-300 mb-1 flex items-center gap-1.5 drop-shadow">
                        নিচে স্ক্রল করুন
                    </span>
                    <div class="w-6 h-6 rounded-full bg-emerald-900/80 border border-amber-400/50 flex items-center justify-center group-hover:border-amber-300 group-hover:bg-emerald-800 transition-all shadow-sm">
                        <svg class="w-3.5 h-3.5 text-amber-300 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>
            </div>
        </div>

    </div>
</div>

<!-- Vape-Inspired Smooth Slider JavaScript Engine (Dynamic 1 to 10+ Books with 3D Transitions) -->
<script>
(function() {
    var slides = document.querySelectorAll('#heroCarousel .hero-slide');
    var dotsBox = document.getElementById('heroDots');
    var orderBtn = document.getElementById('heroOrderBtn');
    var detailBtn = document.getElementById('heroDetailBtn');
    var actionGroup = document.getElementById('heroActionGroup');
    if (!slides.length || !dotsBox) return;

    var booksData = <?= json_encode(array_map(function($b) use ($baseUrl) {
        return [
            'title' => $b['title'],
            'slug'  => $b['slug'],
            'url'   => ($baseUrl ?? '') . '/books/' . $b['slug'],
            'waUrl' => 'https://wa.me/8801711756391?text=' . rawurlencode('আসসালামু আলাইকুম, আমি কারিয়ানা ওয়েবসাইট থেকে "' . $b['title'] . '" বইটি অর্ডার করতে চাই।')
        ];
    }, $booksList), JSON_UNESCAPED_UNICODE) ?>;

    var idx = 0;
    var timer = null;
    var total = slides.length;
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var isTransitioning = false;

    // Generate Dynamic Dots for 1 to 10+ books
    dotsBox.innerHTML = '';
    slides.forEach(function(_, i) {
        var d = document.createElement('button');
        d.setAttribute('role', 'tab');
        d.setAttribute('aria-label', 'বই স্লাইড ' + (i + 1));
        if (i === 0) d.classList.add('is-active');
        d.addEventListener('click', function(e) { 
            e.stopPropagation();
            go(i, true, false); 
        });
        dotsBox.appendChild(d);
    });
    var dots = dotsBox.querySelectorAll('button');

    function updateActionButtons(nextIndex) {
        if (!booksData || !booksData[nextIndex]) return;
        var book = booksData[nextIndex];

        if (actionGroup) {
            actionGroup.classList.add('is-changing');
        }

        setTimeout(function() {
            if (orderBtn) {
                orderBtn.href = book.waUrl;
            }
            if (detailBtn) {
                detailBtn.href = book.url;
            }
            if (actionGroup) {
                actionGroup.classList.remove('is-changing');
            }
        }, 150);
    }

    function go(target, user, isRelative) {
        if (!slides.length || isTransitioning) return;
        var nextIdx;
        var direction = 1; // 1 = next, -1 = prev

        if (isRelative) {
            direction = target >= 0 ? 1 : -1;
            nextIdx = (idx + target + total) % total;
        } else {
            direction = target >= idx ? 1 : -1;
            nextIdx = (target + total) % total;
        }

        if (nextIdx === idx && user && !isRelative) return;

        isTransitioning = true;
        var currentSlide = slides[idx];
        var nextSlide = slides[nextIdx];

        var exitClass = direction > 0 ? 'is-exit-left' : 'is-exit-right';
        var enterClass = direction > 0 ? 'is-enter-right' : 'is-enter-left';

        // Prepare next slide position before entering
        nextSlide.classList.remove('is-active', 'is-exit-left', 'is-exit-right');
        nextSlide.classList.add(enterClass);
        void nextSlide.offsetWidth; // Force CSS reflow

        // Transition out current slide
        currentSlide.classList.remove('is-active');
        currentSlide.classList.add(exitClass);

        // Transition in next slide
        nextSlide.classList.remove(enterClass);
        nextSlide.classList.add('is-active');

        // Update active dots
        idx = nextIdx;
        dots.forEach(function(d, i) {
            d.classList.toggle('is-active', i === idx);
        });

        // Update dynamic buttons link with micro-animation
        updateActionButtons(idx);

        setTimeout(function() {
            currentSlide.classList.remove(exitClass);
            isTransitioning = false;
        }, 650);

        if (user) restart();
    }

    window.heroGo = function(target, user) { go(target, user, false); };
    window.heroPrev = function() { go(-1, true, true); };
    window.heroNext = function() { go(1, true, true); };

    function restart() {
        if (reduceMotion || total <= 1) return;
        clearInterval(timer);
        timer = setInterval(function() {
            if (!document.hidden) go(1, false, true);
        }, 5500);
    }

    var prevBtn = document.getElementById('heroPrev');
    if (prevBtn) {
        prevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            window.heroPrev();
        });
    }

    var nextBtn = document.getElementById('heroNext');
    if (nextBtn) {
        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            window.heroNext();
        });
    }

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
            slides[idx].style.transform = 'perspective(1200px) translateX(' + (dragX * 0.7) + 'px) scale(0.98)';
        });

        function endDrag(commit) {
            if (!dragging) return;
            dragging = false;
            if (slides[idx]) {
                slides[idx].style.transition = '';
                slides[idx].style.transform = '';
            }
            if (commit && Math.abs(dragX) > 40) {
                go(dragX < 0 ? 1 : -1, true, true);
            } else {
                restart();
            }
            startX = null;
            dragX = 0;
        }

        shell.addEventListener('pointerup', function() { endDrag(true); });
        shell.addEventListener('pointercancel', function() { endDrag(false); });
        shell.addEventListener('pointerenter', function() { clearInterval(timer); });
        shell.addEventListener('pointerleave', restart);
    }

    if (dots.length) dots[0].classList.add('is-active');
    restart();
})();

// Smooth Section Handover & Reveal on Scroll or Button Click
window.smoothScrollToContent = function() {
    var main = document.getElementById('mainContentSection');
    if (main) {
        main.classList.add('is-revealed');
        main.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
};

(function() {
    var main = document.getElementById('mainContentSection');
    if (!main) return;

    // Reveal mainContentSection when it enters viewport
    if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    main.classList.add('is-revealed');
                    observer.unobserve(main);
                }
            });
        }, { threshold: 0.05, rootMargin: '0px 0px -20px 0px' });
        observer.observe(main);
    } else {
        main.classList.add('is-revealed');
    }
})();
</script>

<!-- Quick Stats / Features Bar (Soft Warm Ivory with Gold Borders) -->
<div id="mainContentSection" class="bg-[#fffdf9] border-2 border-[#e6dcce] py-8 relative z-20 shadow-[0_12px_35px_-8px_rgba(6,78,59,0.08)] mt-0 md:mt-8 mx-4 md:mx-auto max-w-5xl rounded-2xl">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center divide-x divide-[#ece3d4]">
        <div class="px-3">
            <div class="w-12 h-12 mx-auto rounded-full bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-deep mb-2.5 shadow-sm">
                <svg class="w-6 h-6 text-emerald-deep" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <h4 class="font-bold text-[#064e3b] text-base">সহীহ তিলাওয়াত</h4>
            <p class="text-xs text-slate-600 mt-0.5">বিশুদ্ধ তাজবীদ রুলস</p>
        </div>
        <div class="px-3">
            <div class="w-12 h-12 mx-auto rounded-full bg-amber-50 border border-amber-200/60 flex items-center justify-center text-gold-deep mb-2.5 shadow-sm">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                </svg>
            </div>
            <h4 class="font-bold text-[#064e3b] text-base">দক্ষ শিক্ষক</h4>
            <p class="text-xs text-slate-600 mt-0.5">প্রশিক্ষণপ্রাপ্ত মুয়াল্লিম</p>
        </div>
        <div class="px-3">
            <div class="w-12 h-12 mx-auto rounded-full bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-deep mb-2.5 shadow-sm">
                <svg class="w-6 h-6 text-emerald-deep" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                </svg>
            </div>
            <h4 class="font-bold text-[#064e3b] text-base">সারা দেশে ডেলিভারি</h4>
            <p class="text-xs text-slate-600 mt-0.5">কুরিয়ারে নির্ভরযোগ্য সেবা</p>
        </div>
        <div class="px-3">
            <div class="w-12 h-12 mx-auto rounded-full bg-amber-50 border border-amber-200/60 flex items-center justify-center text-gold-deep mb-2.5 shadow-sm">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
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
                <svg class="w-4 h-4 text-amber-600 mr-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                নূরানী ও তাজবীদ বিভাগ
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
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-emerald-night mb-2">সহীহ কুরআন শিক্ষা</h3>
                <p class="text-sm text-slate-600 mb-4 flex-grow">বড়দের জন্য শুদ্ধভাবে কুরআন তিলাওয়াত শেখার বিশেষ কোর্স।</p>
                <a href="<?= $baseUrl ?? '' ?>/courses" class="text-emerald-vibrant font-bold text-sm inline-flex items-center group-hover:text-gold-deep transition">
                    বিস্তারিত 
                    <svg class="w-3.5 h-3.5 ml-1 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            
            <!-- Course Card 2 -->
            <div class="islamic-card p-6 flex flex-col h-full relative overflow-hidden group">
                <div class="w-14 h-14 bg-emerald-deep text-gold-shimmer rounded-2xl flex items-center justify-center text-2xl mb-4 shadow-md group-hover:bg-gold-rich group-hover:text-white transition-colors">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-emerald-night mb-2">হিফজুল কুরআন</h3>
                <p class="text-sm text-slate-600 mb-4 flex-grow">শিশুদের জন্য আদর্শ হিফজ বিভাগ, আন্তরিক পরিবেশে পাঠদান।</p>
                <a href="<?= $baseUrl ?? '' ?>/courses" class="text-emerald-vibrant font-bold text-sm inline-flex items-center group-hover:text-gold-deep transition">
                    বিস্তারিত 
                    <svg class="w-3.5 h-3.5 ml-1 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <!-- Course Card 3 -->
            <div class="islamic-card p-6 flex flex-col h-full relative overflow-hidden group">
                <div class="w-14 h-14 bg-emerald-deep text-gold-shimmer rounded-2xl flex items-center justify-center text-2xl mb-4 shadow-md group-hover:bg-gold-rich group-hover:text-white transition-colors">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-emerald-night mb-2">নাজেরা বিভাগ</h3>
                <p class="text-sm text-slate-600 mb-4 flex-grow">কুরআন দেখে দ্রুত ও শুদ্ধভাবে পড়ার উন্নত প্রশিক্ষণ।</p>
                <a href="<?= $baseUrl ?? '' ?>/courses" class="text-emerald-vibrant font-bold text-sm inline-flex items-center group-hover:text-gold-deep transition">
                    বিস্তারিত 
                    <svg class="w-3.5 h-3.5 ml-1 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <!-- Course Card 4 -->
            <div class="islamic-card p-6 flex flex-col h-full relative overflow-hidden group">
                <div class="w-14 h-14 bg-emerald-deep text-gold-shimmer rounded-2xl flex items-center justify-center text-2xl mb-4 shadow-md group-hover:bg-gold-rich group-hover:text-white transition-colors">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-emerald-night mb-2">তাজবীদ কোর্স</h3>
                <p class="text-sm text-slate-600 mb-4 flex-grow">মাখরাজ ও সিফাত সহ কুরআন তিলাওয়াতের সৌন্দর্য বৃদ্ধি।</p>
                <a href="<?= $baseUrl ?? '' ?>/courses" class="text-emerald-vibrant font-bold text-sm inline-flex items-center group-hover:text-gold-deep transition">
                    বিস্তারিত 
                    <svg class="w-3.5 h-3.5 ml-1 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
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
                            <svg class="w-6 h-6 text-gold-rich mr-2.5 inline-block shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            নামাজের সময়সূচি
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
                                <svg class="w-4 h-4 mr-1 text-gold-rich animate-pulse inline-block shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                                আসর (চলমান ওয়াক্ত)
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
                    <svg class="w-4 h-4 mr-1.5 text-gold-deep inline-block shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                    </svg>
                    রমজান ও দৈনন্দিন ইবাদত
                </div>
                <h2 class="text-3xl md:text-4xl font-extrabold text-emerald-night leading-tight">
                    প্রতিদিনের নামাজের সঠিক সময়সূচি
                </h2>
                <p class="text-slate-700 text-base leading-relaxed">
                    আমাদের ৬৪ জেলার নামাজের সময়সূচি টুল ব্যবহার করে আপনার জেলার সঠিক সময় জেনে নিন। সাথে থাকছে সেহরী, ইফতার ও তাহাজ্জুদের নিখুঁত হিসাব।
                </p>
                <div class="pt-2 flex flex-col sm:flex-row gap-4">
                    <a href="<?= $baseUrl ?? '' ?>/prayer-times" class="bg-emerald-night hover:bg-emerald-deep text-white px-7 py-3 rounded-xl font-bold text-center transition shadow-md shadow-emerald-night/20 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2 text-gold-shimmer inline-block shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        সম্পূর্ণ সময়সূচি দেখুন
                    </a>
                    <a href="<?= $baseUrl ?? '' ?>/tasbeeh" class="bg-[#fffdf9] border-2 border-emerald-deep text-emerald-night hover:bg-emerald-50 px-7 py-3 rounded-xl font-bold text-center transition flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2 text-gold-deep inline-block shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                        </svg>
                        ডিজিটাল তাসবীহ
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
                    <svg class="w-4 h-4 mr-1.5 inline-block shrink-0 text-gold-deep" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    দেশব্যাপী শক্তিশালী সাংগঠনিক কাঠামো
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
                    <svg class="w-5 h-5 mr-2 text-gold-shimmer inline-block shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    সকল পরিচালক তালিকা দেখুন
                </a>
            </div>
        </div>

        <!-- Quick Stats Grid (Soft Ivory Boxes) -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
            <div class="bg-[#fffefb] p-5 rounded-2xl border border-[#e4dccb] shadow-sm flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-vibrant text-2xl shrink-0">
                    <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">জেলা পরিচালক</p>
                    <h4 class="text-2xl font-extrabold text-emerald-night"><?= !empty($totalDirectorsCount) ? $totalDirectorsCount : '৫৯' ?>+ জন</h4>
                </div>
            </div>
            <div class="bg-[#fffefb] p-5 rounded-2xl border border-[#e4dccb] shadow-sm flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-200/60 flex items-center justify-center text-gold-deep text-2xl shrink-0">
                    <svg class="w-6 h-6 text-gold-deep" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">জেলা কভারেজ</p>
                    <h4 class="text-2xl font-extrabold text-slate-800">৬৪ টি জেলা</h4>
                </div>
            </div>
            <div class="bg-[#fffefb] p-5 rounded-2xl border border-[#e4dccb] shadow-sm flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-vibrant text-2xl shrink-0">
                    <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">প্রত্যয়িত শিক্ষক</p>
                    <h4 class="text-2xl font-extrabold text-emerald-night"><?= !empty($totalTeachersCount) ? $totalTeachersCount : '৩০০' ?>+ জন</h4>
                </div>
            </div>
            <div class="bg-[#fffefb] p-5 rounded-2xl border border-[#e4dccb] shadow-sm flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-200/60 flex items-center justify-center text-gold-deep text-2xl shrink-0">
                    <svg class="w-6 h-6 text-gold-deep" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 font-medium">অধ্যয়নরত শিক্ষার্থী</p>
                    <h4 class="text-2xl font-extrabold text-slate-800">১০,০০০+</h4>
                </div>
            </div>
        </div>

        <!-- Featured Directors Cards (Soft Warm Ivory Cards with Dignified Scholar Photo) -->
        <?php if (!empty($featuredDirectors)): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($featuredDirectors as $dir): ?>
            <div class="bg-[#fffefb] rounded-2xl p-6 border border-[#e5ddcb] shadow-sm hover:shadow-xl hover:border-gold-rich/60 transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="relative w-14 h-14 rounded-full overflow-hidden border-2 border-gold-rich/60 shadow-md shrink-0 bg-emerald-deep flex items-center justify-center">
                            <img src="<?= $baseUrl ?? '' ?>/assets/images/director_scholar_portrait.jpg" 
                                 alt="<?= htmlspecialchars($dir['name']) ?>" 
                                 class="w-full h-full object-cover object-top"
                                 onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden'); this.nextElementSibling.classList.add('flex');">
                            <div class="w-full h-full hidden items-center justify-center font-bold text-lg text-gold-shimmer">
                                <?= mb_substr($dir['name'], 0, 1, 'UTF-8') ?>
                            </div>
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
                        <p class="text-xs text-slate-500 mb-1 font-medium flex items-center">
                            <svg class="w-3.5 h-3.5 text-emerald-vibrant mr-1 inline-block shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            দায়িত্বপ্রাপ্ত এলাকা:
                        </p>
                        <p class="text-xs font-bold text-emerald-night line-clamp-2">
                            <?= htmlspecialchars($dir['district_name']) ?>
                        </p>
                    </div>
                </div>

                <div class="pt-3 border-t border-[#ede6d8] flex items-center justify-between">
                    <?php if ($dir['phone'] !== 'প্রযোজ্য নয়'): ?>
                    <a href="tel:<?= htmlspecialchars($dir['phone']) ?>" class="text-xs font-bold text-emerald-vibrant hover:text-emerald-deep flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1 inline-block shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        কল করুন
                    </a>
                    <?php else: ?>
                    <span class="text-xs text-slate-400">কেন্দ্রীয় দফতর</span>
                    <?php endif; ?>

                    <a href="<?= $baseUrl ?? '' ?>/directors/<?= !empty($dir['slug']) ? htmlspecialchars($dir['slug']) : $dir['id'] ?>" class="text-xs font-bold text-gold-deep hover:text-gold-rich inline-flex items-center">
                        প্রোফাইল 
                        <svg class="w-3 h-3 ml-1 inline-block shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
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
                    <svg class="w-4 h-4 mr-1.5 inline-block shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                    ডিজিটাল পাঠ নির্দেশিকা
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
                    <svg class="w-5 h-5 mr-2 inline-block shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    কিউআর স্ক্যান করুন
                </a>
                <a href="<?= $baseUrl ?? '' ?>/quran" class="w-full sm:w-auto bg-white/10 hover:bg-white/20 border border-white/20 text-white px-6 py-3 rounded-xl font-bold text-center transition flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2 text-gold-shimmer inline-block shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    ওয়েব রিডার চালু করুন
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
                    <svg class="w-4 h-4 mr-1.5 text-gold-deep inline-block shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    জ্ঞান ও তিলাওয়াত সহায়িকা
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
                    সকল ব্লগ পড়ুন 
                    <svg class="w-4 h-4 ml-1.5 inline-block shrink-0 text-emerald-vibrant" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($recentPosts as $post): ?>
            <article class="bg-[#fffefb] rounded-2xl overflow-hidden border border-[#e5ddcb] shadow-sm hover:shadow-xl hover:border-gold-rich/40 transition-all duration-300 flex flex-col group">
                <div class="h-48 bg-emerald-deep relative overflow-hidden">
                    <img src="<?= htmlspecialchars($post['thumbnail_url'] ?? 'https://via.placeholder.com/600x400/064e3b/ffffff?text=Kariana+Quran+Blog') ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" onerror="this.src='https://via.placeholder.com/600x400/064e3b/ffffff?text=Kariana+Quran+Blog'">
                    <div class="absolute top-3 left-3 bg-emerald-night/80 backdrop-blur-sm text-gold-shimmer text-xs px-2.5 py-1 rounded-full font-bold flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1 inline-block shrink-0 text-gold-shimmer" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <?= date('d M, Y', strtotime($post['created_at'])) ?>
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
                            সম্পূর্ণ পড়ুন 
                            <svg class="w-3.5 h-3.5 ml-1 inline-block shrink-0 text-emerald-vibrant" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
