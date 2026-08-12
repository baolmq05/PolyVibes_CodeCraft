<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class TinhThanhModel extends BaseModel
{
    public function getAll(): array
    {
        return $this->pdo->query(
            "SELECT id, ten, mien_tay FROM tinh_thanh ORDER BY mien_tay DESC, ten ASC"
        )->fetchAll();
    }
}
