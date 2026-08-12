<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/CrawlQueueModel.php';
require_once __DIR__ . '/../models/DoanhNghiepModel.php';
require_once __DIR__ . '/../models/NganhNgheModel.php';

class AdminController
{
    public static function crawl(): void
    {
        $queueModel = new CrawlQueueModel();
        $doanhNghiepModel = new DoanhNghiepModel();

        $statsRaw = $queueModel->getStats();
        $totalDn  = $doanhNghiepModel->getTotalCount();

        $tinhOptions = [
            ''              => '-- Nhập thủ công bên dưới --',
            'can-tho-92'    => 'Cần Thơ',
            'long-an-80'    => 'Long An',
            'tien-giang-82' => 'Tiền Giang',
            'ben-tre-83'    => 'Bến Tre',
            'tra-vinh-84'   => 'Trà Vinh',
            'vinh-long-86'  => 'Vĩnh Long',
            'dong-thap-63'  => 'Đồng Tháp',
            'an-giang-89'   => 'An Giang',
            'kien-giang-91' => 'Kiên Giang',
            'hau-giang-93'  => 'Hậu Giang',
            'soc-trang-94'  => 'Sóc Trăng',
            'bac-lieu-95'   => 'Bạc Liêu',
            'ca-mau-96'     => 'Cà Mau',
            'ho-chi-minh-79'=> 'TP. Hồ Chí Minh',
            'ha-noi-01'     => 'Hà Nội',
        ];

        require_once __DIR__ . '/../views/admin_crawl.php';
    }

    public static function danhMuc(): void
    {
        $nganhModel = new NganhNgheModel();
        $uploadDir  = __DIR__ . '/../uploads/nganh-nghe/';
        $uploadUrl  = '../uploads/nganh-nghe/';
        $msg        = '';

        // ── Xử lý POST ────────────────────────────────────────────────
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action == 'save') {
                $id   = (int) ($_POST['id'] ?? 0);
                $ten  = trim($_POST['ten']   ?? '');
                $slug = trim($_POST['slug']  ?? '') ?: slugify($ten);
                $moTa = trim($_POST['mo_ta'] ?? '');

                if ($ten == '') {
                    $msg = '<div class="alert alert-danger">Tên ngành nghề không được để trống.</div>';
                } else {
                    $hinhAnh = null;
                    if (!empty($_FILES['hinh_anh']['tmp_name'])) {
                        $hinhAnh = self::processUpload($_FILES['hinh_anh'], $uploadDir);
                        if ($hinhAnh == null) {
                            $msg = '<div class="alert alert-danger">File ảnh không hợp lệ (cho phép: jpg, png, webp, gif; tối đa 2 MB).</div>';
                        }
                    }

                    if ($msg == '') {
                        try {
                            if ($id > 0) {
                                $nganhModel->update($id, $ten, $slug, $moTa, $hinhAnh);
                                $msg = '<div class="alert alert-success">Đã cập nhật ngành nghề.</div>';
                            } else {
                                $nganhModel->insert($ten, $slug, $moTa, $hinhAnh);
                                $msg = '<div class="alert alert-success">Đã thêm ngành nghề mới.</div>';
                            }
                        } catch (PDOException $e) {
                            if ($e->getCode() === '23000') {
                                $msg = '<div class="alert alert-danger">Slug đã tồn tại, hãy dùng slug khác.</div>';
                            } else {
                                $msg = '<div class="alert alert-danger">Lỗi DB: ' . e($e->getMessage()) . '</div>';
                            }
                        }
                    }
                }
            }

            if ($action == 'delete') {
                $id = (int) ($_POST['id'] ?? 0);
                if ($id > 0) {
                    $nganhModel->delete($id);
                    $msg = '<div class="alert alert-warning">Đã xoá ngành nghề.</div>';
                }
            }

            // Redirect PRG pattern
            $type = str_contains($msg, 'alert-success') ? 'success'
                  : (str_contains($msg, 'alert-danger')  ? 'danger'  : 'warning');
            header('Location: danh-muc.php?msg=' . urlencode(strip_tags($msg)) . '&type=' . $type);
            exit;
        }

        // Hiện thông báo sau redirect
        if (!empty($_GET['msg'])) {
            $allowedTypes = ['success', 'warning', 'danger', 'info'];
            $type  = in_array($_GET['type'] ?? '', $allowedTypes, true) ? $_GET['type'] : 'info';
            $msg   = '<div class="alert alert-' . $type . '">' . e($_GET['msg']) . '</div>';
        }

        // ── Load dữ liệu ──────────────────────────────────────────────
        $nganhs  = $nganhModel->getAllWithCount();
        $editing = null;
        if (isset($_GET['edit'])) {
            $editing = $nganhModel->getById((int) $_GET['edit']);
        }

        require_once __DIR__ . '/../views/admin_danh_muc.php';
    }

    private static function processUpload(array $file, string $dir): ?string
    {
        $allowedMime = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $allowedExt  = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (!in_array($file['type'], $allowedMime, true)) {
            return null;
        }
        if ($file['size'] > 2 * 1024 * 1024) {
            return null;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt, true)) {
            return null;
        }

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $name = 'nn_' . bin2hex(random_bytes(8)) . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], $dir . $name)) {
            return null;
        }
        return $name;
    }
}
