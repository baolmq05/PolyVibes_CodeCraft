<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/TinhThanhModel.php';
require_once __DIR__ . '/../models/PhuongXaModel.php';
require_once __DIR__ . '/../models/LoaiHinhModel.php';
require_once __DIR__ . '/../models/NganhNgheModel.php';
require_once __DIR__ . '/../models/DoanhNghiepModel.php';

class HomeController
{
    public static function index(): void
    {
        $tinhModel       = new TinhThanhModel();
        $phuongModel     = new PhuongXaModel();
        $loaiModel       = new LoaiHinhModel();
        $nganhModel      = new NganhNgheModel();
        $doanhNghiepModel = new DoanhNghiepModel();

        // ── Filter params ─────────────────────────────────────────────
        $filterTinh   = (int)   ($_GET['tinh']    ?? 0);
        $filterPhuong = (int)   ($_GET['phuong']  ?? 0);
        $filterLoai   = (int)   ($_GET['loai']    ?? 0);
        $filterNganh  = (int)   ($_GET['nganh']   ?? 0);
        $filterSearch = trim($_GET['search']      ?? '');
        $page         = max(1,  (int) ($_GET['page'] ?? 1));
        $perPage      = 20;

        // ── Dropdown data ─────────────────────────────────────────────
        $tinhList   = $tinhModel->getAll();
        $phuongList = $filterTinh > 0 ? $phuongModel->getByTinhThanhId($filterTinh) : [];
        $allPhuongList = $phuongModel->getAll();
        $loaiList   = $loaiModel->getAll();
        $nganhList  = $nganhModel->getAll();

        // ── Query builder / Results ───────────────────────────────────
        $filters = [
            'tinh'   => $filterTinh,
            'phuong' => $filterPhuong,
            'loai'   => $filterLoai,
            'nganh'  => $filterNganh,
            'search' => $filterSearch,
        ];

        $total      = $doanhNghiepModel->getFilteredCount($filters);
        $totalPages = (int) ceil($total / $perPage);
        $offset     = ($page - 1) * $perPage;
        $rows       = $doanhNghiepModel->getFilteredList($filters, $perPage, $offset);

        // ── Dynamic SEO title ─────────────────────────────────────────
        $nganhTen = '';
        $tinhTen  = '';
        if ($filterNganh > 0) {
            foreach ($nganhList as $n) {
                if ((int)$n['id'] == $filterNganh) { $nganhTen = $n['ten']; break; }
            }
        }
        if ($filterTinh > 0) {
            foreach ($tinhList as $t) {
                if ((int)$t['id'] == $filterTinh) { $tinhTen = $t['ten']; break; }
            }
        }

        $pageTitle = self::buildTitle($nganhTen, $tinhTen, $total);

        // Helper built inside controller scope to pass to view
        $qs = function (array $override = []) use ($filterTinh, $filterPhuong, $filterLoai, $filterNganh, $filterSearch): string {
            $base = array_filter([
                'tinh'   => $filterTinh,
                'phuong' => $filterPhuong,
                'loai'   => $filterLoai,
                'nganh'  => $filterNganh,
                'search' => $filterSearch,
                'page'   => 1
            ]);
            return '?' . http_build_query(array_merge($base, $override));
        };

        // Render view
        require_once __DIR__ . '/../views/home.php';
    }

    private static function buildTitle(string $nganhTen, string $tinhTen, int $total): string
    {
        if ($nganhTen == '' && $tinhTen == '') {
            return 'Danh sách doanh nghiệp';
        }
        $top = '';
        if ($total >= 50) $top = 'Top 50 ';
        elseif ($total >= 20) $top = 'Top 20 ';
        elseif ($total >= 10) $top = 'Top 10 ';

        $parts = [];
        if ($nganhTen) $parts[] = mb_strtolower($nganhTen, 'UTF-8');
        if ($tinhTen)  $parts[] = "tại {$tinhTen}";

        return ucfirst("{$top}doanh nghiệp " . implode(' ', $parts));
    }
}
