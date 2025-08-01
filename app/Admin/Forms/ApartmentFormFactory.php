<?php

declare(strict_types=1);

namespace App\Admin\Forms;

use Nette\Application\UI\Form;
use Nepada\FileUploadControl\FileUploadControlFactory;
use Nepada\FileUploadControl\Storage\StorageManager;

final class ApartmentFormFactory
{
    public function __construct(
        private FileUploadControlFactory $fileUploadControlFactory,
        private StorageManager $storageManager,
    ) {}

    public function create(callable $onSuccess): Form
    {
        $form = new Form;

        $form->addText('name', 'Název apartmánu:')
            ->setRequired('Zadejte název.');

        $form->addInteger('capacity', 'Kapacita:')
            ->setRequired('Zadejte kapacitu.')
            ->addRule($form::MIN, 'Minimální kapacita je 1.', 1);

        $form->addTextArea('description', 'Popis:')
            ->setHtmlAttribute('rows', 5);

        $form->addInteger('price', 'Cena za noc:')
            ->setRequired('Zadejte cenu.')
            ->addRule($form::FLOAT, 'Cena musí být číslo.');

        // === Hlavní obrázek ===
        $mainImage = $this->fileUploadControlFactory->create('main_image', 'Hlavní obrázek');
        $mainImage
            ->setRequired('Zadejte hlavní obrázek.')
            ->addRule($form::IMAGE, 'Musí být obrázek (JPEG, PNG, GIF).')
            ->addRule($form::MAX_LENGTH, 'Maximálně jeden obrázek.', 1);
        $mainImage->getControlPrototype()
            ->setAttribute('accept', 'image/jpeg,image/png,image/gif')
            ->setAttribute('data-max-files', 1);
        $form->addComponent($mainImage, 'main_image');

        // === Galerie ===
        $gallery = $this->fileUploadControlFactory->create('gallery', 'Galerie apartmánů');
        $gallery
            ->addRule($form::IMAGE, 'Všechny soubory musí být obrázky.')
            ->addRule($form::MAX_LENGTH, 'Maximálně 10 obrázků.', 10);
        $gallery->getControlPrototype()
            ->setAttribute('multiple', true)
            ->setAttribute('accept', 'image/jpeg,image/png,image/gif')
            ->setAttribute('data-max-files', 10);
        $form->addComponent($gallery, 'gallery');

        $form->addSubmit('save', 'Uložit')
            ->setHtmlAttribute('class', 'button-submit');

        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($onSuccess): void {
            $onSuccess($values);
        };

        return $form;
    }
}
