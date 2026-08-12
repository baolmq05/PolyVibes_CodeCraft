<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class LoaiHinhModel extends BaseModel
{
    public function getAll(): array
    {
        return $this->pdo->query(
            "SELECT id, ten FROM loai_hinh_doanh_nghiep ORDER BY ten ASC"
        )->fetchAll();
    }
}
