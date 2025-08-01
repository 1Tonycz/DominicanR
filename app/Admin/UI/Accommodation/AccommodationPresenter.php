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
use Nepada\FileUploadControl\FileUploadControl;
use Nepada\FileUploadControl\UploadReceivedFile;

final class AccommodationPresenter extends BasePresenter
{
    public function __construct(
        private ApartmentRepository $apartmentRepository,
        private ReservationRepository $reservationRepository,
        private ApartmentFormFactory $apartmentFormFactory,
        private ReservationFormFactory $reservationFormFactory,
        private ImagesApartmentRepository $imagesApartmentRepository,
    ) {}

    public function renderAdd(): void
    {
        $this->template->heading = 'Přidat apartmán';
    }

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

    protected function createComponentApartmentForm(): Form
    {
        $id = $this->getParameter('id');
        $isEdit = $id !== null;

        return $this->apartmentFormFactory->create(function (\stdClass $values) use ($isEdit, $id): void {

            // Uložení dat z formuláře
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

            // Získání komponent pro soubory
            /** @var FileUploadControl $mainImageControl */
            $mainImageControl = $this['apartmentForm']['main_image'];
            /** @var UploadReceivedFile[] $mainImages */
            $mainImages = $mainImageControl->getValue();

            /** @var FileUploadControl $galleryControl */
            $galleryControl = $this['apartmentForm']['gallery'];
            /** @var UploadReceivedFile[] $galleryImages */
            $galleryImages = $galleryControl->getValue();

            // Vytvoření složky
            $uploadDir = __DIR__ . '/../../../../www/uploads/apartments/' . $apartmentId;
            bdump($uploadDir, 'Cílová složka pro upload');
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // === Uložení hlavního obrázku ===
            foreach ($mainImages as $image) {
                $tmpPath = $image->getTemporaryFile();
                if ($tmpPath && is_file($tmpPath)) {
                    $filename = 'main_' . time() . '.jpg';
                    copy($tmpPath, "$uploadDir/$filename");
                    $this->apartmentRepository->updateMainImage($apartmentId, $filename);
                    break; // jen první
                }
            }

            foreach ($galleryImages as $i => $image) {
                $tmpPath = $image->getTemporaryFile();
                if ($tmpPath && is_file($tmpPath)) {
                    $imageRow = $this->imagesApartmentRepository->insert([
                        'apartment_id' => $apartmentId,
                        'position' => $i,
                        'is_main' => 0,
                    ]);
                    $filename = $imageRow->id . '.jpg';
                    copy($tmpPath, "$uploadDir/$filename");
                }
            }


            $this->flashMessage($isEdit ? 'Apartmán byl upraven.' : 'Apartmán byl uložen.', 'success');
            $this->redirect('default');
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

        $images = $this->imagesApartmentRepository->getBy(['apartment_id' => $id])->fetchAll();
        foreach ($images as $image) {
            $path = "$uploadDir/{$image->id}.jpg";
            if (is_file($path)) {
                unlink($path);
            }
        }

        $this->imagesApartmentRepository->getBy(['apartment_id' => $id])->delete();

        if ($apartment->main_image_name) {
            $mainPath = "$uploadDir/{$apartment->main_image_name}";
            if (is_file($mainPath)) {
                unlink($mainPath);
            }
        }

        $this->apartmentRepository->delete($id);

        if (is_dir($uploadDir)) {
            $this->deleteDirectory($uploadDir);
        }

        $this->flashMessage('Apartmán byl úspěšně smazán včetně všech obrázků.', 'success');
        $this->redirect('this');
    }

    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) return;
        foreach (scandir($dir) as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = "$dir/$item";
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
}
