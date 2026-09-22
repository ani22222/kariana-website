<?php
declare(strict_types=1);

/**
 * =============================================================================
 * Kariana Quran Islamic Educational Portal & CMS — E2E Master Test Runner
 * =============================================================================
 * Pure PHP 8.2 Command-Line Test Harness.
 * Zero external vendor/composer dependencies.
 *
 * Usage:
 *   php tests/e2e/runner.php [options]
 *
 * Options:
 *   --url=<url>       Target base URL (default: http://127.0.0.1:8015)
 *   --kernel          Force headless in-memory Kernel Mock execution mode
 *   --tier=<1|2|3|4>  Execute only a specific test tier
 *   --filter=<name>   Filter tests matching substring
 *   --verbose, -v     Verbose output detailing every assertion and failure trace
 *   --help, -h        Display help guide
 * =============================================================================
 */

namespace Tests\E2E;

// Register Autoloader
$projectRoot = dirname(__DIR__, 2);
require_once $projectRoot . '/core/Autoloader.php';
\Core\Autoloader::register();

// Register Test Namespace
\Core\Autoloader::addNamespace('Tests\\E2E', __DIR__);

// CLI Options Parser
$options = getopt('vh', ['url:', 'kernel', 'tier:', 'filter:', 'verbose', 'help']);

if (isset($options['h']) || isset($options['help'])) {
    echo <<<HELP
Kariana Quran Portal & CMS — E2E Test Runner
Usage: php tests/e2e/runner.php [options]

Options:
  --url=<url>       Target server URL (default: http://127.0.0.1:8015)
  --kernel          Force internal Kernel Mock mode (bypasses network)
  --tier=<1..4>     Run specific tier only (1: Features, 2: Boundary, 3: Cross, 4: Scenarios)
  --filter=<regex>  Filter tests matching pattern
  --verbose, -v     Detailed assertion traces
  --help, -h        Display this message

HELP;
    exit(0);
}

$baseUrl = $options['url'] ?? 'http://127.0.0.1:8015';
$forceKernel = isset($options['kernel']);
$targetTier = isset($options['tier']) ? (int)$options['tier'] : null;
$filter = $options['filter'] ?? null;
$verbose = isset($options['v']) || isset($options['verbose']);

// Terminal Color Helper
class CliColor
{
    private static bool $supported = true;

    public static function init(): void
    {
        // Support Windows 10+ ANSI and standard Unix terminals
        if (DIRECTORY_SEPARATOR === '\\') {
            self::$supported = (function_exists('sapi_windows_vt100_support') && sapi_windows_vt100_support(STDOUT))
                || getenv('ANSICON') !== false
                || getenv('ConEmuANSI') === 'ON'
                || getenv('TERM') !== false;
        }
    }

    public static function green(string $text): string
    {
        return self::$supported ? "\033[32m{$text}\033[0m" : $text;
    }

    public static function red(string $text): string
    {
        return self::$supported ? "\033[31m{$text}\033[0m" : $text;
    }

    public static function yellow(string $text): string
    {
        return self::$supported ? "\033[33m{$text}\033[0m" : $text;
    }

    public static function cyan(string $text): string
    {
        return self::$supported ? "\033[36m{$text}\033[0m" : $text;
    }

    public static function bold(string $text): string
    {
        return self::$supported ? "\033[1m{$text}\033[0m" : $text;
    }

    public static function bgGreen(string $text): string
    {
        return self::$supported ? "\033[42;30m{$text}\033[0m" : $text;
    }

    public static function bgRed(string $text): string
    {
        return self::$supported ? "\033[41;37;1m{$text}\033[0m" : $text;
    }
}

CliColor::init();

// Banner
echo "\n";
echo CliColor::bold(CliColor::cyan("===============================================================================\n"));
echo CliColor::bold(CliColor::cyan("     কারিয়ানা কুরআন (Kariana Quran) — E2E Test Suite Harness (PHP 8.2)      \n"));
echo CliColor::bold(CliColor::cyan("===============================================================================\n"));

// Initialize Test Client
$client = new TestClient($baseUrl, $forceKernel);
$isLiveHttp = !$client->isKernelMode();

echo "Execution Mode : " . ($isLiveHttp ? CliColor::green("LIVE HTTP Network ({$baseUrl})") : CliColor::yellow("Headless Kernel Mock Dispatcher")) . "\n";
echo "PHP Runtime    : " . PHP_VERSION . " (" . PHP_OS . ")\n";
echo "Timestamp      : " . date('Y-m-d H:i:s T') . "\n";
echo "-------------------------------------------------------------------------------\n\n";

// Test Suites Registry
$suiteClasses = [
    1 => [
        'name'  => 'Tier 1: Feature Coverage (Features 1-34)',
        'class' => Tier1_FeatureCoverageTest::class,
    ],
    2 => [
        'name'  => 'Tier 2: Boundary & Corner Cases (Edge & Security)',
        'class' => Tier2_BoundaryCornerTest::class,
    ],
    3 => [
        'name'  => 'Tier 3: Cross-Feature Interactions & Data Flows',
        'class' => Tier3_CrossFeatureTest::class,
    ],
    4 => [
        'name'  => 'Tier 4: Real-World Scenarios & User Journeys',
        'class' => Tier4_RealWorldScenarioTest::class,
    ],
];

$totalPassed = 0;
$totalFailed = 0;
$totalAssertions = 0;
$suiteReports = [];
$startTimeTotal = microtime(true);

foreach ($suiteClasses as $tierNumber => $suiteMeta) {
    if ($targetTier !== null && $targetTier !== $tierNumber) {
        continue;
    }

    $suiteName = $suiteMeta['name'];
    $className = $suiteMeta['class'];

    echo CliColor::bold("▶ Running " . CliColor::cyan($suiteName) . "\n");

    /** @var TestCase $testInstance */
    $testInstance = new $className($client);

    // Reflect test methods
    $ref = new \ReflectionClass($className);
    $methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);

    $tierPassed = 0;
    $tierFailed = 0;
    $tierAssertions = 0;
    $tierDuration = 0.0;
    $failures = [];

    foreach ($methods as $method) {
        $methodName = $method->getName();
        if (!str_starts_with($methodName, 'test')) {
            continue;
        }

        if ($filter !== null && !str_contains(strtolower($methodName), strtolower($filter))) {
            continue;
        }

        $testStart = microtime(true);
        $testInstance->resetAssertionsCount();

        try {
            $testInstance->setUp();
            $method->invoke($testInstance);
            $testInstance->tearDown();

            $elapsed = (microtime(true) - $testStart) * 1000.0;
            $assertions = $testInstance->getAssertionsCount();

            $tierPassed++;
            $tierAssertions += $assertions;
            $tierDuration += $elapsed;

            echo "  " . CliColor::green("✓") . " {$methodName} "
                . CliColor::yellow(sprintf("(%.1fms, %d assertions)", $elapsed, $assertions)) . "\n";

        } catch (\Throwable $e) {
            $elapsed = (microtime(true) - $testStart) * 1000.0;
            $assertions = $testInstance->getAssertionsCount();

            $tierFailed++;
            $tierAssertions += $assertions;
            $tierDuration += $elapsed;

            $errorMsg = $e->getMessage();
            $file = $e->getFile();
            $line = $e->getLine();

            $failures[] = [
                'test'       => $methodName,
                'message'    => $errorMsg,
                'file'       => $file,
                'line'       => $line,
                'trace'      => $e->getTraceAsString(),
            ];

            echo "  " . CliColor::red("✗") . " {$methodName} "
                . CliColor::red(sprintf("[FAILED: %.1fms]", $elapsed)) . "\n";
            echo "    " . CliColor::red("↳ {$errorMsg}") . " (" . basename($file) . ":{$line})\n";
        }
    }

    $totalPassed += $tierPassed;
    $totalFailed += $tierFailed;
    $totalAssertions += $tierAssertions;

    $suiteReports[$tierNumber] = [
        'name'       => $suiteName,
        'passed'     => $tierPassed,
        'failed'     => $tierFailed,
        'assertions' => $tierAssertions,
        'duration'   => $tierDuration,
        'failures'   => $failures,
    ];

    echo "\n";
}

$totalDuration = (microtime(true) - $startTimeTotal) * 1000.0;

// Summary Table
echo CliColor::bold(CliColor::cyan("===============================================================================\n"));
echo CliColor::bold("                         TEST EXECUTION SUMMARY REPORT                         \n");
echo CliColor::bold(CliColor::cyan("===============================================================================\n"));

foreach ($suiteReports as $tierNum => $rep) {
    $tierStatus = $rep['failed'] === 0 ? CliColor::green("PASSED") : CliColor::red("FAILED");
    echo sprintf(
        "Tier %d: %-50s | %s (%d/%d tests, %d assertions, %.1fms)\n",
        $tierNum,
        $rep['name'],
        $tierStatus,
        $rep['passed'],
        $rep['passed'] + $rep['failed'],
        $rep['assertions'],
        $rep['duration']
    );
}

echo "-------------------------------------------------------------------------------\n";
echo sprintf(
    "Total Tests: %d | Passed: %s | Failed: %s | Assertions: %d | Time: %.2fs\n",
    $totalPassed + $totalFailed,
    CliColor::green((string)$totalPassed),
    $totalFailed > 0 ? CliColor::red((string)$totalFailed) : CliColor::green("0"),
    $totalAssertions,
    $totalDuration / 1000.0
);
echo "-------------------------------------------------------------------------------\n";

if ($totalFailed === 0) {
    echo "\n" . CliColor::bgGreen(" [SUCCESS] ALL 4 TIERS OF E2E TEST SUITE PASSED PERFECTLY! ") . "\n\n";
    exit(0);
} else {
    echo "\n" . CliColor::bgRed(" [FAILURE] {$totalFailed} TEST(S) FAILED. REVIEW DETAILS ABOVE. ") . "\n\n";
    exit(1);
}
