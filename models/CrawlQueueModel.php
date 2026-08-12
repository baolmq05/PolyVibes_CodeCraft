<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class CrawlQueueModel extends BaseModel
{
    public function getStats(): array
    {
        return $this->pdo->query(
            "SELECT trang_thai, COUNT(*) AS n FROM crawl_queue GROUP BY trang_thai"
        )->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}
