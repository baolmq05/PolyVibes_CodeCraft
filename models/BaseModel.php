<?php
declare(strict_types=1);

class BaseModel
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = getPDO();
    }
}
