<?php
/**
 * Application Configuration
 * Kariana Quran Islamic Educational Portal & CMS
 */
return [
    'name'        => 'কারিয়ানা কুরআন',
    'tagline'     => 'সহজ ও সহীহ পদ্ধতিতে কুরআন তিলাওয়াত, হিফজ ও তাজবীদ শিক্ষা',
    'env'         => 'development',
    'debug'       => true,
    'port'        => 8015,
    'timezone'    => 'Asia/Dhaka',
    'locale'      => 'bn_BD',
    'charset'     => 'UTF-8',
    'url'         => 'http://localhost:8015',
    'reader_url'  => 'http://localhost:8014',

    // Islamic Foundation Bangladesh conventions
    'prayer_defaults' => [
        'base_district' => 'ঢাকা',
        'fajr_angle'    => 18.0,
        'isha_angle'    => 18.0,
        'asr_method'    => 'Hanafi', // Hanafi: shadow 2x + noon shadow
        'dhuhr_buffer'  => 1.5,      // minutes
        'maghrib_buffer'=> 2.0,      // minutes
        'sehri_margin'  => 3,        // minutes before Fajr
    ],

    // Zakat Nisab standards (Hanafi)
    'zakat_defaults' => [
        'silver_nisab_tola' => 52.5,   // 612.36 grams
        'gold_nisab_tola'   => 7.5,    // 87.48 grams
        'silver_rate_bhori' => 2000.0, // Default BDT per Bhori
        'gold_rate_bhori'   => 125000.0,// Default BDT per Bhori
        'zakat_rate'        => 0.025,  // 2.5%
    ],
];
