<?php

declare(strict_types=1);

namespace App\Admin\Forms;

use Nette\Application\UI\Form;
use Nette\Forms\Form as NetteForm;

final class ApartmentFormFactory
{
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

        $form->addSubmit('save', 'Uložit')
            ->setHtmlAttribute('class', 'button-submit');

        $form->onSuccess[] = function (Form $form, \stdClass $values) use ($onSuccess): void {
            $onSuccess($values);
        };

        return $form;
    }

}
