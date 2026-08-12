<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class DoanhNghiepModel extends BaseModel
{
    public function getFilteredCount(array $filters): int
    {
        $where = ["d.dia_chi IS NOT NULL AND d.dia_chi != ''"];
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
        if (!empty($filters['search'])) {
            $where[] = '(d.ten_cong_ty LIKE ? OR d.mst LIKE ? OR d.nguoi_dai_dien LIKE ?)';
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }

        $whereSQL = implode(' AND ', $where);
        $s = $this->pdo->prepare("SELECT COUNT(*) FROM doanh_nghiep d WHERE {$whereSQL}");
        $s->execute($params);
        return (int) $s->fetchColumn();
    }

    public function getFilteredList(array $filters, int $limit, int $offset): array
    {
        $where = ["d.dia_chi IS NOT NULL AND d.dia_chi != ''"];
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
        if (!empty($filters['search'])) {
            $where[] = '(d.ten_cong_ty LIKE ? OR d.mst LIKE ? OR d.nguoi_dai_dien LIKE ?)';
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
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
        return (int) $this->pdo->query("SELECT COUNT(*) FROM doanh_nghiep WHERE dia_chi IS NOT NULL AND dia_chi != ''")->fetchColumn();
    }

    public function insert(array $data): bool
    {
        $sql = "INSERT INTO doanh_nghiep 
                (mst, ten_cong_ty, ten_quoc_te, ten_viet_tat, nguoi_dai_dien, 
                 dia_chi, dia_chi_thue, dien_thoai, tinh_trang, ngay_hoat_dong, 
                 quan_ly_boi, loai_hinh_id, nganh_nghe_id, tinh_thanh_id, 
                 phuong_xa_id, url_nguon, ngay_cap_nhat) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $s = $this->pdo->prepare($sql);
        return $s->execute([
            $data['mst'],
            $data['ten_cong_ty'],
            $data['ten_quoc_te'] ?: null,
            $data['ten_viet_tat'] ?: null,
            $data['nguoi_dai_dien'] ?: null,
            $data['dia_chi'] ?: null,
            $data['dia_chi_thue'] ?: null,
            $data['dien_thoai'] ?: null,
            $data['tinh_trang'] ?: null,
            $data['ngay_hoat_dong'] ?: null,
            $data['quan_ly_boi'] ?: null,
            $data['loai_hinh_id'] ?: null,
            $data['nganh_nghe_id'] ?: null,
            $data['tinh_thanh_id'] ?: null,
            $data['phuong_xa_id'] ?: null,
            $data['url_nguon'] ?: null
        ]);
    }

    public function update(string $mst, array $data): bool
    {
        $sql = "UPDATE doanh_nghiep SET 
                ten_cong_ty = ?,
                ten_quoc_te = ?,
                ten_viet_tat = ?,
                nguoi_dai_dien = ?,
                dia_chi = ?,
                dia_chi_thue = ?,
                dien_thoai = ?,
                tinh_trang = ?,
                ngay_hoat_dong = ?,
                quan_ly_boi = ?,
                loai_hinh_id = ?,
                nganh_nghe_id = ?,
                tinh_thanh_id = ?,
                phuong_xa_id = ?,
                url_nguon = ?,
                ngay_cap_nhat = NOW()
                WHERE mst = ?";
        $s = $this->pdo->prepare($sql);
        return $s->execute([
            $data['ten_cong_ty'],
            $data['ten_quoc_te'] ?: null,
            $data['ten_viet_tat'] ?: null,
            $data['nguoi_dai_dien'] ?: null,
            $data['dia_chi'] ?: null,
            $data['dia_chi_thue'] ?: null,
            $data['dien_thoai'] ?: null,
            $data['tinh_trang'] ?: null,
            $data['ngay_hoat_dong'] ?: null,
            $data['quan_ly_boi'] ?: null,
            $data['loai_hinh_id'] ?: null,
            $data['nganh_nghe_id'] ?: null,
            $data['tinh_thanh_id'] ?: null,
            $data['phuong_xa_id'] ?: null,
            $data['url_nguon'] ?: null,
            $mst
        ]);
    }

    public function delete(string $mst): bool
    {
        $s = $this->pdo->prepare("DELETE FROM doanh_nghiep WHERE mst = ?");
        return $s->execute([$mst]);
    }
}
