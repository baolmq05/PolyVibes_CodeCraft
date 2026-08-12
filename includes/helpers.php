<?php
declare(strict_types=1);

/**
 * Chuyển chuỗi tiếng Việt thành slug URL-safe.
 */
function slugify(string $str): string
{
    $map = [
        'à'=>'a','á'=>'a','ạ'=>'a','ả'=>'a','ã'=>'a',
        'â'=>'a','ầ'=>'a','ấ'=>'a','ậ'=>'a','ẩ'=>'a','ẫ'=>'a',
        'ă'=>'a','ằ'=>'a','ắ'=>'a','ặ'=>'a','ẳ'=>'a','ẵ'=>'a',
        'è'=>'e','é'=>'e','ẹ'=>'e','ẻ'=>'e','ẽ'=>'e',
        'ê'=>'e','ề'=>'e','ế'=>'e','ệ'=>'e','ể'=>'e','ễ'=>'e',
        'ì'=>'i','í'=>'i','ị'=>'i','ỉ'=>'i','ĩ'=>'i',
        'ò'=>'o','ó'=>'o','ọ'=>'o','ỏ'=>'o','õ'=>'o',
        'ô'=>'o','ồ'=>'o','ố'=>'o','ộ'=>'o','ổ'=>'o','ỗ'=>'o',
        'ơ'=>'o','ờ'=>'o','ớ'=>'o','ợ'=>'o','ở'=>'o','ỡ'=>'o',
        'ù'=>'u','ú'=>'u','ụ'=>'u','ủ'=>'u','ũ'=>'u',
        'ư'=>'u','ừ'=>'u','ứ'=>'u','ự'=>'u','ử'=>'u','ữ'=>'u',
        'ỳ'=>'y','ý'=>'y','ỵ'=>'y','ỷ'=>'y','ỹ'=>'y','đ'=>'d',
        'À'=>'a','Á'=>'a','Ạ'=>'a','Ả'=>'a','Ã'=>'a',
        'Â'=>'a','Ầ'=>'a','Ấ'=>'a','Ậ'=>'a','Ẩ'=>'a','Ẫ'=>'a',
        'Ă'=>'a','Ằ'=>'a','Ắ'=>'a','Ặ'=>'a','Ẳ'=>'a','Ẵ'=>'a',
        'È'=>'e','É'=>'e','Ẹ'=>'e','Ẻ'=>'e','Ẽ'=>'e',
        'Ê'=>'e','Ề'=>'e','Ế'=>'e','Ệ'=>'e','Ể'=>'e','Ễ'=>'e',
        'Ì'=>'i','Í'=>'i','Ị'=>'i','Ỉ'=>'i','Ĩ'=>'i',
        'Ò'=>'o','Ó'=>'o','Ọ'=>'o','Ỏ'=>'o','Õ'=>'o',
        'Ô'=>'o','Ồ'=>'o','Ố'=>'o','Ộ'=>'o','Ổ'=>'o','Ỗ'=>'o',
        'Ơ'=>'o','Ờ'=>'o','Ớ'=>'o','Ợ'=>'o','Ở'=>'o','Ỡ'=>'o',
        'Ù'=>'u','Ú'=>'u','Ụ'=>'u','Ủ'=>'u','Ũ'=>'u',
        'Ư'=>'u','Ừ'=>'u','Ứ'=>'u','Ự'=>'u','Ử'=>'u','Ữ'=>'u',
        'Ỳ'=>'y','Ý'=>'y','Ỵ'=>'y','Ỷ'=>'y','Ỹ'=>'y','Đ'=>'d',
    ];
    $str = strtr($str, $map);
    $str = mb_strtolower(trim($str), 'UTF-8');
    $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
    $str = preg_replace('/[\s-]+/', '-', $str);
    return trim($str, '-');
}

/**
 * Sleep ngẫu nhiên giữa các request để tránh bị chặn IP.
 */
function randomSleep(): void
{
    $min = defined('CRAWL_SLEEP_MIN') ? CRAWL_SLEEP_MIN : 2;
    $max = defined('CRAWL_SLEEP_MAX') ? CRAWL_SLEEP_MAX : 5;
    sleep(rand($min, $max));
}

/**
 * Ghi kết quả crawl vào bảng crawl_log.
 */
function logCrawl(PDO $pdo, string $url, string $ketQua, string $ghiChu = ''): void
{
    try {
        $pdo->prepare(
            'INSERT INTO crawl_log (url, ket_qua, ghi_chu) VALUES (?, ?, ?)'
        )->execute([$url, $ketQua, mb_substr($ghiChu, 0, 2000)]);
    } catch (PDOException) {
        // Không dừng toàn bộ tiến trình nếu ghi log thất bại
    }
}

/**
 * Lấy hoặc tạo mới bản ghi trong bảng lookup.
 * Dùng chung cho tinh_thanh / phuong_xa / loai_hinh_doanh_nghiep / nganh_nghe.
 *
 * @param array<string,mixed> $extra  Cột bổ sung (vd: ['tinh_thanh_id' => 5] cho phuong_xa)
 */
function upsertLookup(PDO $pdo, string $table, string $ten, array $extra = []): ?int
{
    $ten = trim($ten);
    if ($ten == '') {
        return null;
    }

    // Whitelist bảng để tránh SQL injection
    static $allowed = ['tinh_thanh', 'phuong_xa', 'loai_hinh_doanh_nghiep', 'nganh_nghe'];
    if (!in_array($table, $allowed, true)) {
        return null;
    }

    // phuong_xa yêu cầu tinh_thanh_id để đảm bảo unique
    if ($table == 'phuong_xa' && empty($extra['tinh_thanh_id'])) {
        return null;
    }

    $slug = slugify($ten);
    if ($slug == '') {
        return null;
    }

    // Tìm bản ghi đã tồn tại
    $whereSQL  = 'slug = ?';
    $whereVals = [$slug];
    if ($table == 'phuong_xa') {
        $whereSQL   .= ' AND tinh_thanh_id = ?';
        $whereVals[] = $extra['tinh_thanh_id'];
    }

    $stmt = $pdo->prepare("SELECT id FROM `{$table}` WHERE {$whereSQL} LIMIT 1");
    $stmt->execute($whereVals);
    $id = $stmt->fetchColumn();
    if ($id !== false) {
        return (int) $id;
    }

    // Insert mới
    $cols = array_merge(['ten', 'slug'], array_keys($extra));
    $vals = array_merge([$ten, $slug], array_values($extra));
    $colSQL  = implode(', ', array_map(fn($c) => "`{$c}`", $cols));
    $phSQL   = implode(', ', array_fill(0, count($cols), '?'));

    try {
        $pdo->prepare("INSERT INTO `{$table}` ({$colSQL}) VALUES ({$phSQL})")->execute($vals);
        return (int) $pdo->lastInsertId();
    } catch (PDOException) {
        // Race condition: thử lại SELECT
        $stmt->execute($whereVals);
        $id = $stmt->fetchColumn();
        return $id !== false ? (int) $id : null;
    }
}

/**
 * Fetch HTML từ URL qua cURL với User-Agent thật.
 * Ném RuntimeException nếu HTTP lỗi hoặc cURL thất bại.
 */
function fetchHtml(string $url, int $timeout = 15): string
{
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 3,
        CURLOPT_TIMEOUT        => $timeout,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_ENCODING       => '',
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) '
                                 .'AppleWebKit/537.36 (KHTML, like Gecko) '
                                 .'Chrome/124.0.0.0 Safari/537.36',
        CURLOPT_HTTPHEADER     => [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7',
            'Cache-Control: no-cache',
        ],
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);

    $body     = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($body === false || $httpCode < 200 || $httpCode >= 400) {
        throw new RuntimeException(
            sprintf('HTTP %d — %s — URL: %s', $httpCode, $curlErr, $url)
        );
    }

    return (string) $body;
}

/**
 * In thông báo ra web (HTML) hoặc CLI.
 */
function output(string $msg): void
{
    if (PHP_SAPI == 'cli') {
        echo $msg . PHP_EOL;
    } else {
        echo htmlspecialchars($msg, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "<br>\n";
        if (ob_get_level() > 0) {
            ob_flush();
        }
        flush();
    }
}

/**
 * Escape output cho HTML — shorthand.
 */
function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
