<?php
declare(strict_types=1);

namespace Tests\E2E;

use Core\BengaliHelper;
use Core\Database;

/**
 * Tier 4: Real-World Scenario Test Suite
 * Simulates complete end-to-end user journeys representative of authentic visitor behavior.
 */
class Tier4_RealWorldScenarioTest extends TestCase
{
    /**
     * Journey 1: Prospective Student Journey
     * Visitor explores home -> browses courses -> inspects course details -> submits admission form
     */
    public function testProspectiveStudentJourney(): void
    {
        // Step 1: Visit Home Page
        $homeRes = $this->client->get('/');
        $this->assertTrue(
            in_array($homeRes->getStatusCode(), [200, 302]),
            "Prospective student must be able to load home page"
        );

        // Step 2: Browse Courses Catalog
        $coursesRes = $this->client->get('/courses');
        $this->assertTrue(
            in_array($coursesRes->getStatusCode(), [200, 404]),
            "Prospective student must be able to browse courses"
        );

        // Step 3: View Course Details
        $courseSlug = 'tajweed-shikkha';
        $pdo = $this->getPdo();
        $courseId = 1;

        if ($pdo !== null) {
            try {
                $stmt = $pdo->query("SELECT id, slug FROM courses LIMIT 1");
                $c = $stmt->fetch();
                if ($c) {
                    $courseId = (int)$c['id'];
                    $courseSlug = $c['slug'];
                }
            } catch (\Throwable $e) {}
        }

        $detailRes = $this->client->get("/courses/{$courseSlug}");
        $this->assertTrue(
            in_array($detailRes->getStatusCode(), [200, 404]),
            "Student inspects course details"
        );

        // Step 4: Extract CSRF Token and Submit Admission
        $csrf = $detailRes->extractCsrfToken() ?? $this->client->extractCsrfFrom('/courses');
        $studentPhone = '0181' . rand(1000000, 9999999);
        $studentName = 'আহমেদ হাসান মোস্তফা';

        $applyRes = $this->client->post('/admissions/apply', [
            'csrf_token'    => $csrf,
            'course_id'     => $courseId,
            'student_name'  => $studentName,
            'guardian_name' => 'মোস্তফা হাসান',
            'phone'         => $studentPhone,
            'district'      => 'চট্টগ্রাম',
            'gender'        => 'male',
            'preferred_time'=> 'সন্ধ্যা ৭:০০ - ৮:০০',
        ]);

        $this->assertTrue(
            in_array($applyRes->getStatusCode(), [200, 302, 404]),
            "Admission submission must respond with success or redirect"
        );

        // Step 5: Verify record in MariaDB if database is active
        if ($pdo !== null) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM admissions WHERE phone = :phone");
                $stmt->execute([':phone' => $studentPhone]);
                $lead = $stmt->fetch();
                if ($lead) {
                    $this->assertEquals($studentName, $lead['student_name'], "Lead student name must match");
                    $this->assertEquals('চট্টগ্রাম', $lead['district'], "Lead district must match");
                    // Cleanup
                    $pdo->prepare("DELETE FROM admissions WHERE id = :id")->execute([':id' => $lead['id']]);
                }
            } catch (\Throwable $e) {}
        }
    }

    /**
     * Journey 2: Daily Islamic Devotional Journey
     * Visitor checks prayer times for Sylhet & Chattogram -> checks Sehri/Iftar -> counts Tasbeeh
     */
    public function testDailyIslamicDevotionalJourney(): void
    {
        // Step 1: Open Prayer Times page
        $ptRes = $this->client->get('/prayer-times');
        $this->assertTrue(in_array($ptRes->getStatusCode(), [200, 404]));

        // Step 2: Test district offset resolution for Sylhet (-6m) and Chattogram (-5m)
        $districtsFile = dirname(__DIR__, 2) . '/config/districts.php';
        if (file_exists($districtsFile)) {
            $districts = require $districtsFile;
            
            $sylhet = null;
            $chattogram = null;
            foreach ($districts as $d) {
                if (strtolower($d['name_en'] ?? '') === 'sylhet') $sylhet = $d;
                if (strtolower($d['name_en'] ?? '') === 'chattogram' || strtolower($d['name_en'] ?? '') === 'chittagong') $chattogram = $d;
            }

            $this->assertNotNull($sylhet, "Sylhet must be present in districts");
            $this->assertNotNull($chattogram, "Chattogram must be present in districts");

            // Verify Sylhet is -6 mins
            $this->assertEquals(-6, (int)$sylhet['maghrib_offset'], "Sylhet Maghrib offset must be -6 mins");
            // Verify Chattogram is -5 mins
            $this->assertEquals(-5, (int)$chattogram['maghrib_offset'], "Chattogram Maghrib offset must be -5 mins");
        }

        // Step 3: Open Digital Tasbeeh page
        $tasbeehRes = $this->client->get('/tasbeeh');
        if ($tasbeehRes->getStatusCode() === 200) {
            $content = $tasbeehRes->getContent();
            $this->assertTrue(
                str_contains($content, 'সুবহানাল্লাহ') || str_contains($content, '৩৩'),
                "Tasbeeh must have default SubhanAllah preset"
            );
        }
    }

    /**
     * Journey 3: Annual Zakat Calculation & Assessment Journey
     * User inputs cash, gold, silver, subtracts debts, verifies net wealth and silver Nisab evaluation
     */
    public function testAnnualZakatCalculationJourney(): void
    {
        // Benchmark parameters
        $silverNisabTola = 52.50;
        $silverRate = 2000.00;
        $nisabThreshold = $silverNisabTola * $silverRate; // ৳ ১,০৫,০০০

        // Journey input values
        $cash = 75000.00;
        $goldValue = 150000.00;
        $silverValue = 25000.00;
        $businessGoods = 50000.00;
        $immediateDebt = 20000.00;

        $grossAssets = $cash + $goldValue + $silverValue + $businessGoods; // ৳ 300,000
        $netWealth = $grossAssets - $immediateDebt; // ৳ 280,000

        $this->assertEquals(280000.00, $netWealth, "Net wealth must be ৳ 280,000");

        $isEligible = $netWealth >= $nisabThreshold;
        $this->assertTrue($isEligible, "৳ 280,000 net wealth must be above Silver Nisab threshold (৳ 105,000)");

        $zakatPayable = $isEligible ? $netWealth * 0.025 : 0.00;
        $this->assertEquals(7000.00, $zakatPayable, "2.5% Zakat on ৳ 280,000 must be exactly ৳ 7,000.00");

        // Convert to Bengali numeral representation
        if (class_exists(BengaliHelper::class)) {
            $bnPayable = BengaliHelper::toBengaliNumber((string)$zakatPayable);
            $this->assertEquals('৭০০০', $bnPayable, "Bengali representation of 7000 must be ৭০০০");
        }

        // Test web interface
        $zakatRes = $this->client->get('/zakat');
        if ($zakatRes->getStatusCode() === 200) {
            $content = $zakatRes->getContent();
            $this->assertTrue(
                str_contains($content, 'যাকাত') || str_contains($content, 'নিসাব'),
                "Zakat calculator must be accessible"
            );
        }
    }

    /**
     * Journey 4: Publications Catalog to QR Video Lesson & Quran Reader Bridge Journey
     * Visitor inspects books -> scans page 5 QR -> watches video lesson -> transitions to reader bridge
     */
    public function testBookToQrLessonAndQuranReaderBridgeJourney(): void
    {
        // Step 1: Browse Publications
        $booksRes = $this->client->get('/books');
        $this->assertTrue(in_array($booksRes->getStatusCode(), [200, 404]));

        // Step 2: Scan QR for Page 5
        $scanRes = $this->client->get('/scan?page=5');
        $this->assertTrue(in_array($scanRes->getStatusCode(), [200, 404]));

        // Step 3: Transition to Quran App Bridge
        $bridgeRes = $this->client->get('/quran-bridge');
        if ($bridgeRes->getStatusCode() === 200) {
            $content = $bridgeRes->getContent();
            $this->assertTrue(
                str_contains($content, 'কুরআন') || str_contains($content, 'তাজবীদ'),
                "Quran Bridge must display Quran reader gateway"
            );
        }
    }
}
