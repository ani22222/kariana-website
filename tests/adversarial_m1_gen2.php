<?php
/**
 * Empirical Challenger M1 Gen2 #2 — Adversarial Stress Test Suite
 *
 * Rigorously stress tests:
 * 1. Core\Model $orderBy SQL injection prevention and whitelisting
 * 2. Core\Csrf non-string type handling and TypeError immunity
 * 3. Sensitive file HTTP access restrictions
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/core/Autoloader.php';
\Core\Autoloader::register();

use Core\Model;
use Core\Csrf;
use Core\Session;
use Core\Request;
use Core\Controller;
use Core\Database;

class TestDistrictModel extends Model
{
    protected string $table = 'prayer_districts';
    protected string $primaryKey = 'id';
}

class TestCsrfController extends Controller
{
    public function checkCsrf(Request $request): bool
    {
        return $this->validateCsrf($request);
    }
}

function runAdversarialM1Challenge(): array
{
    $report = [
        'timestamp' => date('Y-m-d H:i:s T'),
        'total_tests' => 0,
        'passed' => 0,
        'failed' => 0,
        'verdict' => 'PENDING',
        'sections' => [],
    ];

    $assert = function (string $section, string $testName, bool $condition, string $details = '', array $meta = []) use (&$report) {
        $report['total_tests']++;
        if ($condition) {
            $report['passed']++;
        } else {
            $report['failed']++;
        }
        $report['sections'][$section][] = [
            'test' => $testName,
            'passed' => $condition,
            'details' => $details,
            'meta' => $meta,
        ];
    };

    // =========================================================================
    // SECTION 1: Core\Model OrderBy SQL Injection & Whitelisting Stress
    // =========================================================================
    $sec1 = 'Model OrderBy SQL Injection Defense';
    $model = new TestDistrictModel();

    // 1.1 The four explicit payloads requested by user
    $userPayloads = [
        'Payload 1: id ASC, (SELECT SLEEP(1))' => 'id ASC, (SELECT SLEEP(1))',
        'Payload 2: id; DROP TABLE users;'     => 'id; DROP TABLE users;',
        'Payload 3: 1\' UNION SELECT ...'       => "1' UNION SELECT 1,2,3,4,5,6,7,8,9,10,11,12,NOW() -- ",
        'Payload 4: id ASC SLEEP col'          => 'id ASC SLEEP col',
    ];

    foreach ($userPayloads as $desc => $payload) {
        $exceptionThrownWhere = false;
        $exceptionMsgWhere = '';
        try {
            $model->where([], $payload, 1);
        } catch (\InvalidArgumentException $e) {
            $exceptionThrownWhere = true;
            $exceptionMsgWhere = $e->getMessage();
        } catch (\Throwable $e) {
            $exceptionMsgWhere = 'Unexpected: ' . get_class($e) . ': ' . $e->getMessage();
        }

        $exceptionThrownAll = false;
        $exceptionMsgAll = '';
        try {
            $model->all($payload, 1);
        } catch (\InvalidArgumentException $e) {
            $exceptionThrownAll = true;
            $exceptionMsgAll = $e->getMessage();
        } catch (\Throwable $e) {
            $exceptionMsgAll = 'Unexpected: ' . get_class($e) . ': ' . $e->getMessage();
        }

        $passed = $exceptionThrownWhere && $exceptionThrownAll;
        $assert(
            $sec1,
            "Mandated Challenge: {$desc}",
            $passed,
            "where() caught: " . ($exceptionThrownWhere ? 'YES' : 'NO') . " ('{$exceptionMsgWhere}'), all() caught: " . ($exceptionThrownAll ? 'YES' : 'NO') . " ('{$exceptionMsgAll}')",
            ['payload' => $payload, 'where_caught' => $exceptionThrownWhere, 'all_caught' => $exceptionThrownAll]
        );
    }

    // 1.2 Extended Adversarial SQLi Vectors
    $adversarialPayloads = [
        'Time-based Sleep Function'            => 'id ASC, SLEEP(1)',
        'Subquery with Select'                 => 'id ASC, (SELECT 1 FROM users)',
        'Boolean Tautology with Expression'    => 'id ASC, 1=1',
        'Heavy Benchmark Injection'            => 'id ASC, BENCHMARK(1000000,MD5(1))',
        'Stacked Semicolon Query'              => 'id ASC; SELECT 1',
        'SQL Dash Comment Injection'           => 'id ASC--',
        'SQL Hash Comment Injection'           => 'id ASC#',
        'SQL Block Comment Injection'          => 'id ASC/*comment*/',
        'Trailing Comma Syntax Exploitation'   => 'id ASC,',
        'Leading Comma Syntax Exploitation'    => ',id ASC',
        'Consecutive Empty Commas'             => 'id ASC,,name DESC',
        'Newline Delimiter Obfuscation'        => "id ASC\nSLEEP\ncol",
        'Adjacent Column Space Stuffing'       => 'col1 col2',
        'Missing Comma Between Directions'     => 'col1 ASC col2 DESC',
        'Conditional Case Expression'          => '(CASE WHEN (1=1) THEN id ELSE name END)',
        'XPath Error-based ExtractValue'       => 'extractvalue(1,concat(0x7e,version()))',
        'XML Error-based UpdateXML'            => 'updatexml(1,concat(0x7e,version()),1)',
        'Boolean AND Operator'                 => 'id ASC AND 1=1',
        'Boolean OR Operator'                  => 'id ASC OR 1=1',
        'UNION Keyword Stuffing'               => 'id ASC UNION SELECT 1',
        'Quote Escaped Tautology'              => "id' OR '1'='1",
        'Triple Dot Column Specification'      => 'prayer_districts.col.subcol ASC',
        'Function with Keyword Trailing'       => 'id ASC, col ASC SLEEP',
        'Semicolon Stacked Table Drop'         => 'id ASC; DROP TABLE prayer_districts;',
    ];

    foreach ($adversarialPayloads as $desc => $payload) {
        $caught = false;
        $msg = '';
        try {
            $model->where([], $payload, 1);
        } catch (\InvalidArgumentException $e) {
            $caught = true;
            $msg = $e->getMessage();
        } catch (\Throwable $e) {
            $msg = 'Unexpected exception: ' . get_class($e) . ': ' . $e->getMessage();
        }

        $assert(
            $sec1,
            "Adversarial Payload: {$desc}",
            $caught,
            $caught ? "Safely rejected with InvalidArgumentException: '{$msg}'" : "FAIL: Payload was not rejected! Result: {$msg}",
            ['payload' => $payload, 'caught' => $caught]
        );
    }

    // 1.3 Oracle Verification: Legitimate ORDER BY clauses MUST NOT be rejected
    $legitimateClauses = [
        'Single column default direction'   => 'id',
        'Single column ASC'                 => 'id ASC',
        'Single column DESC'                => 'id DESC',
        'Single column lowercase asc'       => 'id asc',
        'Single column lowercase desc'      => 'id desc',
        'Table prefix with column ASC'      => 'prayer_districts.id ASC',
        'Table prefix with column DESC'     => 'prayer_districts.id DESC',
        'Multi-column compound sorting'     => 'division_bn ASC, id DESC',
        'Three-column compound sorting'     => 'name_en ASC, division_bn DESC, id ASC',
        'Empty string (no order by)'        => '',
    ];

    foreach ($legitimateClauses as $desc => $clause) {
        $success = false;
        $error = '';
        $rows = [];
        try {
            $rows = $model->where([], $clause, 3);
            $success = is_array($rows);
        } catch (\Throwable $e) {
            $success = false;
            $error = get_class($e) . ': ' . $e->getMessage();
        }

        $assert(
            $sec1,
            "Oracle Legitimate Clause: {$desc}",
            $success,
            $success ? "Successfully executed valid clause '{$clause}', returned " . count($rows) . " rows." : "FAIL: Legitimate clause rejected! Error: {$error}",
            ['clause' => $clause, 'success' => $success, 'row_count' => count($rows)]
        );
    }

    // =========================================================================
    // SECTION 2: Core\Csrf Non-String Type Handling & TypeError Immunity
    // =========================================================================
    $sec2 = 'CSRF Type Handling & Robustness';
    Session::start();
    $activeToken = Csrf::token();

    $nonStringInputs = [
        'Null value'                           => null,
        'Empty array'                          => [],
        'Associative array with csrf_token'    => ['csrf_token' => $activeToken],
        'Indexed array of strings'             => ['token1', 'token2'],
        'Nested array'                         => [['deep' => ['nested' => $activeToken]]],
        'Integer 0'                            => 0,
        'Integer 1'                            => 1,
        'Integer 12345'                        => 12345,
        'Negative integer -999'                => -999,
        'PHP_INT_MAX integer'                  => PHP_INT_MAX,
        'Float 0.0'                            => 0.0,
        'Float 3.14159'                        => 3.14159,
        'Boolean true'                         => true,
        'Boolean false'                        => false,
        'Generic stdClass object'              => new \stdClass(),
        'Object with token property'           => (object)['csrf_token' => $activeToken],
        'Stringable class object'              => new class($activeToken) {
            public function __construct(private string $t) {}
            public function __toString(): string { return $this->t; }
        },
    ];

    foreach ($nonStringInputs as $desc => $input) {
        $noTypeErrorCsrf = false;
        $returnValCsrf = null;
        $errorMsgCsrf = '';

        try {
            $returnValCsrf = Csrf::validate($input);
            $noTypeErrorCsrf = ($returnValCsrf === false);
        } catch (\TypeError $e) {
            $errorMsgCsrf = 'FATAL TypeError: ' . $e->getMessage();
        } catch (\Throwable $e) {
            $errorMsgCsrf = 'Exception: ' . get_class($e) . ': ' . $e->getMessage();
        }

        $assert(
            $sec2,
            "Csrf::validate() Non-String: {$desc}",
            $noTypeErrorCsrf,
            $noTypeErrorCsrf ? "Safely returned bool(false) without TypeError." : "FAIL: {$errorMsgCsrf}",
            ['input_type' => get_debug_type($input), 'return_val' => $returnValCsrf, 'error' => $errorMsgCsrf]
        );
    }

    // 2.2 Test Controller::validateCsrf with non-string POST input
    $controller = new TestCsrfController();
    $controllerInputs = [
        'POST csrf_token is array'       => ['token' => 'nested_hacker'],
        'POST csrf_token is integer'     => 99999,
        'POST csrf_token is null'        => null,
    ];

    foreach ($controllerInputs as $desc => $postVal) {
        $caughtTypeError = false;
        $controllerReturn = null;
        $controllerError = '';

        $savedPost = $_POST;
        if ($postVal === null) {
            unset($_POST['csrf_token']);
        } else {
            $_POST['csrf_token'] = $postVal;
        }

        try {
            $req = new Request();
            $controllerReturn = $controller->checkCsrf($req);
        } catch (\TypeError $e) {
            $caughtTypeError = true;
            $controllerError = 'FATAL TypeError: ' . $e->getMessage();
        } catch (\Throwable $e) {
            $controllerError = 'Exception: ' . get_class($e) . ': ' . $e->getMessage();
        }
        $_POST = $savedPost;

        $passed = (!$caughtTypeError && $controllerReturn === false);
        $assert(
            $sec2,
            "Controller::validateCsrf() Simulation: {$desc}",
            $passed,
            $passed ? "Controller safely rejected non-string POST without TypeError (returned false)." : "FAIL: {$controllerError}",
            ['input_type' => get_debug_type($postVal), 'returned' => $controllerReturn]
        );
    }

    // 2.3 Verify valid token DOES succeed
    $validValidation = Csrf::validate($activeToken);
    $assert(
        $sec2,
        'Csrf::validate() Valid Active Token',
        $validValidation === true,
        "Active session token successfully validated as true.",
        ['token_length' => strlen($activeToken)]
    );

    // =========================================================================
    // SECTION 3: HTTP Access Control Verification
    // =========================================================================
    $sec3 = 'Sensitive File HTTP Access Security';
    $baseUrl = 'http://localhost/Kariana%20Website';

    $pathsToVerify = [
        '/.env'                                   => 403,
        '/.git/config'                            => 403,
        '/database/schema.sql'                    => 403,
        '/tests/unit/test_m1.php'                 => 403,
        '/config/app.php'                         => 403,
        '/config/database.php'                    => 403,
        '/core/Model.php'                         => 403,
        '/core/Csrf.php'                          => 403,
        '/storage/'                               => 403,
        '/app/Controllers/HomeController.php'     => 403,
        '/.agents/worker_m1_gen2/handoff.md'      => 403,
        '/composer.json'                          => 403,
        '/assets/css/main.css'                    => 200,
        '/'                                       => 200,
        '/blog/সহজ-পদ্ধতিতে-কুরআন-শেখা'           => 200,
    ];

    foreach ($pathsToVerify as $path => $expectedStatus) {
        $targetUrl = $baseUrl . $path;
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'ignore_errors' => true,
                'timeout' => 5,
                'header' => "User-Agent: AdversarialChallenger/2.0\r\n",
            ],
        ]);

        $responseHeaders = @get_headers($targetUrl, false, $context);
        $statusCode = 0;
        if ($responseHeaders && isset($responseHeaders[0])) {
            if (preg_match('#HTTP/\S+\s+(\d{3})#', $responseHeaders[0], $m)) {
                $statusCode = (int)$m[1];
            }
        }

        $statusPassed = ($statusCode === $expectedStatus);
        $assert(
            $sec3,
            "HTTP Access Rule: {$path} (Expected {$expectedStatus})",
            $statusPassed,
            $statusPassed ? "Got expected HTTP {$statusCode} for '{$path}'." : "FAIL: Expected HTTP {$expectedStatus}, got HTTP {$statusCode} for '{$path}'.",
            ['url' => $targetUrl, 'expected_code' => $expectedStatus, 'actual_code' => $statusCode, 'status_line' => $responseHeaders[0] ?? 'NO_RESPONSE']
        );
    }

    $report['verdict'] = ($report['failed'] === 0) ? 'CONFIRM CORRECTNESS' : 'CHALLENGE FAILED';
    return $report;
}
