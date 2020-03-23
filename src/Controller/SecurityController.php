<?php

declare(strict_types=1);

namespace SymfonyDemo\Controller;

use Laminas\Authentication\AuthenticationServiceInterface;
use Laminas\Form\Element\Csrf;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Session\SessionManager;
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
        if ($this->identity()) {

            return $this->redirect()->toRoute('blog');
        }
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
        $prg = $this->prg('login', true);
        if ($prg instanceof Response) {

            return $prg;
        }
        if ($prg === false) {

            return new ViewModel(['form' => $form]);
        }
        $form->setData($prg);
        $form->setValidationGroup('username', 'password', '__csrf');
        if (! $form->isValid()) {

            return new ViewModel(['form' => $form]);
        }
        $adapter = $this->authenticationService->getAdapter();
        $adapter->setIdentity($form->getData()['username'] ?? '');
        $adapter->setCredential($form->getData()['password'] ?? '');
        $authResult = $this->authenticationService->authenticate();
        if (! $authResult->isValid()) {
            $form->setMessages([
                'username' => $authResult->getMessages()
            ]);

            return new ViewModel(['form' => $form]);
        }
        $identity = $authResult->getIdentity();
        $this->authenticationService->getStorage()->write($identity);

        return $this->redirect()->toRoute('blog');
    }

    /**
     * @return Response
     */
    public function logoutAction(): Response
    {
        if ($this->authenticationService->hasIdentity()) {
            $this->authenticationService->clearIdentity();
            $sessionManager = new SessionManager();
            $sessionManager->forgetMe();
        }

        return $this->redirect()->toRoute('login');
    }
}
