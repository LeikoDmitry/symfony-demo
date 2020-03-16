<?php

declare(strict_types=1);

namespace SymfonyDemo\Controller;

use Laminas\Authentication\AuthenticationServiceInterface;
use Laminas\Form\Element\Csrf;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Form\Annotation\AnnotationBuilder;
use SymfonyDemo\Entity\User;

class SecurityController extends AbstractActionController
{
    /**
     * @var int
     */
    private const CSRF_TIMEOUT = 600;

    /**
     * @var AuthenticationServiceInterface
     */
    private $authenticationService;

    /**
     * @param  AuthenticationServiceInterface  $authService
     */
    public function __construct(AuthenticationServiceInterface $authService)
    {
        $this->authenticationService = $authService;
    }

    /**
     * @return Response|ViewModel
     */
    public function loginAction()
    {
        $builder = new AnnotationBuilder();
        $form = $builder->createForm(User::class);
        $form->add([
            'type' => Csrf::class,
            'name' => '__csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => static::CSRF_TIMEOUT,
                ],
            ],
        ]);
        if (! $this->getRequest()->isPost()) {

            return new ViewModel(['form' => $form]);
        }
        $request = $this->getRequest();
        $data = $request->getPost();
        $form->setData($data);
        $form->setValidationGroup('username', 'password', '__csrf');
        if (! $form->isValid()) {

            return $this->redirect()->toRoute('login');
        }
        $adapter = $this->authenticationService->getAdapter();
        $adapter->setIdentity($form->getData()['username']);
        $adapter->setCredential($form->getData()['password']);
        $authResult = $this->authenticationService->authenticate();
        if (! $authResult->isValid()) {
            return $this->redirect()->toRoute('login');
        }
        $identity = $authResult->getIdentity();
        $this->authenticationService->getStorage()->write($identity);

        return $this->redirect()->toRoute('home');
    }

    /**
     * @return ViewModel
     */
    public function logoutAction()
    {
        return new ViewModel();
    }
}
