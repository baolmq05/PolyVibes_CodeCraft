<?php
declare(strict_types=1);
// Sao chép file này thành db.php và điền thông tin kết nối thật.
// KHÔNG commit db.php lên repo công khai.

define('DB_HOST',    'localhost');
define('DB_NAME',    'thongtindoanhnghiep');
define('DB_USER',    'root');
define('DB_PASS',    'YOUR_PASSWORD_HERE');
define('DB_CHARSET', 'utf8mb4');

define('CRAWL_SLEEP_MIN',  2);
define('CRAWL_SLEEP_MAX',  5);
define('CRAWL_TIMEOUT',    15);
define('CRAWL_MAX_RETRY',  3);
define('CRAWL_BATCH_SIZE', 20);

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
