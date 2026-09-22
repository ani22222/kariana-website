<?php
declare(strict_types=1);

namespace Tests\E2E;

/**
 * Unified Test Client:
 * Supports both real HTTP network transport (cURL/Streams) against http://127.0.0.1:8015
 * and in-memory headless Kernel Mock dispatching for instant or offline execution.
 */
class TestClient
{
    private string $baseUrl;
    private bool $forceKernel;
    private ?bool $serverOnline = null;
    private array $cookies = [];
    private array $defaultHeaders = [
        'User-Agent' => 'KarianaE2ETestClient/1.0',
        'Accept'     => 'text/html,application/xhtml+xml,application/xml,application/json,*/*',
    ];

    public function __construct(string $baseUrl = 'http://127.0.0.1:8015', bool $forceKernel = false)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->forceKernel = $forceKernel;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function setBaseUrl(string $url): void
    {
        $this->baseUrl = rtrim($url, '/');
        $this->serverOnline = null;
    }

    public function setForceKernel(bool $force): void
    {
        $this->forceKernel = $force;
    }

    public function isKernelMode(): bool
    {
        return $this->forceKernel || !$this->isServerOnline();
    }

    public function clearCookies(): void
    {
        $this->cookies = [];
    }

    /**
     * Check if the dedicated port 8015 HTTP server is online
     */
    public function isServerOnline(): bool
    {
        if ($this->forceKernel) {
            return false;
        }

        if ($this->serverOnline !== null) {
            return $this->serverOnline;
        }

        $parts = parse_url($this->baseUrl);
        $host = $parts['host'] ?? '127.0.0.1';
        $port = $parts['port'] ?? 8015;

        $fp = @fsockopen($host, (int)$port, $errno, $errstr, 0.4);
        if ($fp) {
            fclose($fp);
            $this->serverOnline = true;
        } else {
            $this->serverOnline = false;
        }

        return $this->serverOnline;
    }

    /**
     * Perform GET request
     */
    public function get(string $uri, array $headers = []): TestResponse
    {
        return $this->request('GET', $uri, [], $headers);
    }

    /**
     * Perform POST request
     */
    public function post(string $uri, array $data = [], array $headers = []): TestResponse
    {
        return $this->request('POST', $uri, $data, $headers);
    }

    /**
     * Perform DELETE request
     */
    public function delete(string $uri, array $data = [], array $headers = []): TestResponse
    {
        return $this->request('DELETE', $uri, $data, $headers);
    }

    /**
     * Perform unified request
     */
    public function request(string $method, string $uri, array $data = [], array $headers = []): TestResponse
    {
        $cleanUri = '/' . ltrim($uri, '/');
        $startTime = microtime(true);

        if (!$this->isKernelMode()) {
            try {
                $response = $this->sendHttpRequest($method, $cleanUri, $data, $headers);
                $duration = (microtime(true) - $startTime) * 1000.0;
                return new TestResponse(
                    $response['status'],
                    $response['headers'],
                    $response['body'],
                    $duration,
                    $this->baseUrl . $cleanUri
                );
            } catch (\Throwable $e) {
                // If live HTTP connection fails, seamlessly fallback to kernel mock
                $this->serverOnline = false;
            }
        }

        // Execute via internal Kernel mock
        $res = $this->dispatchKernel($method, $cleanUri, $data, $headers);
        $duration = (microtime(true) - $startTime) * 1000.0;
        return new TestResponse(
            $res['status'],
            $res['headers'],
            $res['body'],
            $duration,
            'kernel://' . $cleanUri
        );
    }

    /**
     * Send real HTTP request via cURL or stream wrapper
     */
    private function sendHttpRequest(string $method, string $uri, array $data, array $customHeaders): array
    {
        $fullUrl = $this->baseUrl . $uri;
        $headers = array_merge($this->defaultHeaders, $customHeaders);

        // Add cookie header if cookies present
        if (!empty($this->cookies)) {
            $cookieStr = [];
            foreach ($this->cookies as $name => $val) {
                $cookieStr[] = "{$name}={$val}";
            }
            $headers['Cookie'] = implode('; ', $cookieStr);
        }

        if (function_exists('curl_init')) {
            return $this->sendCurlRequest($method, $fullUrl, $data, $headers);
        }

        return $this->sendStreamRequest($method, $fullUrl, $data, $headers);
    }

    private function sendCurlRequest(string $method, string $url, array $data, array $headers): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $formattedHeaders = [];
        foreach ($headers as $k => $v) {
            $formattedHeaders[] = "{$k}: {$v}";
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $formattedHeaders);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        } elseif ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            }
        }

        $raw = curl_exec($ch);
        if ($raw === false) {
            $err = curl_error($ch);
            curl_close($ch);
            throw new \RuntimeException("cURL Request failed: {$err}");
        }

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        $rawHeaders = substr($raw, 0, $headerSize);
        $body = substr($raw, $headerSize);
        $parsedHeaders = $this->parseHeaders($rawHeaders);

        // Extract cookies
        $this->storeCookiesFromHeaders($parsedHeaders);

        return [
            'status'  => $statusCode,
            'headers' => $parsedHeaders,
            'body'    => $body,
        ];
    }

    private function sendStreamRequest(string $method, string $url, array $data, array $headers): array
    {
        $formattedHeaders = [];
        foreach ($headers as $k => $v) {
            $formattedHeaders[] = "{$k}: {$v}";
        }

        $options = [
            'http' => [
                'method'          => $method,
                'header'          => implode("\r\n", $formattedHeaders),
                'timeout'         => 10,
                'ignore_errors'   => true,
                'follow_location' => 0,
            ],
        ];

        if ($method === 'POST' && !empty($data)) {
            $options['http']['content'] = http_build_query($data);
            $options['http']['header'] .= "\r\nContent-Type: application/x-www-form-urlencoded";
        }

        $context = stream_context_create($options);
        $body = @file_get_contents($url, false, $context);
        $rawHeaders = $http_response_header ?? [];

        $statusCode = 200;
        if (!empty($rawHeaders) && preg_match('#HTTP/\d\.\d\s+(\d+)#', $rawHeaders[0], $m)) {
            $statusCode = (int)$m[1];
        }

        $parsedHeaders = $this->parseHeaders(implode("\r\n", $rawHeaders));
        $this->storeCookiesFromHeaders($parsedHeaders);

        return [
            'status'  => $statusCode,
            'headers' => $parsedHeaders,
            'body'    => $body !== false ? $body : '',
        ];
    }

    private function parseHeaders(string $rawHeaders): array
    {
        $headers = [];
        $lines = explode("\r\n", trim($rawHeaders));
        foreach ($lines as $line) {
            if (str_contains($line, ':')) {
                [$key, $val] = explode(':', $line, 2);
                $cleanKey = trim($key);
                $cleanVal = trim($val);
                if (isset($headers[$cleanKey])) {
                    if (!is_array($headers[$cleanKey])) {
                        $headers[$cleanKey] = [$headers[$cleanKey]];
                    }
                    $headers[$cleanKey][] = $cleanVal;
                } else {
                    $headers[$cleanKey] = $cleanVal;
                }
            }
        }
        return $headers;
    }

    private function storeCookiesFromHeaders(array $headers): void
    {
        foreach ($headers as $key => $value) {
            if (strtolower($key) === 'set-cookie') {
                $cookieLines = is_array($value) ? $value : [$value];
                foreach ($cookieLines as $line) {
                    $parts = explode(';', $line);
                    $nv = explode('=', trim($parts[0]), 2);
                    if (count($nv) === 2) {
                        $this->cookies[trim($nv[0])] = trim($nv[1]);
                    }
                }
            }
        }
    }

    /**
     * Dispatch in-memory request directly through Application Kernel
     */
    public function dispatchKernel(string $method, string $uri, array $data = [], array $headers = []): array
    {
        $projectRoot = dirname(__DIR__, 2);
        
        // Ensure Autoloader is loaded
        if (!class_exists(\Core\Autoloader::class)) {
            require_once $projectRoot . '/core/Autoloader.php';
            \Core\Autoloader::register();
        }

        // Backup existing globals
        $origServer = $_SERVER;
        $origGet = $_GET;
        $origPost = $_POST;
        $origFiles = $_FILES;
        $origCookie = $_COOKIE;

        // Parse query string if present in URI
        $parsed = parse_url($uri);
        $path = $parsed['path'] ?? '/';
        $queryString = $parsed['query'] ?? '';
        $queryParams = [];
        if ($queryString !== '') {
            parse_str($queryString, $queryParams);
        }

        // Setup mock environment
        $_SERVER['REQUEST_METHOD'] = strtoupper($method);
        $_SERVER['REQUEST_URI'] = $uri;
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['HTTP_HOST'] = '127.0.0.1:8015';
        $_SERVER['SERVER_PORT'] = '8015';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['QUERY_STRING'] = $queryString;
        
        foreach ($headers as $hk => $hv) {
            $serverKey = 'HTTP_' . strtoupper(str_replace('-', '_', $hk));
            $_SERVER[$serverKey] = $hv;
        }

        $_GET = $queryParams;
        $_POST = $method === 'POST' ? $data : [];
        $_COOKIE = $this->cookies;

        $status = 200;
        $capturedHeaders = [];
        $body = '';

        try {
            // Check if index.php defines full routes or router setup
            // Or instantiate Core\App and execute
            $request = new \Core\Request();
            
            // Check if App has a router or if routes need to be loaded
            $app = \Core\App::getInstance();
            if ($app === null) {
                $app = new \Core\App();
            }

            $router = $app->getRouter();

            // If router has no routes registered, load routes from index.php or routes registry
            $refRouter = new \ReflectionClass($router);
            $propRoutes = $refRouter->getProperty('routes');
            $propRoutes->setAccessible(true);
            $currentRoutes = $propRoutes->getValue($router);

            if (empty($currentRoutes)) {
                $indexFile = $projectRoot . '/index.php';
                if (file_exists($indexFile)) {
                    // Start output buffering to capture any output from index.php
                    ob_start();
                    try {
                        require_once $indexFile;
                    } catch (\Throwable $ie) {
                        // ignore if index.php already executed
                    }
                    ob_end_clean();
                }
            }

            // Dispatch request
            $response = $router->dispatch($request);
            $status = $response->getStatusCode();
            
            // Reflect headers from response
            $refRes = new \ReflectionClass($response);
            if ($refRes->hasProperty('headers')) {
                $propH = $refRes->getProperty('headers');
                $propH->setAccessible(true);
                $capturedHeaders = $propH->getValue($response);
            }
            $body = $response->getContent();

        } catch (\Throwable $e) {
            $status = 500;
            $body = "Kernel Dispatch Error: " . $e->getMessage() . "\n" . $e->getTraceAsString();
        } finally {
            // Restore globals
            $_SERVER = $origServer;
            $_GET = $origGet;
            $_POST = $origPost;
            $_FILES = $origFiles;
            $_COOKIE = $origCookie;
        }

        return [
            'status'  => $status,
            'headers' => $capturedHeaders,
            'body'    => $body,
        ];
    }

    /**
     * Helper to retrieve CSRF token by fetching a page
     */
    public function extractCsrfFrom(string $path = '/courses'): string
    {
        $response = $this->get($path);
        $token = $response->extractCsrfToken();
        if ($token === null) {
            // If page does not render form directly, generate from Core\Csrf
            if (class_exists(\Core\Csrf::class)) {
                return \Core\Csrf::token();
            }
            return 'mock_csrf_token_' . bin2hex(random_bytes(16));
        }
        return $token;
    }

    /**
     * Alias for extractCsrfFrom to fetch CSRF token from a path
     */
    public function fetchCsrfToken(string $path = '/courses'): string
    {
        return $this->extractCsrfFrom($path);
    }
}
