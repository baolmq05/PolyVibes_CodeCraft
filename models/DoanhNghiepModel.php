<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class DoanhNghiepModel extends BaseModel
{
    public function getFilteredCount(array $filters): int
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['tinh'])) {
            $where[] = 'd.tinh_thanh_id = ?';
            $params[] = (int) $filters['tinh'];
        }
        if (!empty($filters['phuong'])) {
            $where[] = 'd.phuong_xa_id = ?';
            $params[] = (int) $filters['phuong'];
        }
        if (!empty($filters['loai'])) {
            $where[] = 'd.loai_hinh_id = ?';
            $params[] = (int) $filters['loai'];
        }
        if (!empty($filters['nganh'])) {
            $where[] = 'd.nganh_nghe_id = ?';
            $params[] = (int) $filters['nganh'];
        }

        $whereSQL = implode(' AND ', $where);
        $s = $this->pdo->prepare("SELECT COUNT(*) FROM doanh_nghiep d WHERE {$whereSQL}");
        $s->execute($params);
        return (int) $s->fetchColumn();
    }

    public function getFilteredList(array $filters, int $limit, int $offset): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['tinh'])) {
            $where[] = 'd.tinh_thanh_id = ?';
            $params[] = (int) $filters['tinh'];
        }
        if (!empty($filters['phuong'])) {
            $where[] = 'd.phuong_xa_id = ?';
            $params[] = (int) $filters['phuong'];
        }
        if (!empty($filters['loai'])) {
            $where[] = 'd.loai_hinh_id = ?';
            $params[] = (int) $filters['loai'];
        }
        if (!empty($filters['nganh'])) {
            $where[] = 'd.nganh_nghe_id = ?';
            $params[] = (int) $filters['nganh'];
        }

        $whereSQL = implode(' AND ', $where);
        $s = $this->pdo->prepare("
            SELECT d.mst, d.ten_cong_ty, d.nguoi_dai_dien, d.dia_chi, d.tinh_trang,
                   n.ten  AS nganh_ten,
                   t.ten  AS tinh_ten,
                   l.ten  AS loai_ten
            FROM doanh_nghiep d
            LEFT JOIN nganh_nghe              n ON n.id = d.nganh_nghe_id
            LEFT JOIN tinh_thanh              t ON t.id = d.tinh_thanh_id
            LEFT JOIN loai_hinh_doanh_nghiep  l ON l.id = d.loai_hinh_id
            WHERE {$whereSQL}
            ORDER BY d.ten_cong_ty ASC
            LIMIT ? OFFSET ?
        ");
        
        $s->execute(array_merge($params, [$limit, $offset]));
        return $s->fetchAll();
    }

    public function getByMst(string $mst): ?array
    {
        $s = $this->pdo->prepare("
            SELECT d.*,
                   n.ten      AS nganh_ten,
                   n.slug     AS nganh_slug,
                   n.hinh_anh AS nganh_hinh_anh,
                   t.ten      AS tinh_ten,
                   p.ten      AS phuong_ten,
                   l.ten      AS loai_ten
            FROM doanh_nghiep d
            LEFT JOIN nganh_nghe              n ON n.id = d.nganh_nghe_id
            LEFT JOIN tinh_thanh              t ON t.id = d.tinh_thanh_id
            LEFT JOIN phuong_xa               p ON p.id = d.phuong_xa_id
            LEFT JOIN loai_hinh_doanh_nghiep  l ON l.id = d.loai_hinh_id
            WHERE d.mst = ?
            LIMIT 1
        ");
        $s->execute([$mst]);
        $res = $s->fetch();
        return $res ?: null;
    }

    public function getTotalCount(): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM doanh_nghiep")->fetchColumn();
    }
}
