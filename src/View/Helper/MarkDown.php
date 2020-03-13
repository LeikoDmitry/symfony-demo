<?php

namespace SymfonyDemo\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Twig\Extra\Markdown\DefaultMarkdown;
use Twig\Extra\Markdown\MarkdownRuntime;

class MarkDown extends AbstractHelper
{
    /**
     * @return MarkdownRuntime
     */
    public function __invoke()
    {
        return new MarkdownRuntime(new DefaultMarkdown());
    }
}
