<?php

declare(strict_types=1);

namespace App\Core\Repository\Admin;

use App\Core\Repository\BaseRepository;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;


final class ApartmentRepository extends BaseRepository
{
    protected function getTableName(): string
    {
        return 'apartments';
    }

    public function findByName(string $name): ?ActiveRow
    {
        return $this->getAll()
            ->where('name', $name)
            ->fetch();
    }

    public function getAvailable(): Selection
    {
        return $this->getAll()
            ->where('active', true);
    }

    public function getWithCapacity(int $minCapacity): Selection
    {
        return $this->getAll()
            ->where('capacity >= ?', $minCapacity);
    }

    public function updateMainImage(int $apartmentId, string $imageName): void
    {
        $this->update($apartmentId, [
            'main_image_name' => $imageName,
        ]);
    }

}
