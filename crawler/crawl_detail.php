<?php
declare(strict_types=1);
/**
 * Lấy URL từ crawl_queue (trạng thái 'cho' hoặc 'that_bai' còn lần thử),
 * fetch trang chi tiết masothue.com, parse và upsert vào bảng doanh_nghiep.
 *
 * CLI:  php crawler/crawl_detail.php [so_ban_ghi]
 * Web:  /admin/run_crawl.php?action=detail&limit=20
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/simple_html_dom.php';
require_once __DIR__ . '/../includes/helpers.php';

// ── Tham số ───────────────────────────────────────────────────
if (PHP_SAPI == 'cli') {
    $limit = isset($argv[1]) ? (int) $argv[1]
                             : (defined('CRAWL_BATCH_SIZE') ? CRAWL_BATCH_SIZE : 20);
} else {
    $limit = max(1, min(100, (int) ($_GET['limit'] ?? (defined('CRAWL_BATCH_SIZE') ? CRAWL_BATCH_SIZE : 20))));
}

$maxRetry = defined('CRAWL_MAX_RETRY') ? CRAWL_MAX_RETRY : 3;
$timeout  = defined('CRAWL_TIMEOUT')   ? CRAWL_TIMEOUT   : 15;
$pdo      = getPDO();

// ── Lấy hàng đợi ──────────────────────────────────────────────
$queue = $pdo->prepare(
    "SELECT id, url FROM crawl_queue
     WHERE trang_thai = 'cho'
        OR (trang_thai = 'that_bai' AND so_lan_thu < ?)
     ORDER BY id ASC
     LIMIT ?"
);
$queue->execute([$maxRetry, $limit]);
$rows = $queue->fetchAll();

$totalRows = count($rows);
output("▶ Crawl detail: xử lý " . $totalRows . " URL (limit={$limit})");

foreach ($rows as $idx => $row) {
    $currentNum = $idx + 1;
    $queueId = (int) $row['id'];
    $url     = $row['url'];

    // Đánh dấu đang xử lý + tăng số lần thử
    $pdo->prepare(
        "UPDATE crawl_queue
         SET trang_thai='dang_xu_ly', so_lan_thu = so_lan_thu + 1, ngay_cap_nhat = NOW()
         WHERE id = ?"
    )->execute([$queueId]);

    output("  [{$currentNum}/{$totalRows}] → {$url}");

    // ── Fetch ──────────────────────────────────────────────────
    try {
        $html = fetchHtml($url, $timeout);
    } catch (RuntimeException $e) {
        $msg = $e->getMessage();
        output("    ✗ fetch: {$msg}");
        logCrawl($pdo, $url, 'that_bai', $msg);
        $pdo->prepare("UPDATE crawl_queue SET trang_thai='that_bai', ngay_cap_nhat=NOW() WHERE id=?")
            ->execute([$queueId]);
        randomSleep();
        continue;
    }

    // ── Parse ──────────────────────────────────────────────────
    $dom = str_get_html($html);
    if (!$dom) {
        $msg = 'str_get_html trả về false';
        output("    ✗ parse: {$msg}");
        logCrawl($pdo, $url, 'that_bai', $msg);
        $pdo->prepare("UPDATE crawl_queue SET trang_thai='that_bai', ngay_cap_nhat=NOW() WHERE id=?")
            ->execute([$queueId]);
        randomSleep();
        continue;
    }

    $data = parseDetailPage($dom, $url);
    $dom->clear();

    if (empty($data['mst'])) {
        $msg = 'Không tìm thấy MST trong trang';
        output("    ✗ {$msg}");
        logCrawl($pdo, $url, 'that_bai', $msg);
        $pdo->prepare("UPDATE crawl_queue SET trang_thai='that_bai', ngay_cap_nhat=NOW() WHERE id=?")
            ->execute([$queueId]);
        randomSleep();
        continue;
    }

    // ── Upsert lookup tables ───────────────────────────────────
    $tinhId  = $data['ten_tinh']   ? upsertLookup($pdo, 'tinh_thanh', $data['ten_tinh']) : null;
    $phuongId = $data['ten_phuong']
        ? upsertLookup($pdo, 'phuong_xa', $data['ten_phuong'],
                       $tinhId ? ['tinh_thanh_id' => $tinhId] : [])
        : null;
    $loaiId  = $data['loai_hinh']  ? upsertLookup($pdo, 'loai_hinh_doanh_nghiep', $data['loai_hinh']) : null;
    $nganhId = $data['nganh_nghe'] ? upsertLookup($pdo, 'nganh_nghe', $data['nganh_nghe']) : null;

    // ── Upsert doanh_nghiep ────────────────────────────────────
    $sql = "
        INSERT INTO doanh_nghiep
            (mst, ten_cong_ty, ten_quoc_te, ten_viet_tat, nguoi_dai_dien,
             dia_chi, dia_chi_thue, dien_thoai, tinh_trang, ngay_hoat_dong,
             quan_ly_boi, loai_hinh_id, nganh_nghe_id, tinh_thanh_id,
             phuong_xa_id, url_nguon, ngay_cap_nhat)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())
        ON DUPLICATE KEY UPDATE
            ten_cong_ty    = VALUES(ten_cong_ty),
            ten_quoc_te    = VALUES(ten_quoc_te),
            ten_viet_tat   = VALUES(ten_viet_tat),
            nguoi_dai_dien = VALUES(nguoi_dai_dien),
            dia_chi        = VALUES(dia_chi),
            dia_chi_thue   = VALUES(dia_chi_thue),
            dien_thoai     = VALUES(dien_thoai),
            tinh_trang     = VALUES(tinh_trang),
            ngay_hoat_dong = VALUES(ngay_hoat_dong),
            quan_ly_boi    = VALUES(quan_ly_boi),
            loai_hinh_id   = VALUES(loai_hinh_id),
            nganh_nghe_id  = VALUES(nganh_nghe_id),
            tinh_thanh_id  = VALUES(tinh_thanh_id),
            phuong_xa_id   = VALUES(phuong_xa_id),
            url_nguon      = VALUES(url_nguon),
            ngay_cap_nhat  = NOW()
    ";

    try {
        // Kiểm tra xem MST đã tồn tại trong doanh_nghiep chưa
        $checkMst = $pdo->prepare("SELECT COUNT(*) FROM doanh_nghiep WHERE mst = ?");
        $checkMst->execute([$data['mst']]);
        $exists = ((int)$checkMst->fetchColumn() > 0);

        $pdo->prepare($sql)->execute([
            $data['mst'],
            $data['ten_cong_ty'],
            $data['ten_quoc_te']    ?: null,
            $data['ten_viet_tat']   ?: null,
            $data['nguoi_dai_dien'] ?: null,
            $data['dia_chi']        ?: null,
            $data['dia_chi_thue']   ?: null,
            $data['dien_thoai']     ?: null,
            $data['tinh_trang']     ?: null,
            $data['ngay_hoat_dong'] ?: null,
            $data['quan_ly_boi']    ?: null,
            $loaiId,
            $nganhId,
            $tinhId,
            $phuongId,
            $url,
        ]);

        logCrawl($pdo, $url, 'thanh_cong', 'MST: ' . $data['mst'] . ($exists ? ' (Đã tồn tại - cập nhật)' : ' (Thêm mới)'));
        $pdo->prepare("UPDATE crawl_queue SET trang_thai='thanh_cong', ngay_cap_nhat=NOW() WHERE id=?")
            ->execute([$queueId]);

        if ($exists) {
            output("    ~ [{$currentNum}/{$totalRows}] Đã có trong cơ sở dữ liệu (Cập nhật mới): {$data['ten_cong_ty']} [{$data['mst']}]");
        } else {
            output("    ✔ [{$currentNum}/{$totalRows}] Thêm mới doanh nghiệp thành công: {$data['ten_cong_ty']} [{$data['mst']}]");
        }
    } catch (PDOException $e) {
        $msg = 'DB: ' . $e->getMessage();
        output("    ✗ [{$currentNum}/{$totalRows}] {$msg}");
        logCrawl($pdo, $url, 'that_bai', $msg);
        $pdo->prepare("UPDATE crawl_queue SET trang_thai='that_bai', ngay_cap_nhat=NOW() WHERE id=?")
            ->execute([$queueId]);
    }

    randomSleep();
}

output("<span style='color: #28a745; font-weight: bold;'>✔ Hoàn tất crawl_detail. Đã cập nhật xong {$totalRows} doanh nghiệp.</span>", true);
output("<script>
  if (window.parent && window.parent.Swal) {
      window.parent.Swal.fire({
          title: 'Cào chi tiết hoàn tất!',
          text: 'Đã xử lý xong " . $totalRows . " doanh nghiệp từ hàng đợi.',
          icon: 'success',
          confirmButtonText: 'Đồng ý',
          confirmButtonColor: '#3525cd'
      });
  } else {
      alert('Hoàn tất cào chi tiết doanh nghiệp! Đã xử lý xong " . $totalRows . " URL trong hàng đợi.');
  }
</script>", true);

// ──────────────────────────────────────────────────────────────
// Hàm parse trang chi tiết
// ──────────────────────────────────────────────────────────────

/**
 * @return array<string,string>
 */
function parseDetailPage(simple_html_dom $dom, string $url): array
{
    $data = [
        'mst'            => '',
        'ten_cong_ty'    => '',
        'ten_quoc_te'    => '',
        'ten_viet_tat'   => '',
        'nguoi_dai_dien' => '',
        'dia_chi'        => '',
        'dia_chi_thue'   => '',
        'dien_thoai'     => '',
        'tinh_trang'     => '',
        'ngay_hoat_dong' => '',
        'quan_ly_boi'    => '',
        'loai_hinh'      => '',
        'nganh_nghe'     => '',
        'ten_tinh'       => '',
        'ten_phuong'     => '',
    ];

    // Tên công ty từ H1
    $h1 = $dom->find('h1', 0);
    if ($h1) {
        $data['ten_cong_ty'] = trim($h1->plaintext);
    }

    $table = $dom->find('table.table-taxinfo', 0);
    if (!$table) {
        return $data;
    }

    foreach ($table->find('tr') as $tr) {
        $tds = $tr->find('td');
        if (count($tds) < 2) {
            continue;
        }
        $label = trim(strip_tags($tds[0]->innertext));
        $value = trim(strip_tags($tds[1]->innertext));
        // Chuẩn hoá khoảng trắng do HTML entities
        $value = preg_replace('/\s+/', ' ', $value);

        // Thứ tự quan trọng: "Địa chỉ Thuế" trước "Địa chỉ"
        switch (true) {
            case str_contains($label, 'Mã số thuế'):
                $data['mst'] = preg_replace('/\s+/', '', $value);
                break;
            case str_contains($label, 'Địa chỉ Thuế'):
                $data['dia_chi_thue'] = $value;
                break;
            case str_contains($label, 'Địa chỉ'):
                $data['dia_chi'] = $value;
                extractLocation($value, $data);
                break;
            case str_contains($label, 'Tình trạng'):
                $data['tinh_trang'] = $value;
                break;
            case str_contains($label, 'Tên quốc tế'):
                $data['ten_quoc_te'] = $value;
                break;
            case str_contains($label, 'Tên viết tắt'):
                $data['ten_viet_tat'] = $value;
                break;
            case str_contains($label, 'Người đại diện'):
                $data['nguoi_dai_dien'] = $value;
                break;
            case str_contains($label, 'Điện thoại'):
                $data['dien_thoai'] = $value;
                break;
            case str_contains($label, 'Ngày hoạt động'):
                $data['ngay_hoat_dong'] = $value;
                break;
            case str_contains($label, 'Quản lý bởi'):
                $data['quan_ly_boi'] = $value;
                break;
            case str_contains($label, 'Loại hình'):
                $data['loai_hinh'] = $value;
                break;
            case str_contains($label, 'Ngành nghề'):
                $data['nganh_nghe'] = $value;
                break;
        }
    }

    return $data;
}

function extractLocation(string $diaChi, array &$data): void
{
    $parts = array_map('trim', explode(',', $diaChi));
    $n     = count($parts);

    // Bỏ hậu tố 'Việt Nam' nếu có để lấy đúng tỉnh/thành ở vị trí cuối cùng
    if ($n > 0 && mb_strtolower($parts[$n - 1], 'UTF-8') === 'việt nam') {
        array_pop($parts);
        $n = count($parts);
    }

    if ($n >= 1) {
        $last = $parts[$n - 1];
        // Bỏ tiền tố hành chính
        $ten = preg_replace('/^(Tỉnh|Thành phố|TP\.|Thị xã)\s*/u', '', $last);
        $data['ten_tinh'] = trim($ten);
    }
    if ($n >= 3) {
        $phuong = $parts[$n - 3];
        $ten = preg_replace('/^(Phường|Xã|Thị trấn|Thị xã)\s*/u', '', $phuong);
        $data['ten_phuong'] = trim($ten);
    }
}
