<?php
namespace Core;

/**
 * Application Kernel & Front Controller Lifecycle Manager
 * Handles boot sequence, global error/exception handling, session aging, and route dispatching
 */
class App
{
    private static ?App $instance = null;
    private Router $router;
    private Request $request;
    private array $config = [];

    public function __construct()
    {
        self::$instance = $this;

        // 1. Load application configuration
        $configFile = dirname(__DIR__) . '/config/app.php';
        if (file_exists($configFile)) {
            $this->config = require $configFile;
        }

        // 2. Set default timezone
        $timezone = $this->config['timezone'] ?? 'Asia/Dhaka';
        date_default_timezone_set($timezone);

        // 3. Register global error and exception handlers
        $this->registerErrorHandlers();

        // 4. Start hardened session and age flash messages
        Session::start();
        Session::ageFlash();

        // 5. Initialize Request & Router
        $this->request = new Request();
        $this->router = new Router();
    }

    public static function getInstance(): ?App
    {
        return self::$instance;
    }

    public function getRouter(): Router
    {
        return $this->router;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getConfig(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Run application lifecycle: dispatch request and send response
     */
    public function run(): void
    {
        try {
            $response = $this->router->dispatch($this->request);
            $response->send();
        } catch (\Throwable $e) {
            $this->handleException($e);
        }
    }

    /**
     * Register standard PHP error and exception handlers
     */
    private function registerErrorHandlers(): void
    {
        set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline) {
            if (!(error_reporting() & $errno)) {
                return false;
            }
            throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
        });

        set_exception_handler([$this, 'handleException']);
    }

    /**
     * Centralized exception renderer
     */
    public function handleException(\Throwable $e): void
    {
        error_log(sprintf(
            "[%s] Uncaught %s: %s in %s on line %d\nStack Trace:\n%s",
            date('Y-m-d H:i:s'),
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        ));

        $debug = (bool)($this->config['debug'] ?? true);
        http_response_code(500);

        if ($debug) {
            echo '<!DOCTYPE html><html lang="bn"><head><meta charset="utf-8">';
            echo '<title>সিস্টেম ত্রুটি | কারিয়ানা কুরআন</title>';
            echo '<style>';
            echo 'body{font-family:system-ui,sans-serif;background:#fff1f2;color:#9f1239;padding:30px;line-height:1.6;}';
            echo '.container{max-width:900px;margin:0 auto;background:#fff;padding:25px;border-radius:8px;box-shadow:0 4px 6px rgba(0,0,0,0.1);border-left:6px solid #e11d48;}';
            echo 'h1{font-size:1.5rem;margin-top:0;color:#881337;}';
            echo 'pre{background:#1e293b;color:#f8fafc;padding:15px;border-radius:6px;overflow-x:auto;font-size:0.875rem;}';
            echo '.file{color:#475569;font-size:0.95rem;margin-bottom:15px;}';
            echo '</style></head><body>';
            echo '<div class="container">';
            echo '<h1>সিস্টেম ত্রুটি (Uncaught Exception)</h1>';
            echo '<p><strong>বার্তা:</strong> ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>';
            echo '<p class="file"><strong>ফাইল:</strong> ' . htmlspecialchars($e->getFile(), ENT_QUOTES, 'UTF-8') . ' (লাইন: ' . $e->getLine() . ')</p>';
            echo '<h3>Stack Trace:</h3>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8') . '</pre>';
            echo '</div></body></html>';
        } else {
            echo '<!DOCTYPE html><html lang="bn"><head><meta charset="utf-8">';
            echo '<title>সার্ভার ত্রুটি (৫০০) | কারিয়ানা কুরআন</title>';
            echo '<style>';
            echo 'body{font-family:sans-serif;text-align:center;padding:50px;background:#fcfbf7;color:#064e3b;}';
            echo 'h1{font-size:2.5rem;color:#064e3b;}p{color:#64748b;font-size:1.1rem;}';
            echo '.btn{display:inline-block;margin-top:20px;padding:10px 24px;background:#047857;color:#fff;text-decoration:none;border-radius:6px;}';
            echo '</style></head><body>';
            echo '<h1>সাময়িক সার্ভার সমস্যা</h1>';
            echo '<p>দুঃখিত, একটি প্রযুক্তিগত ত্রুটি ঘটেছে। আমাদের কারিগরি টিম বিষয়টি খতিয়ে দেখছে।</p>';
            echo '<a class="btn" href="' . ($this->request ? $this->request->getBaseUrl() : '/') . '/">মূল পাতায় ফিরে যান</a>';
            echo '</body></html>';
        }
        exit;
    }
}
