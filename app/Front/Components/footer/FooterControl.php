<?php

declare(strict_types=1);

namespace App\Front\Components\footer;

use Nette\Application\UI\Control;
use Nette\Bridges\ApplicationLatte\Template;

/**
 * @property-read Template $template
 */

class FooterControl extends Control
{
    public function render(): void
    {
        $this->template->render(__DIR__ . "/footer.latte");
    }
}