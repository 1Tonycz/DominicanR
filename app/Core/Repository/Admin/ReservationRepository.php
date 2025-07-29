<?php

declare(strict_types=1);

namespace App\Core\Repository\Admin;

use App\Core\Repository\BaseRepository;
use Nette\Database\Table\Selection;

final class ReservationRepository extends BaseRepository
{
    protected function getTableName(): string
    {
        return 'reservations';
    }

    public function findByApartment(int $apartmentId): Selection
    {
        return $this->getAll()
            ->where('apartment_id', $apartmentId);
    }

    public function findOverlapping(int $apartmentId, \DateTimeInterface $from, \DateTimeInterface $to)
    {
        return $this->getBy([
            'apartment_id' => $apartmentId,
            'NOT (to_date < ? OR from_date > ?)' => [$from, $to]
        ]);
    }

    public function findUpcoming(\DateTimeInterface $after): Selection
    {
        return $this->getAll()
            ->where('date_from >= ?', $after)
            ->order('date_from ASC');
    }

    public function findBetween(\DateTimeInterface $start, \DateTimeInterface $end): Selection
    {
        return $this->getAll()
            ->where('date_from >= ? AND date_to <= ?', $start, $end)
            ->order('date_from ASC');
    }

    public function hasConflict(int $apartmentId, string $from, string $to, ?int $ignoreId = null): bool
    {
        $selection = $this->getAll()
            ->where('apartment_id = ?', $apartmentId)
            ->where('NOT (to_date <= ? OR from_date >= ?)', $from, $to);

        if ($ignoreId !== null) {
            $selection->where('id != ?', $ignoreId);
        }

        return $selection->count('*') > 0;
    }

}
