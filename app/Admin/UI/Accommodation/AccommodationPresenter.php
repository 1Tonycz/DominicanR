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
        $apartment = $this->apartmentRepository->getById($id);
        if (!$apartment) {
            $this->error('Apartmán nebyl nalezen');
        }

        $this->template->heading = 'Upravit apartmán';
        $this->template->apartmentId = $id;
        $this->template->apartment = $apartment;
        $this->template->images = $this->imagesApartmentRepository->getImagesByApartment($id)->fetchAll();
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
        $id = $this->getParameter('id'); // získání ID pokud existuje
        $isEdit = $id !== null;

        return $this->apartmentFormFactory->create(function (\stdClass $values) use ($isEdit, $id): void {
            if ($isEdit) {
                $this->apartmentRepository->update($id, [
                    'name' => $values->name,
                    'capacity' => $values->capacity,
                    'description' => $values->description,
                    'price' => $values->price,
                ]);
                $apartmentId = $id;
            } else {
                $apartment = $this->apartmentRepository->insert([
                    'name' => $values->name,
                    'capacity' => $values->capacity,
                    'description' => $values->description,
                    'price' => $values->price,
                ]);
                $apartmentId = $apartment->id;
            }

            $uploadDir = __DIR__ . '/../../../../www/uploads/apartments/' . $apartmentId;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $files = $this->getHttpRequest()->getFiles();

/// === Hlavní obrázek
            $mainImage = $files['mainImage'] ?? null;
            if ($mainImage instanceof \Nette\Http\FileUpload && $mainImage->isOk() && $mainImage->isImage()) {
                $filename = 'main_' . time() . '.' . $mainImage->getImageFileExtension();
                $mainImage->move("$uploadDir/$filename");
                $this->apartmentRepository->updateMainImage($apartmentId, $filename);
            }

/// === Galerie
            $gallery = $files['images'] ?? null;

            if ($gallery instanceof \Nette\Http\FileUpload && $gallery->isOk() && $gallery->isImage()) {
                // jeden soubor – ošetření, kdyby `multiple` nebylo použito
                $gallery = [$gallery];
            }

            if (is_array($gallery)) {
                foreach ($gallery as $i => $file) {
                    if ($file instanceof \Nette\Http\FileUpload && $file->isOk() && $file->isImage()) {
                        $imageRow = $this->imagesApartmentRepository->insert([
                            'apartment_id' => $apartmentId,
                            'position' => $i,
                            'is_main' => 0,
                        ]);

                        $filename = $imageRow->id . '.jpg';
                        $file->move("$uploadDir/$filename");
                    }
                }
            }

            $this->flashMessage($isEdit ? 'Apartmán byl upraven.' : 'Apartmán byl uložen.', 'success');
            $this->redirect('default');
        });
    }


    protected function createComponentReservationForm(): Form
    {
        return $this->reservationFormFactory->create(function (): void {
            $this->flashMessage('Rezervace byla uložena.', 'success');
            $this->redirect('this');
        });
    }

    public function renderDefault(): void
    {
        $this->template->apartments = $this->apartmentRepository->getAll()->fetchAll();
    }

    public function handleDelete(int $id): void
    {
        $apartment = $this->apartmentRepository->getById($id);
        if (!$apartment) {
            $this->flashMessage('Apartmán nebyl nalezen.', 'error');
            $this->redirect('this');
        }

        $uploadDir = __DIR__ . "/../../../../www/uploads/apartments/$id";

        // 1. Najdi obrázky
        $images = $this->imagesApartmentRepository
            ->getBy(['apartment_id' => $id])
            ->fetchAll();

        // 2. Smaž soubory obrázků
        foreach ($images as $image) {
            $path = "$uploadDir/{$image->id}.jpg";
            if (is_file($path)) {
                unlink($path);
            }
        }

        // 3. Smaž záznamy obrázků z DB
        $this->imagesApartmentRepository
            ->getBy(['apartment_id' => $id])
            ->delete();

        // 4. Smaž hlavní obrázek (pokud existuje)
        if ($apartment->main_image_name) {
            $mainPath = "$uploadDir/{$apartment->main_image_name}";
            if (is_file($mainPath)) {
                unlink($mainPath);
            }
        }

        // 5. Smaž apartmán samotný
        $this->apartmentRepository->delete($id);

        // 6. Smaž adresář
        if (is_dir($uploadDir)) {
            $this->deleteDirectory($uploadDir);
        }

        $this->flashMessage('Apartmán byl úspěšně smazán včetně všech obrázků.', 'success');
        $this->redirect('this');
    }


    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = "$dir/$item";
            if (is_dir($path)) {
                $this->deleteDirectory($path); // rekurze pro podadresáře
            } else {
                unlink($path);
            }
        }

        rmdir($dir);
    }

    public function handleDeleteImage(int $id): void
    {
        if (!$this->isAjax()) {
            $this->error('Pouze AJAX požadavek', 400);
        }

        $image = $this->imagesApartmentRepository->getById($id);
        if (!$image) {
            $this->sendJson(['status' => 'error', 'message' => 'Obrázek nenalezen']);
            return;
        }

        $uploadDir = __DIR__ . '/../../../../www/uploads/apartments/' . $image->apartment_id;
        $filePath = "$uploadDir/{$image->id}.jpg";

        if (is_file($filePath)) {
            unlink($filePath);
        }

        $this->imagesApartmentRepository->delete($id);

        $this->sendJson(['status' => 'ok']);
    }

}
