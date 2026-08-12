<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/DoanhNghiepModel.php';

class DetailController
{
    public static function show(): void
    {
        $doanhNghiepModel = new DoanhNghiepModel();

        // ── Validate MST ──────────────────────────────────────────────
        $mst = preg_replace('/[^0-9-]/', '', $_GET['mst'] ?? '');
        if ($mst == '') {
            header('Location: index.php');
            exit;
        }

        // ── Truy vấn doanh nghiệp ─────────────────────────────────────
        $dn = $doanhNghiepModel->getByMst($mst);

        if (!$dn) {
            http_response_code(404);
            require_once __DIR__ . '/../includes/helpers.php';
            ?>
            <!DOCTYPE html>
            <html lang="vi"><head><meta charset="UTF-8"><title>Không tìm thấy</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
            </head><body class="container py-5">
            <h2>Không tìm thấy doanh nghiệp</h2>
            <p>MST <code><?= e($mst) ?></code> chưa có trong hệ thống.</p>
            <a href="index.php" class="btn btn-primary">← Quay lại danh sách</a>
            </body></html>
            <?php
            exit;
        }

        $uploadUrl = 'uploads/nganh-nghe/';
        $pageTitle = $dn['ten_cong_ty'];

        // Render view
        require_once __DIR__ . '/../views/detail.php';
    }
}
