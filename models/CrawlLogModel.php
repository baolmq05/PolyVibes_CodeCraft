<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class CrawlLogModel extends BaseModel
{
    public function getFilteredCount(array $filters): int
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['result'])) {
            $where[] = 'ket_qua = ?';
            $params[] = $filters['result'];
        }
        if (!empty($filters['search'])) {
            $where[] = 'url LIKE ?';
            $params[] = '%' . $filters['search'] . '%';
        }

        $whereSQL = implode(' AND ', $where);
        $s = $this->pdo->prepare("SELECT COUNT(*) FROM crawl_log WHERE {$whereSQL}");
        $s->execute($params);
        return (int) $s->fetchColumn();
    }

    public function getFilteredList(array $filters, int $limit, int $offset): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['result'])) {
            $where[] = 'ket_qua = ?';
            $params[] = $filters['result'];
        }
        if (!empty($filters['search'])) {
            $where[] = 'url LIKE ?';
            $params[] = '%' . $filters['search'] . '%';
        }

        $whereSQL = implode(' AND ', $where);
        $s = $this->pdo->prepare("
            SELECT * FROM crawl_log
            WHERE {$whereSQL}
            ORDER BY id DESC
            LIMIT ? OFFSET ?
        ");
        $s->execute(array_merge($params, [$limit, $offset]));
        return $s->fetchAll();
    }
}
