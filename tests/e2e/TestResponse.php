<?php
declare(strict_types=1);

namespace Tests\E2E;

/**
 * Encapsulates an HTTP / Kernel response with fluent assertion helpers.
 */
class TestResponse
{
    private int $statusCode;
    private array $headers;
    private string $content;
    private float $executionTimeMs;
    private string $url;

    public function __construct(
        int $statusCode,
        array $headers,
        string $content,
        float $executionTimeMs = 0.0,
        string $url = ''
    ) {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->content = $content;
        $this->executionTimeMs = $executionTimeMs;
        $this->url = $url;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHeader(string $name): ?string
    {
        $target = strtolower($name);
        foreach ($this->headers as $key => $value) {
            if (strtolower($key) === $target) {
                return is_array($value) ? implode(', ', $value) : (string)$value;
            }
        }
        return null;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getExecutionTimeMs(): float
    {
        return $this->executionTimeMs;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Assert HTTP status code matches expected
     */
    public function assertStatus(int $expected, string $message = ''): self
    {
        if ($this->statusCode !== $expected) {
            $msg = $message ?: "Expected HTTP status {$expected}, got {$this->statusCode} for URL: {$this->url}";
            throw new \AssertionError($msg);
        }
        return $this;
    }

    /**
     * Assert status is in 2xx range
     */
    public function assertSuccessful(string $message = ''): self
    {
        if ($this->statusCode < 200 || $this->statusCode >= 300) {
            $msg = $message ?: "Expected 2xx status, got {$this->statusCode} for URL: {$this->url}";
            throw new \AssertionError($msg);
        }
        return $this;
    }

    /**
     * Assert status is redirect (301, 302, 303, 307, 308)
     */
    public function assertRedirect(?string $expectedLocation = null, string $message = ''): self
    {
        $isRedirect = in_array($this->statusCode, [301, 302, 303, 307, 308], true);
        if (!$isRedirect) {
            $msg = $message ?: "Expected redirect status, got {$this->statusCode} for URL: {$this->url}";
            throw new \AssertionError($msg);
        }

        if ($expectedLocation !== null) {
            $actualLocation = $this->getHeader('Location');
            if ($actualLocation === null || !str_contains($actualLocation, $expectedLocation)) {
                $msg = $message ?: "Expected redirect to '{$expectedLocation}', got '{$actualLocation}'";
                throw new \AssertionError($msg);
            }
        }
        return $this;
    }

    /**
     * Assert content contains a given substring
     */
    public function assertContains(string $needle, string $message = ''): self
    {
        if (!str_contains($this->content, $needle)) {
            $preview = mb_substr(strip_tags($this->content), 0, 200);
            $msg = $message ?: "Response body does not contain expected substring '{$needle}'. Preview: [{$preview}...]";
            throw new \AssertionError($msg);
        }
        return $this;
    }

    /**
     * Assert content does not contain a given substring
     */
    public function assertNotContains(string $needle, string $message = ''): self
    {
        if (str_contains($this->content, $needle)) {
            $msg = $message ?: "Response body unexpectedly contains substring '{$needle}'";
            throw new \AssertionError($msg);
        }
        return $this;
    }

    /**
     * Assert valid JSON response
     */
    public function assertJson(): array
    {
        $decoded = json_decode($this->content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \AssertionError("Response is not valid JSON: " . json_last_error_msg() . "\nContent: " . mb_substr($this->content, 0, 300));
        }
        return $decoded;
    }

    /**
     * Assert valid XML response
     */
    public function assertValidXml(): \SimpleXMLElement
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($this->content);
        if ($xml === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            $errStrs = array_map(fn($e) => trim($e->message), $errors);
            throw new \AssertionError("Response is not valid XML:\n" . implode("\n", $errStrs));
        }
        return $xml;
    }

    /**
     * Assert presence and validity of Schema.org JSON-LD structured data in HTML
     */
    public function assertValidJsonLd(?string $expectedType = null): array
    {
        // Extract all <script type="application/ld+json">...</script>
        $pattern = '#<script[^>]*type=["\']application/ld\+json["\'][^>]*>(.*?)</script>#is';
        if (!preg_match_all($pattern, $this->content, $matches)) {
            throw new \AssertionError("No Schema.org JSON-LD <script> tags found in response for URL: {$this->url}");
        }

        $schemas = [];
        foreach ($matches[1] as $index => $jsonStr) {
            $decoded = json_decode(trim($jsonStr), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \AssertionError("Schema.org JSON-LD block #{$index} contains malformed JSON: " . json_last_error_msg());
            }
            $schemas[] = $decoded;
        }

        if ($expectedType !== null) {
            $foundType = false;
            foreach ($schemas as $schema) {
                if (isset($schema['@type']) && $schema['@type'] === $expectedType) {
                    $foundType = true;
                    break;
                }
                // Also check if @graph array is present
                if (isset($schema['@graph']) && is_array($schema['@graph'])) {
                    foreach ($schema['@graph'] as $item) {
                        if (isset($item['@type']) && $item['@type'] === $expectedType) {
                            $foundType = true;
                            break 2;
                        }
                    }
                }
            }
            if (!$foundType) {
                $typesFound = array_column($schemas, '@type');
                throw new \AssertionError("Expected Schema.org type '{$expectedType}', but found types: [" . implode(', ', array_filter($typesFound)) . "] in URL: {$this->url}");
            }
        }

        return $schemas;
    }

    /**
     * Extract CSRF token from HTML form hidden field
     */
    public function extractCsrfToken(): ?string
    {
        // Match <input type="hidden" name="csrf_token" value="...">
        if (preg_match('#<input[^>]+name=["\']csrf_token["\'][^>]+value=["\']([^"\']+)["\']#i', $this->content, $m)) {
            return $m[1];
        }
        // Match value then name
        if (preg_match('#<input[^>]+value=["\']([^"\']+)["\'][^>]+name=["\']csrf_token["\']#i', $this->content, $m)) {
            return $m[1];
        }
        // Match meta tag <meta name="csrf-token" content="...">
        if (preg_match('#<meta[^>]+name=["\']csrf-token["\'][^>]+content=["\']([^"\']+)["\']#i', $this->content, $m)) {
            return $m[1];
        }
        return null;
    }

    /**
     * Extract arbitrary regex match
     */
    public function extractRegex(string $pattern): ?string
    {
        if (preg_match($pattern, $this->content, $m)) {
            return $m[1] ?? $m[0];
        }
        return null;
    }
}
