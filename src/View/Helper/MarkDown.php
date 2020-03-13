<?php

namespace SymfonyDemo\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Parsedown;

class MarkDown extends AbstractHelper
{
    /**
     * @return Parsedown
     */
    public function __invoke()
    {
        return new Parsedown();
    }
}
