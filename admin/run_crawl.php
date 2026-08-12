<?php
declare(strict_types=1);
/**
 * Bridge: gọi script crawler và stream output vào iframe log.
 * Được gọi từ admin/crawl.php qua target="logframe".
 */
header('Content-Type: text/html; charset=utf-8');
header('X-Content-Type-Options: nosniff');

$action = $_GET['action'] ?? '';
// Sanitize tham số tỉnh
$tinh  = preg_replace('/[^a-z0-9-]/', '', strtolower($_GET['tinh'] ?? ''));
$limit = max(1, min(200, (int) ($_GET['limit'] ?? 20)));

$allowed = ['list' => '../crawler/crawl_list.php', 'detail' => '../crawler/crawl_detail.php'];
if (!isset($allowed[$action])) {
    die('<p style="color:red">Action không hợp lệ.</p>');
}

$script = realpath(__DIR__ . '/' . $allowed[$action]);
// Path traversal guard
if (!$script || !str_starts_with($script, realpath(__DIR__ . '/../crawler'))) {
    die('<p style="color:red">Đường dẫn script không hợp lệ.</p>');
}

// Truyền tham số qua $_GET để script crawler đọc
$_GET['limit'] = $limit;
if ($action === 'list') {
    if ($tinh === '') {
        die('<p style="color:red">Thiếu tham số tỉnh.</p>');
    }
    $_GET['tinh'] = $tinh;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<style>
  body { background: #1e1e2e; color: #cdd6f4; font-family: monospace;
         font-size: 13px; margin: 0; padding: 12px; }
  br   { display: block; margin-bottom: 2px; }
</style>
</head>
<body>
<?php
ob_implicit_flush(true);
if (ob_get_level() > 0) ob_end_flush();

require $script;
?>
</body>
</html>
