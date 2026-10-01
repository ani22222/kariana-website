<?php
/**
 * Official A4 Ready-To-Print ID Card Sheet View
 * Layout: Standard A4 Portrait (210mm x 297mm)
 * Contents: Both Front Face (সম্মুখ ভাগ) & Back Face (পশ্চাৎ ভাগ) in accurate CR80 physical dimensions (54mm x 85.6mm)
 * Includes: Center fold/cut guidelines, corner crop marks, and pouch lamination instructions.
 */
$baseUrl = isset($baseUrl) ? rtrim($baseUrl, '/') : '';
$type = $type ?? 'teacher';
$member = $member ?? [];
$a4Svg = $a4Svg ?? '';
$autoPrint = !empty($autoPrint);
$nameBn = $member['name'] ?? 'নাম দেওয়া হয়নি';
$roleTitle = ($type === 'director') ? ($member['designation'] ?? 'জেলা পরিচালক') : 'শিক্ষক (মুয়াল্লিম)';
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'A4 প্রিন্ট শিট — ক্ব-রিয়ানা কুরআন') ?></title>
    
    <!-- Google Fonts: Hind Siliguri -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (Local CDN / Core) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        bengali: ['"Hind Siliguri"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        /* Exact physical A4 print formatting */
        @page {
            size: A4 portrait;
            margin: 0mm;
        }

        @media print {
            html, body {
                width: 210mm !important;
                height: 297mm !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
            #printPageWrapper {
                padding: 0 !important;
                margin: 0 !important;
                background: #ffffff !important;
                display: block !important;
            }
            #a4Sheet {
                width: 210mm !important;
                height: 297mm !important;
                max-width: 210mm !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            #a4Sheet svg {
                width: 100% !important;
                height: 100% !important;
                display: block !important;
            }
        }

        @media screen {
            body {
                background-color: #090d16;
            }
            #a4Sheet {
                width: 210mm;
                min-height: 297mm;
                box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.7);
            }
            #a4Sheet svg {
                width: 100%;
                height: auto;
                display: block;
            }
        }
    </style>
</head>
<body class="font-bengali text-slate-800 antialiased min-h-screen">

    <!-- FLOATING TOP ACTION BAR (HIDDEN ON PRINT) -->
    <header class="no-print sticky top-0 z-50 bg-slate-900/95 backdrop-blur-md border-b border-emerald-800/60 shadow-xl px-4 py-3">
        <div class="max-w-6xl mx-auto flex flex-wrap items-center justify-between gap-4">
            
            <!-- Left Info -->
            <div class="flex items-center gap-3">
                <a href="<?= $baseUrl ?>/admin/id-card/<?= $type ?>/<?= $member['id'] ?? 1 ?>" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-emerald-300 hover:text-white text-xs font-bold transition flex items-center gap-1.5 border border-slate-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>প্রিভিউতে ফিরুন</span>
                </a>
                <div>
                    <h1 class="text-sm md:text-base font-bold text-amber-300">
                        <?= htmlspecialchars($nameBn) ?> — রেডি-টু-প্রিন্ট A4 শিট
                    </h1>
                    <p class="text-xs text-slate-400">
                        <?= htmlspecialchars($roleTitle) ?> • প্রমিত CR80 সাইজ (৫৪ মিমি × ৮৫.৬ মিমি) • উভয় পৃষ্ঠা
                    </p>
                </div>
            </div>

            <!-- Print Badge Instructions -->
            <div class="hidden lg:flex items-center gap-2 bg-emerald-950/80 px-3 py-1.5 rounded-xl border border-emerald-700/50 text-xs text-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                <span>প্রিন্টার স্কেল অবশ্যই <strong>100% / Actual Size</strong> রাখবেন</span>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-2.5">
                <!-- PRINT BUTTON -->
                <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-gradient-to-r from-amber-400 via-amber-300 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-emerald-950 font-black text-xs md:text-sm shadow-lg flex items-center gap-2 transition-all transform hover:scale-105 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>🖨️ এখনই প্রিন্ট করুন</span>
                </button>

                <!-- DOWNLOAD A4 SVG -->
                <a href="<?= $baseUrl ?>/admin/id-card/download-a4/<?= $type ?>/<?= $member['id'] ?? 1 ?>" class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-xs shadow flex items-center gap-1.5 transition border border-emerald-500/50">
                    <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>A4 SVG ডাউনলোড</span>
                </a>
            </div>
        </div>
    </header>

    <!-- MAIN A4 CANVAS WRAPPER -->
    <main id="printPageWrapper" class="flex justify-center items-center py-6 md:py-10 px-2 sm:px-4">
        
        <!-- Standalone 210mm x 297mm A4 Paper Canvas -->
        <div id="a4Sheet" class="bg-white rounded-sm overflow-hidden transition-all">
            <?= $a4Svg ?>
        </div>

    </main>

    <?php if ($autoPrint): ?>
    <script>
        // Automatic trigger for 1-click print experience
        window.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
    <?php endif; ?>

</body>
</html>
