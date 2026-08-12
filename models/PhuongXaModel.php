<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class PhuongXaModel extends BaseModel
{
    public function getByTinhThanhId(int $tinhThanhId): array
    {
        $s = $this->pdo->prepare(
            "SELECT id, ten FROM phuong_xa WHERE tinh_thanh_id = ? ORDER BY ten ASC"
        );
        $s->execute([$tinhThanhId]);
        return $s->fetchAll();
    }
}
