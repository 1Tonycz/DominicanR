<?php

declare(strict_types=1);

namespace App\Core\Repository;

use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;

abstract class BaseRepository
{
    public function __construct(
        protected readonly Explorer $database
    ) {}

    abstract protected function getTableName(): string;

    public function getAll(): Selection
    {
        return $this->database->table($this->getTableName());
    }

    public function getById(int|string $id): ?ActiveRow
    {
        return $this->getAll()->get($id);
    }

    public function getBy(array|string $cond, ...$params): Selection
    {
        return is_array($cond)
            ? $this->getAll()->where($cond)
            : $this->getAll()->where($cond, ...$params);
    }

    public function insert(array $data): ActiveRow
    {
        $row = $this->getAll()->insert($data);
        if (!$row instanceof ActiveRow) {
            throw new \RuntimeException('Insert did not return ActiveRow.');
        }
        return $row;
    }

    public function update(int|string $id, array $data): void
    {
        $row = $this->getById($id);
        if (!$row) {
            throw new \InvalidArgumentException("Row with ID $id not found.");
        }
        $row->update($data);
    }

    public function delete(int|string $id): void
    {
        $row = $this->getById($id);
        if (!$row) {
            throw new \InvalidArgumentException("Row with ID $id not found.");
        }
        $row->delete();
    }
}
