<?php

declare(strict_types=1);

namespace App\Front\UI;


use App\Front\Components\footer\FooterControl;
use App\Front\Components\Navbar\NavbarControl;
use Nette\Application\UI\Presenter;

/**
 * @property-read $template
 */


class BasePresenter extends Presenter
{
    public function beforeRender(): void
    {
        $this->template->currVer = date("dmHis");
    }


    protected function getBasePath(): string
    {
        return __DIR__ . "/../../../www";
    }

    protected function createComponentNavbar(): NavbarControl
    {
        return new NavbarControl();
    }

    protected function createComponentFooter(): FooterControl
    {
        return new FooterControl();
    }
}