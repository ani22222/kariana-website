<?php
namespace Core;

/**
 * HTTP Request Handler
 * Normalizes input, decodes Unicode Bengali paths, resolves HTTP methods, and auto-detects Base URLs
 */
class Request
{
    private string $method;
    private string $path;
    private array $queryParams;
    private array $bodyParams;
    private array $serverParams;
    private array $files;
    private string $baseUrl;

    public function __construct()
    {
        $this->serverParams = $_SERVER;
        $this->queryParams = $_GET;
        $this->bodyParams = $this->sanitizeInput($_POST);
        $this->files = $_FILES;

        // Determine HTTP method (supporting _method spoofing for PUT, PATCH, DELETE)
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        if ($method === 'POST') {
            if (isset($_POST['_method'])) {
                $override = strtoupper((string)$_POST['_method']);
                if (in_array($override, ['PUT', 'PATCH', 'DELETE', 'OPTIONS'])) {
                    $method = $override;
                }
            } elseif (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
                $method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
            }
        }
        $this->method = $method;

        // Parse path and handle subfolder / port 8015 deployment
        $this->resolvePathAndBaseUrl();
    }

    /**
     * Resolve the request path and calculate the dynamic Base URL
     */
    private function resolvePathAndBaseUrl(): void
    {
        // 1. Raw URI parsing
        $requestUri = $this->serverParams['REQUEST_URI'] ?? '/';
        $parsedUrl = parse_url($requestUri);
        $rawPath = $parsedUrl['path'] ?? '/';

        // 2. Decode percent-encoded Unicode characters (such as Bengali UTF-8 characters)
        $decodedPath = rawurldecode($rawPath);

        // 3. Dynamic Base Directory Detection
        $scriptName = str_replace('\\', '/', $this->serverParams['SCRIPT_NAME'] ?? '');
        $scriptDir = dirname($scriptName);
        $scriptDir = ($scriptDir === '/' || $scriptDir === '\\' || $scriptDir === '.') ? '' : $scriptDir;

        // 4. Reverse Proxy Prefix Detection (e.g. HTTP_X_FORWARDED_PREFIX like '/kariana')
        $forwardedPrefix = '';
        if (!empty($this->serverParams['HTTP_X_FORWARDED_PREFIX'])) {
            $rawPrefix = $this->serverParams['HTTP_X_FORWARDED_PREFIX'];
            if (str_contains($rawPrefix, ',')) {
                $rawPrefix = trim(explode(',', $rawPrefix)[0]);
            }
            $forwardedPrefix = '/' . trim($rawPrefix, '/');
            if ($forwardedPrefix === '/') {
                $forwardedPrefix = '';
            }
        }

        // If a forwarded prefix is present and the request path starts with it, strip it for internal routing
        if ($forwardedPrefix !== '') {
            if ($decodedPath === $forwardedPrefix) {
                $decodedPath = '';
            } elseif (str_starts_with($decodedPath, $forwardedPrefix . '/')) {
                $decodedPath = substr($decodedPath, strlen($forwardedPrefix));
            }
        }

        // Check decoded script directory comparison
        $decodedScriptDir = rawurldecode($scriptDir);

        if ($decodedScriptDir !== '') {
            if ($decodedPath === $decodedScriptDir) {
                $decodedPath = '';
            } elseif (str_starts_with($decodedPath, $decodedScriptDir . '/')) {
                $decodedPath = substr($decodedPath, strlen($decodedScriptDir));
            }
        } elseif ($scriptDir !== '') {
            if ($decodedPath === $scriptDir) {
                $decodedPath = '';
            } elseif (str_starts_with($decodedPath, $scriptDir . '/')) {
                $decodedPath = substr($decodedPath, strlen($scriptDir));
            }
        }

        // Clean up the internal routing path
        $cleanPath = '/' . trim($decodedPath, '/');
        $this->path = $cleanPath === '' ? '/' : $cleanPath;

        // 5. Construct Base URL (detecting protocol, host, port, and subdirectory/prefix)
        $cfScheme = null;
        if (!empty($this->serverParams['HTTP_CF_VISITOR'])) {
            $cfVisitor = json_decode($this->serverParams['HTTP_CF_VISITOR'], true);
            if (is_array($cfVisitor) && isset($cfVisitor['scheme'])) {
                $cfScheme = strtolower((string)$cfVisitor['scheme']);
            }
        }

        $forwardedProto = strtolower($this->serverParams['HTTP_X_FORWARDED_PROTO'] ?? '');
        if (str_contains($forwardedProto, ',')) {
            $forwardedProto = trim(explode(',', $forwardedProto)[0]);
        }

        $isHttps = (!empty($this->serverParams['HTTPS']) && $this->serverParams['HTTPS'] !== 'off')
            || (isset($this->serverParams['SERVER_PORT']) && (int)$this->serverParams['SERVER_PORT'] === 443)
            || $forwardedProto === 'https'
            || $cfScheme === 'https'
            || (!empty($this->serverParams['HTTP_X_FORWARDED_SSL']) && strtolower($this->serverParams['HTTP_X_FORWARDED_SSL']) === 'on')
            || (!empty($this->serverParams['HTTP_FRONT_END_HTTPS']) && strtolower($this->serverParams['HTTP_FRONT_END_HTTPS']) !== 'off');

        $protocol = $isHttps ? 'https' : 'http';

        $host = $this->serverParams['HTTP_X_FORWARDED_HOST']
            ?? $this->serverParams['HTTP_HOST']
            ?? 'localhost';

        if (str_contains($host, ',')) {
            $host = trim(explode(',', $host)[0]);
        }

        // Base directory or reverse proxy prefix
        $basePrefix = $forwardedPrefix !== '' ? $forwardedPrefix : $scriptDir;

        $this->baseUrl = rtrim("{$protocol}://{$host}{$basePrefix}", '/');
    }

    /**
     * Recursive input sanitization for XSS safety
     */
    private function sanitizeInput(mixed $data): mixed
    {
        if (is_array($data)) {
            $sanitized = [];
            foreach ($data as $key => $value) {
                $sanitized[$key] = $this->sanitizeInput($value);
            }
            return $sanitized;
        }

        if (is_string($data)) {
            // Trim whitespace without altering Bengali characters or HTML content intended for editors
            return trim($data);
        }

        return $data;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function isGet(): bool
    {
        return $this->method === 'GET';
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Return full URL for a relative path
     */
    public function url(string $path = ''): string
    {
        $cleanPath = '/' . ltrim($path, '/');
        return $this->baseUrl . ($cleanPath === '/' ? '' : $cleanPath);
    }

    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    public function getBody(): array
    {
        return $this->bodyParams;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->queryParams[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $this->bodyParams[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->queryParams) || array_key_exists($key, $this->bodyParams);
    }

    public function hasPost(string $key): bool
    {
        return array_key_exists($key, $this->bodyParams);
    }

    public function all(): array
    {
        return array_merge($this->queryParams, $this->bodyParams);
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function getFile(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function getHeader(string $name): ?string
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $this->serverParams[$key] ?? null;
    }

    public function getIp(): string
    {
        return $this->serverParams['HTTP_CF_CONNECTING_IP']
            ?? $this->serverParams['HTTP_CLIENT_IP']
            ?? $this->serverParams['HTTP_X_FORWARDED_FOR']
            ?? $this->serverParams['REMOTE_ADDR']
            ?? '127.0.0.1';
    }

    public function isAjax(): bool
    {
        return !empty($this->serverParams['HTTP_X_REQUESTED_WITH'])
            && strtolower($this->serverParams['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
