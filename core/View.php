<?php
namespace Core;

/**
 * Lightweight View Rendering Engine
 * Supports nested layouts, partials, safe data escaping, and flexible view directory paths
 */
class View
{
    private static ?string $viewsDir = null;

    /**
     * Get or initialize views base directory
     */
    public static function getViewsDir(): string
    {
        if (self::$viewsDir === null) {
            $appViews = dirname(__DIR__) . '/app/Views';
            $views = dirname(__DIR__) . '/views';

            if (is_dir($appViews)) {
                self::$viewsDir = $appViews;
            } elseif (is_dir($views)) {
                self::$viewsDir = $views;
            } else {
                self::$viewsDir = $appViews;
            }
        }
        return self::$viewsDir;
    }

    public static function setViewsDir(string $dir): void
    {
        self::$viewsDir = rtrim($dir, '/\\');
    }

    /**
     * Render a view within a layout
     * 
     * @param string $view Relative view path (e.g. 'home/index' or 'blog/show')
     * @param array $data Variables to pass to the view
     * @param string|null $layout Layout path (default 'layouts/main'), or null/false to disable layout
     * @return string Rendered HTML output
     */
    public static function render(string $view, array $data = [], ?string $layout = 'layouts/main'): string
    {
        $viewFile = self::resolveViewFile($view);

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View template file not found: {$view} (Checked: {$viewFile})");
        }

        // Shared view variables
        $request = new Request();
        $data['baseUrl'] = $request->getBaseUrl();
        $data['currentPath'] = $request->getPath();
        $data['csrfToken'] = Csrf::token();
        $data['csrfField'] = Csrf::field();
        $data['user'] = Session::getUser();
        $data['isLoggedIn'] = Session::isLoggedIn();

        // Render inner view
        $content = self::renderFile($viewFile, $data);

        // If no layout is specified, return raw view content
        if ($layout === null || $layout === '' || $layout === false) {
            return $content;
        }

        // Render wrapping layout
        $layoutFile = self::resolveViewFile($layout);
        if (!file_exists($layoutFile)) {
            // If layout doesn't exist, fallback to raw content
            return $content;
        }

        $data['content'] = $content;
        return self::renderFile($layoutFile, $data);
    }

    /**
     * Render a partial view template (without layout)
     */
    public static function partial(string $partial, array $data = []): string
    {
        return self::render($partial, $data, null);
    }

    /**
     * Resolve view file path, adding .php extension if missing
     */
    private static function resolveViewFile(string $view): string
    {
        $clean = ltrim($view, '/\\');
        if (!str_ends_with($clean, '.php')) {
            $clean .= '.php';
        }

        $baseDir = self::getViewsDir();
        $primary = $baseDir . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $clean);

        if (file_exists($primary)) {
            return $primary;
        }

        // Secondary check in alternate views folder
        $altDir = dirname(__DIR__) . '/views';
        $secondary = $altDir . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $clean);
        if (file_exists($secondary)) {
            return $secondary;
        }

        return $primary;
    }

    /**
     * Internal file rendering via output buffering
     */
    private static function renderFile(string $filePath, array $data): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        include $filePath;
        return ob_get_clean() ?: '';
    }

    /**
     * Helper to escape HTML characters
     */
    public static function escape(mixed $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
