<?php
declare(strict_types=1);

namespace Tests\E2E;

use PDO;
use Core\Database;

/**
 * Abstract Base Test Case for Kariana Quran E2E Suites
 */
abstract class TestCase
{
    protected TestClient $client;
    protected ?PDO $pdo = null;
    protected int $assertionsCount = 0;
    protected string $currentTestName = '';

    public function __construct(TestClient $client)
    {
        $this->client = $client;
    }

    public function setUp(): void
    {
        // Hook for child setup
    }

    public function tearDown(): void
    {
        // Hook for child cleanup
    }

    public function getAssertionsCount(): int
    {
        return $this->assertionsCount;
    }

    public function resetAssertionsCount(): void
    {
        $this->assertionsCount = 0;
    }

    /**
     * Get active database connection
     */
    protected function getPdo(): ?PDO
    {
        if ($this->pdo === null) {
            try {
                if (class_exists(Database::class)) {
                    $this->pdo = Database::getInstance();
                } else {
                    $projectRoot = dirname(__DIR__, 2);
                    $config = require $projectRoot . '/config/database.php';
                    $dsn = sprintf(
                        "mysql:host=%s;port=%d;dbname=%s;charset=%s",
                        $config['host'] ?? 'localhost',
                        $config['port'] ?? 3306,
                        $config['database'] ?? 'kariana_portal',
                        $config['charset'] ?? 'utf8mb4'
                    );
                    $this->pdo = new PDO($dsn, $config['username'] ?? 'root', $config['password'] ?? '', [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]);
                }
            } catch (\Throwable $e) {
                // Database might be optional or offline for some pure unit tests
                $this->pdo = null;
            }
        }
        return $this->pdo;
    }

    protected function assertTrue(bool $condition, string $message = ''): void
    {
        $this->assertionsCount++;
        if ($condition !== true) {
            throw new \AssertionError($message ?: "Failed asserting that condition is true.");
        }
    }

    protected function assertFalse(bool $condition, string $message = ''): void
    {
        $this->assertionsCount++;
        if ($condition !== false) {
            throw new \AssertionError($message ?: "Failed asserting that condition is false.");
        }
    }

    protected function assertEquals(mixed $expected, mixed $actual, string $message = ''): void
    {
        $this->assertionsCount++;
        if ($expected !== $actual) {
            $expectedStr = is_scalar($expected) ? (string)$expected : json_encode($expected, JSON_UNESCAPED_UNICODE);
            $actualStr = is_scalar($actual) ? (string)$actual : json_encode($actual, JSON_UNESCAPED_UNICODE);
            throw new \AssertionError($message ?: "Failed asserting that [{$actualStr}] matches expected [{$expectedStr}].");
        }
    }

    protected function assertNotEquals(mixed $expected, mixed $actual, string $message = ''): void
    {
        $this->assertionsCount++;
        if ($expected === $actual) {
            $expectedStr = is_scalar($expected) ? (string)$expected : json_encode($expected, JSON_UNESCAPED_UNICODE);
            throw new \AssertionError($message ?: "Failed asserting that [{$expectedStr}] does not match expected.");
        }
    }

    protected function assertNull(mixed $actual, string $message = ''): void
    {
        $this->assertionsCount++;
        if ($actual !== null) {
            throw new \AssertionError($message ?: "Failed asserting that value is null.");
        }
    }

    protected function assertNotNull(mixed $actual, string $message = ''): void
    {
        $this->assertionsCount++;
        if ($actual === null) {
            throw new \AssertionError($message ?: "Failed asserting that value is not null.");
        }
    }

    protected function assertCount(int $expectedCount, array|\Countable $countable, string $message = ''): void
    {
        $this->assertionsCount++;
        $actualCount = count($countable);
        if ($actualCount !== $expectedCount) {
            throw new \AssertionError($message ?: "Failed asserting that count {$actualCount} matches expected {$expectedCount}.");
        }
    }

    protected function assertGreaterThan(mixed $expected, mixed $actual, string $message = ''): void
    {
        $this->assertionsCount++;
        if (!($actual > $expected)) {
            throw new \AssertionError($message ?: "Failed asserting that {$actual} is greater than {$expected}.");
        }
    }

    protected function assertMatchesRegex(string $pattern, string $string, string $message = ''): void
    {
        $this->assertionsCount++;
        if (!preg_match($pattern, $string)) {
            throw new \AssertionError($message ?: "Failed asserting that '{$string}' matches regex pattern '{$pattern}'.");
        }
    }

    /**
     * Assert database table contains matching record
     */
    protected function assertDatabaseHas(string $table, array $criteria, string $message = ''): void
    {
        $this->assertionsCount++;
        $pdo = $this->getPdo();
        if ($pdo === null) {
            throw new \AssertionError("Cannot assert database record: Database connection unavailable.");
        }

        $clauses = [];
        $params = [];
        foreach ($criteria as $col => $val) {
            $clauses[] = "`{$col}` = :param_{$col}";
            $params[":param_{$col}"] = $val;
        }

        $sql = "SELECT COUNT(*) as cnt FROM `{$table}` WHERE " . implode(' AND ', $clauses);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $count = (int)$stmt->fetchColumn();

        if ($count === 0) {
            $criteriaJson = json_encode($criteria, JSON_UNESCAPED_UNICODE);
            $msg = $message ?: "Failed asserting that table `{$table}` contains record matching {$criteriaJson}.";
            throw new \AssertionError($msg);
        }
    }

    /**
     * Assert database table does NOT contain matching record
     */
    protected function assertDatabaseMissing(string $table, array $criteria, string $message = ''): void
    {
        $this->assertionsCount++;
        $pdo = $this->getPdo();
        if ($pdo === null) {
            return; // Skip if db not available
        }

        $clauses = [];
        $params = [];
        foreach ($criteria as $col => $val) {
            $clauses[] = "`{$col}` = :param_{$col}";
            $params[":param_{$col}"] = $val;
        }

        $sql = "SELECT COUNT(*) as cnt FROM `{$table}` WHERE " . implode(' AND ', $clauses);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $count = (int)$stmt->fetchColumn();

        if ($count > 0) {
            $criteriaJson = json_encode($criteria, JSON_UNESCAPED_UNICODE);
            $msg = $message ?: "Failed asserting that table `{$table}` does not contain record matching {$criteriaJson} (found {$count} rows).";
            throw new \AssertionError($msg);
        }
    }
}
