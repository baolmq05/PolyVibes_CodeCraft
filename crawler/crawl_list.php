<?php
declare(strict_types=1);
/**
 * Crawl danh sách doanh nghiệp theo tỉnh từ masothue.com.
 * Thêm URL chi tiết vào crawl_queue (bỏ qua URL đã tồn tại).
 *
 * CLI:  php crawler/crawl_list.php can-tho-96 [so_trang_toi_da]
 * Web:  /admin/run_crawl.php?action=list&tinh=can-tho-96&limit=5
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/simple_html_dom.php';
require_once __DIR__ . '/../includes/helpers.php';

// ── Tham số đầu vào ──────────────────────────────────────────
if (PHP_SAPI === 'cli') {
    $tinhSlug  = $argv[1] ?? '';
    $pageLimit = isset($argv[2]) ? (int) $argv[2] : 0;
} else {
    // Chỉ cho phép ký tự an toàn trong slug
    $tinhSlug  = preg_replace('/[^a-z0-9-]/', '', strtolower($_GET['tinh'] ?? ''));
    $pageLimit = max(0, min(200, (int) ($_GET['limit'] ?? 5)));
}

if ($tinhSlug === '') {
    output('Thiếu tham số tỉnh. Ví dụ: can-tho-96 [số_trang]');
    if (PHP_SAPI === 'cli') {
        exit(1);
    }
    return;
}

// ── Khởi động ─────────────────────────────────────────────────
$pdo      = getPDO();
$baseUrl  = "https://masothue.com/tra-cuu-ma-so-thue-theo-tinh/{$tinhSlug}";
$page     = 1;
$inserted = 0;
$skipped  = 0;
$timeout  = defined('CRAWL_TIMEOUT') ? CRAWL_TIMEOUT : 15;

output("▶ Bắt đầu crawl danh sách: {$tinhSlug}");
output("  Base URL: {$baseUrl}");

// Câu lệnh kiểm tra và insert queue
$stmtCheckUrl = $pdo->prepare('SELECT id FROM crawl_queue WHERE url = ? LIMIT 1');
$stmtCheckMst = $pdo->prepare('SELECT id FROM doanh_nghiep WHERE mst = ? LIMIT 1');
$stmtInsert   = $pdo->prepare('INSERT IGNORE INTO crawl_queue (url) VALUES (?)');

while (true) {
    if ($pageLimit > 0 && $page > $pageLimit) {
        output("  Đạt giới hạn {$pageLimit} trang — dừng.");
        break;
    }

    $url = ($page == 1) ? $baseUrl : "{$baseUrl}?page={$page}";
    output("  Trang {$page}: {$url}");

    try {
        $html = fetchHtml($url, $timeout);
    } catch (RuntimeException $e) {
        output("  ✗ Lỗi fetch: " . $e->getMessage());
        logCrawl($pdo, $url, 'that_bai', $e->getMessage());
        break;
    }

    $dom    = str_get_html($html);
    $blocks = $dom ? $dom->find('[data-prefetch]') : [];

    if (empty($blocks)) {
        output("  Không tìm thấy block nào — kết thúc phân trang.");
        if ($dom) $dom->clear();
        break;
    }

    // Kiểm tra có trang tiếp theo không (trước khi xử lý từng block)
    $hasNextPage = ($dom->find('a[rel=next]', 0) != null)
                || ($dom->find('.pagination .next:not(.disabled)', 0) != null);

    $totalBlocks = count($blocks);
    foreach ($blocks as $idx => $block) {
        $currentNum = $idx + 1;
        $path = trim($block->getAttribute('data-prefetch'));
        if ($path == '' || !str_starts_with($path, '/')) {
            continue;
        }

        $detailUrl = 'https://masothue.com' . $path;

        // Kiểm tra URL đã có trong queue chưa
        $stmtCheckUrl->execute([$detailUrl]);
        if ($stmtCheckUrl->fetchColumn() !== false) {
            $skipped++;
            output("    ~ [{$currentNum}/{$totalBlocks}] Đã có trong hàng đợi: {$detailUrl}");
            continue;
        }

        // Kiểm tra MST đã crawl xong chưa (tách từ path /{mst}-{slug})
        if (preg_match('#^/([0-9]+(?:-[0-9]+)*)-#', $path, $m)) {
            $mst = str_replace('-', '', $m[1]);
            $stmtCheckMst->execute([$mst]);
            if ($stmtCheckMst->fetchColumn() !== false) {
                $skipped++;
                output("    ~ [{$currentNum}/{$totalBlocks}] Đã lưu doanh nghiệp trước đó (MST: {$mst}): {$detailUrl}");
                continue;
            }
        }

        $stmtInsert->execute([$detailUrl]);
        if ($stmtInsert->rowCount() > 0) {
            $inserted++;
            output("    + [{$currentNum}/{$totalBlocks}] Thêm mới vào hàng đợi: {$detailUrl}");
        } else {
            $skipped++;
            output("    ~ [{$currentNum}/{$totalBlocks}] Đã bỏ qua: {$detailUrl}");
        }
    }

    $dom->clear();

    if (!$hasNextPage) {
        output("  Không có trang tiếp theo — dừng.");
        break;
    }

    $page++;
    randomSleep();
}

output("<span style='color: #28a745; font-weight: bold;'>✔ Hoàn tất. Đã thêm: {$inserted} | Bỏ qua: {$skipped}</span>", true);
output("<script>alert('Hoàn tất cào danh sách doanh nghiệp! Đã thêm {$inserted} URL mới.');</script>", true);
