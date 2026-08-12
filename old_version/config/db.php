<?php
declare(strict_types=1);

define('DB_HOST',    'localhost');
define('DB_NAME',    'thongtindoanhnghiep');
define('DB_USER',    'root');
define('DB_PASS',    'mysql');            // Thay bằng mật khẩu thật của máy
define('DB_CHARSET', 'utf8mb4');

// Cấu hình crawl
define('CRAWL_SLEEP_MIN',  2);   // giây
define('CRAWL_SLEEP_MAX',  5);   // giây
define('CRAWL_TIMEOUT',    15);  // giây cho mỗi request
define('CRAWL_MAX_RETRY',  3);   // số lần thử lại khi lỗi
define('CRAWL_BATCH_SIZE', 20);  // số URL xử lý mỗi lần chạy crawl_detail

function getPDO(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST, DB_NAME, DB_CHARSET
        );
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}
