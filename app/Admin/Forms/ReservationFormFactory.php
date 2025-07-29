<?php

namespace App\Admin\Forms;

use App\Core\Repository\Admin\ReservationRepository;
use App\Core\Repository\Admin\ApartmentRepository;
use Nette\Application\UI\Form;
use Nette\SmartObject;

class ReservationFormFactory
{
    use SmartObject;

    public function __construct(
        private ReservationRepository $reservationRepository,
        private ApartmentRepository $apartmentRepository
    ) {}

    public function create(callable $onSuccess, ?array $defaultValues = null): Form
    {
        $form = new Form;

        // Získání apartmánů pro výběr
        $apartments = $this->apartmentRepository->findAll()->fetchPairs('id', 'name');

        $form->addSelect('apartment_id', 'Apartmán:', $apartments)
            ->setPrompt('Vyberte apartmán')
            ->setRequired('Vyberte apartmán');

        $form->addText('first_name', 'Jméno')
            ->setRequired();

        $form->addText('second_name', 'Příjmení')
            ->setRequired();

        $form->addEmail('email', 'E-mail')
            ->setRequired();

        $form->addText('phone', 'Telefon')
            ->setRequired();

        $form->addText('from', 'Od')
            ->setHtmlType('date')
            ->setRequired();

        $form->addText('to', 'Do')
            ->setHtmlType('date')
            ->setRequired();

        $form->addTextArea('note', 'Poznámka');

        $form->addSubmit('send', 'Uložit');

        if ($defaultValues) {
            $form->setDefaults($defaultValues);
        }

        $form->onSuccess[] = function (Form $form, array $values) use ($onSuccess): void {
            $onSuccess($values);
        };

        return $form;
    }
}
