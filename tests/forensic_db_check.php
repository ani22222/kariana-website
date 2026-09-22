<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

try {
    $configFile = dirname(__DIR__) . '/config/database.php';
    if (!file_exists($configFile)) {
        echo json_encode(['error' => 'Database config file missing'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    $config = require $configFile;
    $host = $config['host'] ?? 'localhost';
    $port = $config['port'] ?? 3306;
    $dbname = $config['database'] ?? 'kariana_portal';
    $username = $config['username'] ?? 'root';
    $password = $config['password'] ?? '';

    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ]);

    // 1. Check current database and server version
    $dbNameStmt = $pdo->query("SELECT DATABASE() as db_name, VERSION() as version");
    $dbMeta = $dbNameStmt->fetch();

    // 2. Query all tables in kariana_portal
    $tablesStmt = $pdo->prepare("
        SELECT TABLE_NAME, TABLE_TYPE, ENGINE, TABLE_ROWS, TABLE_COLLATION 
        FROM information_schema.TABLES 
        WHERE TABLE_SCHEMA = :schema
        ORDER BY TABLE_NAME
    ");
    $tablesStmt->execute(['schema' => $dbname]);
    $tables = $tablesStmt->fetchAll();

    // 3. Query columns for each table
    $columnsStmt = $pdo->prepare("
        SELECT TABLE_NAME, COLUMN_NAME, ORDINAL_POSITION, COLUMN_DEFAULT, IS_NULLABLE, DATA_TYPE, COLUMN_TYPE, COLUMN_KEY, EXTRA
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = :schema
        ORDER BY TABLE_NAME, ORDINAL_POSITION
    ");
    $columnsStmt->execute(['schema' => $dbname]);
    $allColumns = $columnsStmt->fetchAll();

    $tableColumns = [];
    foreach ($allColumns as $col) {
        $t = $col['TABLE_NAME'];
        if (!isset($tableColumns[$t])) {
            $tableColumns[$t] = [];
        }
        $tableColumns[$t][] = [
            'name' => $col['COLUMN_NAME'],
            'type' => $col['COLUMN_TYPE'],
            'nullable' => $col['IS_NULLABLE'],
            'key' => $col['COLUMN_KEY'],
            'default' => $col['COLUMN_DEFAULT'],
            'extra' => $col['EXTRA'],
        ];
    }

    // 4. Exact counts for all 12 tables
    $tableCounts = [];
    $expected12Tables = [
        'users', 'categories', 'posts', 'courses', 'admissions',
        'books', 'pages', 'qr_lessons', 'prayer_districts',
        'zakat_settings', 'site_settings', 'migrations'
    ];

    foreach ($expected12Tables as $tbl) {
        try {
            $c = (int)$pdo->query("SELECT COUNT(*) FROM `{$tbl}`")->fetchColumn();
            $tableCounts[$tbl] = $c;
        } catch (\Throwable $e) {
            $tableCounts[$tbl] = 'ERROR: ' . $e->getMessage();
        }
    }

    // 5. Query all 64 districts details
    $districtsStmt = $pdo->query("
        SELECT id, name_bn, name_en, division_bn, latitude, longitude, 
               fajr_offset, sunrise_offset, dhuhr_offset, asr_offset, maghrib_offset, isha_offset 
        FROM `prayer_districts` 
        ORDER BY id ASC
    ");
    $districts = $districtsStmt->fetchAll();

    // 6. Inspect font file directly via PHP
    $fontPath = dirname(__DIR__) . '/public/assets/fonts/AAR-SQ-003.ttf';
    $fontExists = file_exists($fontPath);
    $fontSize = $fontExists ? filesize($fontPath) : 0;
    $fontHeader = '';
    if ($fontExists) {
        $fp = fopen($fontPath, 'rb');
        $headerBytes = fread($fp, 12);
        fclose($fp);
        $fontHeader = bin2hex($headerBytes);
    }

    // 7. Inspect admin user
    $adminStmt = $pdo->query("SELECT id, name, email, username, password, role, created_at FROM `users` WHERE username = 'admin'");
    $adminUser = $adminStmt->fetch();

    echo json_encode([
        'success' => true,
        'db_meta' => $dbMeta,
        'tables_count' => count($tables),
        'tables' => $tables,
        'columns' => $tableColumns,
        'row_counts' => $tableCounts,
        'districts_count' => count($districts),
        'districts_sample' => array_slice($districts, 0, 5),
        'districts_all_divisions' => array_values(array_unique(array_column($districts, 'division_bn'))),
        'districts_count_by_division' => array_count_values(array_column($districts, 'division_bn')),
        'admin_user' => [
            'id' => $adminUser['id'] ?? null,
            'name' => $adminUser['name'] ?? null,
            'email' => $adminUser['email'] ?? null,
            'username' => $adminUser['username'] ?? null,
            'role' => $adminUser['role'] ?? null,
            'password_hash_prefix' => substr($adminUser['password'] ?? '', 0, 7), // e.g. $2y$10$
            'password_verify_admin123' => isset($adminUser['password']) ? password_verify('admin123', $adminUser['password']) : false,
        ],
        'font_file' => [
            'path' => $fontPath,
            'exists' => $fontExists,
            'size' => $fontSize,
            'header_hex' => $fontHeader,
            'is_truetype_header' => str_starts_with($fontHeader, '00010000') || str_starts_with($fontHeader, '74727565'),
        ],
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (\Throwable $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
