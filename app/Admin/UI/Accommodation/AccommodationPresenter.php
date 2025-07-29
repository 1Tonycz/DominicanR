<?php

declare(strict_types=1);

namespace App\Admin\UI\Accommodation;

use App\Admin\UI\BasePresenter;
use App\Core\Repository\Admin\ApartmentRepository;
use App\Core\Repository\Admin\ReservationRepository;
use App\Admin\Forms\ApartmentFormFactory;
use App\Admin\Forms\ReservationFormFactory;
use Nette\Application\UI\Form;
use App\Core\Repository\Admin\ImagesApartmentRepository;
use Nette\Database\Table\ActiveRow;

final class AccommodationPresenter extends BasePresenter
{
    public function __construct(
        private ApartmentRepository $apartmentRepository,
        private ReservationRepository $reservationRepository,
        private ApartmentFormFactory $apartmentFormFactory,
        private ReservationFormFactory $reservationFormFactory,
        private ImagesApartmentRepository $imagesApartmentRepository,
    ) {}

    // === Přidání apartmánu ===
    public function actionAdd(): void
    {
        // formulář se připraví ve createComponentApartmentForm()
    }

    public function renderAdd(): void
    {
        $this->template->heading = 'Přidat apartmán';
    }

    // === Editace apartmánu ===
    public function actionEdit(int $id): void
    {
        $apartment = $this->apartmentRepository->getById($id);
        if (!$apartment) {
            $this->error('Apartmán nebyl nalezen');
        }

        $this['apartmentForm']->setDefaults($apartment->toArray());
    }

    public function renderEdit(int $id): void
    {
        $this->template->heading = 'Upravit apartmán';
        $this->template->apartmentId = $id;
    }

    // === Výpis rezervací pro apartmán ===
    public function renderReservation(int $apartmentId): void
    {
        $apartment = $this->apartmentRepository->getById($apartmentId);
        if (!$apartment) {
            $this->error('Apartmán nebyl nalezen');
        }

        $this->template->apartment = $apartment;
        $this->template->reservations = $this->reservationRepository->findByApartment($apartmentId);
    }

    // === Přidání rezervace ===
    public function actionAddReservation(): void
    {
        $this->template->form = $this->reservationFormFactory->create(function (array $values): void {
            $this->reservationRepository->insert($values);
            $this->flashMessage('Rezervace byla vytvořena.', 'success');
            $this->redirect('reservation', ['apartmentId' => $values['apartment_id']]);
        });
    }


    // === Editace rezervace ===
    public function actionEditReservation(int $id): void
    {
        $reservation = $this->reservationRepository->findByApartment($id);
        if (!$reservation) {
            $this->error('Rezervace nenalezena.');
        }

        $this->template->form = $this->reservationFormFactory->create(
            function (array $values) use ($id): void {
                $this->reservationRepository->update($id, $values);
                $this->flashMessage('Rezervace byla upravena.', 'success');
                $this->redirect('reservation', ['apartmentId' => $values['apartment_id']]);
            },
            $reservation->toArray()
        );
    }


    // === Formuláře ===

    protected function createComponentApartmentForm(): Form
    {
        return $this->apartmentFormFactory->create(function (\stdClass $values): void {
            $apartment = $this->apartmentRepository->insert([
                'name' => $values->name,
                'capacity' => $values->capacity,
                'description' => $values->description,
                'price' => $values->price,
            ]);

            $apartmentId = $apartment->id;
            $uploadDir = __DIR__ . '/../../../../www/uploads/apartments/' . $apartmentId;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // === Hlavní obrázek
            $mainImage = $this->getHttpRequest()->getFile('mainImage');
            if ($mainImage && $mainImage->isOk() && $mainImage->isImage()) {
                $filename = 'main_' . time() . '.' . $mainImage->getImageFileExtension();
                $mainImage->move("$uploadDir/$filename");
                $this->apartmentRepository->updateMainImage($apartmentId, $filename);
            }

            // === Galerie obrázků
            $gallery = $this->getHttpRequest()->getFiles()['images'] ?? [];
            if (is_array($gallery)) {
                foreach ($gallery as $i => $file) {
                    if ($file->isOk() && $file->isImage()) {
                        // 1. Vlož záznam do DB
                        $imageRow = $this->imagesApartmentRepository->insert([
                            'apartment_id' => $apartmentId,
                            'position' => $i,
                            'is_main' => 0,
                        ]);

                        // 2. Ulož soubor podle ID
                        $imageId = $imageRow->id;
                        $filename = "$imageId.jpg";
                        $file->move("$uploadDir/$filename");
                    }
                }
            }


            $this->flashMessage('Apartmán byl uložen.', 'success');
            $this->redirect('this');
        });
    }

    protected function createComponentReservationForm(): Form
    {
        return $this->reservationFormFactory->create(function (): void {
            $this->flashMessage('Rezervace byla uložena.', 'success');
            $this->redirect('this');
        });
    }
}
