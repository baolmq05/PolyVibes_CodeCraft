<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/CrawlQueueModel.php';
require_once __DIR__ . '/../models/DoanhNghiepModel.php';
require_once __DIR__ . '/../models/NganhNgheModel.php';
require_once __DIR__ . '/../models/CrawlLogModel.php';


class AdminController
{
    public static function crawl(): void
    {
        $queueModel = new CrawlQueueModel();
        $doanhNghiepModel = new DoanhNghiepModel();

        $statsRaw = $queueModel->getStats();
        $totalDn = $doanhNghiepModel->getTotalCount();
        $queueItems = $queueModel->getFilteredList([], 5, 0);

        $tinhOptions = [
            '' => '-- Nhập thủ công bên dưới --',
            'can-tho-96' => 'Cần Thơ',
            'long-an-29' => 'Long An',
            'tien-giang-177' => 'Tiền Giang',
            'ben-tre-185' => 'Bến Tre',
            'tra-vinh-41' => 'Trà Vinh',
            'vinh-long-193' => 'Vĩnh Long',
            'dong-thap-63' => 'Đồng Tháp',
            'an-giang-93' => 'An Giang',
            'kien-giang-80' => 'Kiên Giang',
            'hau-giang-190' => 'Hậu Giang',
            'soc-trang-949' => 'Sóc Trăng',
            'bac-lieu-197' => 'Bạc Liêu',
            'ca-mau-108' => 'Cà Mau',
            'ho-chi-minh' => 'TP. Hồ Chí Minh',
        ];

        require_once __DIR__ . '/../views/admin_crawl.php';
    }

    public static function danhMuc(): void
    {
        $nganhModel = new NganhNgheModel();
        $msg = '';

        // ── Xử lý POST (Xoá) ──────────────────────────────────────────
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            if ($action == 'delete') {
                $id = (int) ($_POST['id'] ?? 0);
                if ($id > 0) {
                    $nganhModel->delete($id);
                    $msg = 'Đã xoá ngành nghề thành công.';
                    header('Location: danh-muc.php?msg=' . urlencode($msg) . '&type=warning');
                    exit;
                }
            }
        }

        // Hiện thông báo sau redirect
        if (!empty($_GET['msg'])) {
            $allowedTypes = ['success', 'warning', 'danger', 'info'];
            $type = in_array($_GET['type'] ?? '', $allowedTypes, true) ? $_GET['type'] : 'info';
            $msg = '<div class="alert alert-' . $type . '">' . e($_GET['msg']) . '</div>';
        }

        $nganhs = $nganhModel->getAllWithCount();
        require_once __DIR__ . '/../views/admin_danh_muc.php';
    }

    public static function danhMucEdit(): void
    {
        $nganhModel = new NganhNgheModel();
        $uploadDir = __DIR__ . '/../uploads/nganh-nghe/';
        $uploadUrl = '../uploads/nganh-nghe/';
        $msg = '';
        $id = (int) ($_GET['id'] ?? 0);

        $editing = null;
        if ($id > 0) {
            $editing = $nganhModel->getById($id);
        }

        // ── Xử lý POST (Lưu / Cập nhật) ───────────────────────────────
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            if ($action == 'save') {
                $ten = trim($_POST['ten'] ?? '');
                $slug = trim($_POST['slug'] ?? '') ?: slugify($ten);
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
                                $msg = 'Cập nhật ngành nghề thành công.';
                            } else {
                                $nganhModel->insert($ten, $slug, $moTa, $hinhAnh);
                                $msg = 'Thêm mới ngành nghề thành công.';
                            }
                            header('Location: danh-muc.php?msg=' . urlencode($msg) . '&type=success');
                            exit;
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
        }

        require_once __DIR__ . '/../views/admin_danh_muc_edit.php';
    }

    private static function processUpload(array $file, string $dir): ?string
    {
        $allowedMime = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

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

    public static function dashboard(): void
    {
        $queueModel = new CrawlQueueModel();
        $doanhNghiepModel = new DoanhNghiepModel();

        $stats = $queueModel->getStats();
        $totalDn = $doanhNghiepModel->getTotalCount();

        require_once __DIR__ . '/../views/admin_dashboard.php';
    }

    public static function queue(): void
    {
        $queueModel = new CrawlQueueModel();

        $filterStatus = $_GET['status'] ?? '';
        $filterSearch = trim($_GET['search'] ?? '');
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 20;

        $filters = [
            'status' => $filterStatus,
            'search' => $filterSearch,
        ];

        $total = $queueModel->getFilteredCount($filters);
        $totalPages = (int) ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        $rows = $queueModel->getFilteredList($filters, $perPage, $offset);

        $qs = function (array $override = []) use ($filterStatus, $filterSearch): string {
            $base = array_filter([
                'status' => $filterStatus,
                'search' => $filterSearch,
                'page' => 1
            ]);
            return '?' . http_build_query(array_merge($base, $override));
        };

        require_once __DIR__ . '/../views/admin_queue.php';
    }

    public static function logs(): void
    {
        $logModel = new CrawlLogModel();

        $filterResult = $_GET['result'] ?? '';
        $filterSearch = trim($_GET['search'] ?? '');
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 20;

        $filters = [
            'result' => $filterResult,
            'search' => $filterSearch,
        ];

        $total = $logModel->getFilteredCount($filters);
        $totalPages = (int) ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        $rows = $logModel->getFilteredList($filters, $perPage, $offset);

        $qs = function (array $override = []) use ($filterResult, $filterSearch): string {
            $base = array_filter([
                'result' => $filterResult,
                'search' => $filterSearch,
                'page' => 1
            ]);
            return '?' . http_build_query(array_merge($base, $override));
        };

        require_once __DIR__ . '/../views/admin_logs.php';
    }

    public static function doanhNghiep(): void
    {
        require_once __DIR__ . '/../models/TinhThanhModel.php';
        require_once __DIR__ . '/../models/PhuongXaModel.php';
        require_once __DIR__ . '/../models/LoaiHinhModel.php';
        require_once __DIR__ . '/../models/NganhNgheModel.php';

        $tinhModel = new TinhThanhModel();
        $phuongModel = new PhuongXaModel();
        $loaiModel = new LoaiHinhModel();
        $nganhModel = new NganhNgheModel();
        $doanhNghiepModel = new DoanhNghiepModel();

        // ── Xử lý POST (Xóa doanh nghiệp) ─────────────────────────────
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            if ($action === 'delete') {
                $mst = trim($_POST['mst'] ?? '');
                if ($mst !== '') {
                    $doanhNghiepModel->delete($mst);
                    header('Location: doanh-nghiep.php?msg=' . urlencode('Đã xóa doanh nghiệp thành công.') . '&type=warning');
                    exit;
                }
            }
        }

        // Hiện thông báo sau redirect
        $msg = '';
        if (!empty($_GET['msg'])) {
            $allowedTypes = ['success', 'warning', 'danger', 'info'];
            $type = in_array($_GET['type'] ?? '', $allowedTypes, true) ? $_GET['type'] : 'info';
            $msg = '<div class="alert alert-' . $type . '">' . e($_GET['msg']) . '</div>';
        }

        $filterTinh = (int) ($_GET['tinh'] ?? 0);
        $filterPhuong = (int) ($_GET['phuong'] ?? 0);
        $filterLoai = (int) ($_GET['loai'] ?? 0);
        $filterNganh = (int) ($_GET['nganh'] ?? 0);
        $filterSearch = trim($_GET['search'] ?? '');
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 20;

        $tinhList = $tinhModel->getAll();
        $phuongList = $filterTinh > 0 ? $phuongModel->getByTinhThanhId($filterTinh) : [];
        $allPhuongList = $phuongModel->getAll();
        $loaiList = $loaiModel->getAll();
        $nganhList = $nganhModel->getAll();

        $filters = [
            'tinh' => $filterTinh,
            'phuong' => $filterPhuong,
            'loai' => $filterLoai,
            'nganh' => $filterNganh,
            'search' => $filterSearch,
        ];

        $total = $doanhNghiepModel->getFilteredCount($filters);
        $totalPages = (int) ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        $rows = $doanhNghiepModel->getFilteredList($filters, $perPage, $offset);

        $qs = function (array $override = []) use ($filterTinh, $filterPhuong, $filterLoai, $filterNganh, $filterSearch): string {
            $base = array_filter([
                'tinh' => $filterTinh,
                'phuong' => $filterPhuong,
                'loai' => $filterLoai,
                'nganh' => $filterNganh,
                'search' => $filterSearch,
                'page' => 1
            ]);
            return '?' . http_build_query(array_merge($base, $override));
        };

        require_once __DIR__ . '/../views/admin_doanh_nghiep.php';
    }

    public static function doanhNghiepEdit(): void
    {
        require_once __DIR__ . '/../models/TinhThanhModel.php';
        require_once __DIR__ . '/../models/PhuongXaModel.php';
        require_once __DIR__ . '/../models/LoaiHinhModel.php';
        require_once __DIR__ . '/../models/NganhNgheModel.php';
        require_once __DIR__ . '/../models/DoanhNghiepModel.php';

        $tinhModel = new TinhThanhModel();
        $phuongModel = new PhuongXaModel();
        $loaiModel = new LoaiHinhModel();
        $nganhModel = new NganhNgheModel();
        $doanhNghiepModel = new DoanhNghiepModel();

        $msg = '';
        $mst = trim($_GET['mst'] ?? '');
        $editing = null;
        if ($mst !== '') {
            $editing = $doanhNghiepModel->getByMst($mst);
        }

        // ── Xử lý POST (Thêm mới / Cập nhật) ───────────────────────────
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            if ($action === 'save') {
                $isEdit = !empty($_POST['is_edit']);
                $mstPost = trim($_POST['mst'] ?? '');

                $data = [
                    'mst' => $mstPost,
                    'ten_cong_ty' => trim($_POST['ten_cong_ty'] ?? ''),
                    'ten_quoc_te' => trim($_POST['ten_quoc_te'] ?? ''),
                    'ten_viet_tat' => trim($_POST['ten_viet_tat'] ?? ''),
                    'nguoi_dai_dien' => trim($_POST['nguoi_dai_dien'] ?? ''),
                    'dia_chi' => trim($_POST['dia_chi'] ?? ''),
                    'dia_chi_thue' => trim($_POST['dia_chi_thue'] ?? ''),
                    'dien_thoai' => trim($_POST['dien_thoai'] ?? ''),
                    'tinh_trang' => trim($_POST['tinh_trang'] ?? ''),
                    'ngay_hoat_dong' => trim($_POST['ngay_hoat_dong'] ?? '') ?: null,
                    'quan_ly_boi' => trim($_POST['quan_ly_boi'] ?? ''),
                    'tinh_thanh_id' => (int) ($_POST['tinh_thanh_id'] ?? 0) ?: null,
                    'phuong_xa_id' => (int) ($_POST['phuong_xa_id'] ?? 0) ?: null,
                    'loai_hinh_id' => (int) ($_POST['loai_hinh_id'] ?? 0) ?: null,
                    'nganh_nghe_id' => (int) ($_POST['nganh_nghe_id'] ?? 0) ?: null,
                    'url_nguon' => trim($_POST['url_nguon'] ?? '')
                ];

                if ($data['mst'] === '' || $data['ten_cong_ty'] === '') {
                    $msg = '<div class="alert alert-danger">Vui lòng nhập đầy đủ Mã số thuế và Tên công ty.</div>';
                } else {
                    try {
                        if ($isEdit) {
                            $doanhNghiepModel->update($mst, $data);
                            $msg = 'Cập nhật doanh nghiệp thành công.';
                        } else {
                            if ($doanhNghiepModel->getByMst($data['mst']) !== null) {
                                throw new Exception("Mã số thuế này đã tồn tại.");
                            }
                            $doanhNghiepModel->insert($data);
                            $msg = 'Thêm mới doanh nghiệp thành công.';
                        }
                        header('Location: doanh-nghiep.php?msg=' . urlencode($msg) . '&type=success');
                        exit;
                    } catch (Exception $e) {
                        $msg = '<div class="alert alert-danger">' . e($e->getMessage()) . '</div>';
                    }
                }
            }
        }

        $tinhList = $tinhModel->getAll();
        $loaiList = $loaiModel->getAll();
        $nganhList = $nganhModel->getAll();
        $allPhuongList = $phuongModel->getAll();

        require_once __DIR__ . '/../views/admin_doanh_nghiep_edit.php';
    }
}
