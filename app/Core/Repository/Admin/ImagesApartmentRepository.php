<?php

declare(strict_types=1);

namespace App\Core\Repository\Admin;

use App\Core\Repository\BaseRepository;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;

final class ImagesApartmentRepository extends BaseRepository
{
    protected function getTableName(): string
    {
        return 'apartment_images';
    }

    /**
     * Vloží fotku k apartmánu.
     */
    public function insertImage(int $apartmentId, string $filename, int $position = 0, bool $isMain = false): void
    {
        $this->insert([
            'apartment_id' => $apartmentId,
            'filename' => $filename,
            'position' => $position,
            'is_main' => $isMain ? 1 : 0,
        ]);
    }

    public function updatePosition(int $imageId, int $position): void
    {
        $this->update($imageId, ['position' => $position]);
    }

    /**
     * Vrátí všechny fotky daného apartmánu.
     */
    public function getImagesByApartment(int $apartmentId): Selection
    {
        return $this->getBy('apartment_id = ?', $apartmentId)->order('position ASC');
    }

    /**
     * Vrátí hlavní obrázek apartmánu.
     */
    public function getMainImage(int $apartmentId): ?ActiveRow
    {
        return $this->getBy([
            'apartment_id' => $apartmentId,
            'is_main' => true
        ])->limit(1)->fetch();
    }

    /**
     * Smaže všechny fotky daného apartmánu.
     */
    public function deleteImagesByApartment(int $apartmentId): void
    {
        $this->getBy('apartment_id = ?', $apartmentId)->delete();
    }

    /**
     * Smaže konkrétní obrázek.
     */
    public function deleteImageById(int $imageId): void
    {
        $this->delete($imageId);
    }

    /**
     * Nastaví obrázek jako hlavní.
     */
    public function setAsMain(int $imageId, int $apartmentId): void
    {
        // nejdříve všechny zruší jako hlavní
        $this->getBy('apartment_id = ?', $apartmentId)
            ->update(['is_main' => 0]);

        // potom nastaví vybraný
        $this->update($imageId, ['is_main' => 1]);
    }

    public function getMainPhotoByApartmentId(int $apartmentId): ?ActiveRow
    {
        return $this->getBy([
            'apartment_id' => $apartmentId,
            'is_main' => true,
        ])->limit(1)->fetch();
    }


}
