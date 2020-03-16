<?php

declare(strict_types=1);

namespace SymfonyDemo\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class UserController extends AbstractActionController
{
    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        return new ViewModel();
    }
}
