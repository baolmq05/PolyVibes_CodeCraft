<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class NganhNgheModel extends BaseModel
{
    public function getAll(): array
    {
        return $this->pdo->query(
            "SELECT id, ten, hinh_anh FROM nganh_nghe ORDER BY ten ASC"
        )->fetchAll();
    }

    public function getAllWithCount(): array
    {
        return $this->pdo->query("
            SELECT n.id, n.ten, n.slug, n.hinh_anh, n.mo_ta, n.ngay_tao,
                   COUNT(d.id) AS so_dn
            FROM nganh_nghe n
            LEFT JOIN doanh_nghiep d ON d.nganh_nghe_id = n.id
            GROUP BY n.id
            ORDER BY so_dn DESC, n.ten ASC
        ")->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $s = $this->pdo->prepare('SELECT * FROM nganh_nghe WHERE id = ?');
        $s->execute([$id]);
        $res = $s->fetch();
        return $res ?: null;
    }

    public function insert(string $ten, string $slug, string $moTa, ?string $hinhAnh): bool
    {
        $s = $this->pdo->prepare(
            'INSERT INTO nganh_nghe (ten, slug, mo_ta, hinh_anh) VALUES (?,?,?,?)'
        );
        return $s->execute([$ten, $slug, $moTa, $hinhAnh]);
    }

    public function update(int $id, string $ten, string $slug, string $moTa, ?string $hinhAnh = null): bool
    {
        if ($hinhAnh != null) {
            $s = $this->pdo->prepare(
                'UPDATE nganh_nghe SET ten=?, slug=?, mo_ta=?, hinh_anh=? WHERE id=?'
            );
            return $s->execute([$ten, $slug, $moTa, $hinhAnh, $id]);
        } else {
            $s = $this->pdo->prepare(
                'UPDATE nganh_nghe SET ten=?, slug=?, mo_ta=? WHERE id=?'
            );
            return $s->execute([$ten, $slug, $moTa, $id]);
        }
    }

    public function delete(int $id): bool
    {
        $s = $this->pdo->prepare('DELETE FROM nganh_nghe WHERE id=?');
        return $s->execute([$id]);
    }
}
