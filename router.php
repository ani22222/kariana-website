<?php
/**
 * CLI Built-in Web Server Router
 * Dedicated Port: 8015 (Bound to 0.0.0.0 for LAN/Wi-Fi Multi-Device Access)
 * Usage: php -S 0.0.0.0:8015 router.php
 */

declare(strict_types=1);

$rawUri = $_SERVER['REQUEST_URI'] ?? '/';
$parsedPath = parse_url($rawUri, PHP_URL_PATH) ?? '/';
$decodedPath = rawurldecode($parsedPath);

// 1. Directory Traversal Defense: Reject any attempt to climb directory trees
if (str_contains($decodedPath, '..') || str_contains($decodedPath, '\\')) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=UTF-8');
    echo '400 Bad Request: Invalid path traversal sequence detected.';
    exit;
}

// 2. Dotfile / Dotdirectory Defense: Block all hidden entities (.env, .git, .agents, .htaccess)
$segments = array_values(array_filter(explode('/', trim($decodedPath, '/'))));
foreach ($segments as $segment) {
    if (str_starts_with($segment, '.')) {
        http_response_code(403);
        header('Content-Type: text/plain; charset=UTF-8');
        echo '403 Forbidden: Access to hidden files or directories is denied.';
        exit;
    }
}

// 3. Protected Application Directories & System Scripts Defense
$protectedDirs = ['app', 'config', 'core', 'database', 'storage', 'tests', '.agents', '.git', 'vendor'];
$firstSegment = strtolower($segments[0] ?? '');
if (in_array($firstSegment, $protectedDirs, true) || strtolower(basename($decodedPath)) === 'router.php') {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    echo '403 Forbidden: Direct access to internal application directories or system scripts is denied.';
    exit;
}

// 4. Sensitive Extension Defense: Block direct retrieval of sensitive file formats
$ext = strtolower(pathinfo($decodedPath, PATHINFO_EXTENSION));
$blockedExtensions = ['sql', 'md', 'json', 'env', 'log', 'ini', 'lock', 'yml', 'yaml', 'bak', 'sh', 'bat'];
if ($ext === 'json' && basename($decodedPath) === 'manifest.json') {
    // Permit PWA web app manifest
} elseif (in_array($ext, $blockedExtensions, true)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    echo '403 Forbidden: Direct access to this file type is denied.';
    exit;
}

// 5. Safe Static Asset Resolution: Strictly isolated to the public/ directory
$publicDir = realpath(__DIR__ . '/public');
if ($publicDir !== false) {
    // Normalization: strip leading /public if already present in request path
    $relativeAssetPath = $decodedPath;
    if (str_starts_with($relativeAssetPath, '/public/')) {
        $relativeAssetPath = substr($relativeAssetPath, 7);
    }

    $candidateFile = $publicDir . DIRECTORY_SEPARATOR . ltrim(str_replace('/', DIRECTORY_SEPARATOR, $relativeAssetPath), DIRECTORY_SEPARATOR);

    if (file_exists($candidateFile) && is_file($candidateFile)) {
        $resolvedCandidate = realpath($candidateFile);
        
        // Ensure resolved realpath is strictly located inside the public/ root
        if ($resolvedCandidate !== false && str_starts_with($resolvedCandidate, $publicDir)) {
            $fileExt = strtolower(pathinfo($resolvedCandidate, PATHINFO_EXTENSION));

            $allowedMimes = [
                'css'   => 'text/css; charset=UTF-8',
                'js'    => 'application/javascript; charset=UTF-8',
                'png'   => 'image/png',
                'jpg'   => 'image/jpeg',
                'jpeg'  => 'image/jpeg',
                'gif'   => 'image/gif',
                'webp'  => 'image/webp',
                'svg'   => 'image/svg+xml',
                'ico'   => 'image/x-icon',
                'ttf'   => 'font/ttf',
                'woff'  => 'font/woff',
                'woff2' => 'font/woff2',
                'eot'   => 'application/vnd.ms-fontobject',
                'otf'   => 'font/otf',
                'pdf'   => 'application/pdf',
                'mp3'   => 'audio/mpeg',
                'mp4'   => 'video/mp4',
                'txt'   => 'text/plain; charset=UTF-8',
                'xml'   => 'application/xml; charset=UTF-8',
                'json'  => 'application/manifest+json; charset=UTF-8',
            ];

            if (isset($allowedMimes[$fileExt])) {
                header("Content-Type: {$allowedMimes[$fileExt]}");
                header("Content-Length: " . (string)filesize($resolvedCandidate));
                header("Cache-Control: public, max-age=604800");
                header("X-Content-Type-Options: nosniff");
                readfile($resolvedCandidate);
                exit;
            }

            http_response_code(403);
            header('Content-Type: text/plain; charset=UTF-8');
            echo '403 Forbidden: File type not permitted for public static delivery.';
            exit;
        }
    }
}

// 6. Forward all application web requests to the Front Controller
require_once __DIR__ . '/index.php';
