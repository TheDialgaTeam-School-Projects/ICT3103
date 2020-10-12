<?php

namespace teamwork\controller;

use teamwork\core\application\ApplicationInterface;
use teamwork\core\controller\AbstractController;
use teamwork\core\response\ResponseInterface;
use teamwork\service\AuthenticationService;
use teamwork\service\CsrfTokenService;
use teamwork\service\MySqlService;

class AuthenticationController extends AbstractController
{
    /** @var CsrfTokenService Csrf token service object. */
    private CsrfTokenService $csrfTokenService;

    /** @var MySqlService Mysql service object. */
    private MySqlService $mysqlService;

    /** @var AuthenticationService Authentication service object. */
    private AuthenticationService $authenticationService;

    public function __construct(ApplicationInterface $app)
    {
        parent::__construct($app);
        $this->csrfTokenService = new CsrfTokenService();
        $this->mysqlService = new MySqlService($app->getConfig()['Mysqli']);
        $this->authenticationService = new AuthenticationService($this->mysqlService);
    }

    public function actionIndex(): ResponseInterface
    {
        if ($this->isGetMethod()) {
            return $this->view('Authentication/login', [
                'pageTitle' => 'Login',
                'csrfToken' => $this->csrfTokenService->getCsrfToken('login'),
            ]);
        } else if ($this->isPostMethod()) {
            // Csrf check
            if (!$this->csrfTokenService->isCsrfTokenValid('login', $_POST['csrfToken'])) {
                return $this->error('Invalid request.');
            }

            // Login attempt
            $errorMessage = null;

            if ($this->authenticationService->login($_POST['username'], $_POST['password'])) {
                return $this->redirect($this->getRouteUri('Home'), ResponseInterface::RESPONSE_STATUS_CODE_SEE_OTHER);
            } else {
                return $this->view('Authentication/login', [
                    'pageTitle' => 'Login',
                    'csrfToken' => $this->csrfTokenService->getCsrfToken('login'),
                    'errorMessage' => 'Either username or password mismatch.',
                ]);
            }
        } else {
            return $this->error('Invalid request.');
        }
    }

    function actionRegister(): ResponseInterface
    {
        if ($this->isGetMethod()) {
            return $this->view('Authentication/register', [
                'pageTitle' => 'Register an account',
                'csrfToken' => $this->csrfTokenService->getCsrfToken('register'),
            ]);
        } else if ($this->isPostMethod()) {
            // Csrf check
            if (!$this->csrfTokenService->isCsrfTokenValid('register', $_POST['csrfToken'])) {
                return $this->error('Invalid request.');
            }

            // Validate and create an account.
            $errorMessage = '';

            if ($this->authenticationService->register($_POST, $errorMessage)) {
                return $this->redirect($this->getRouteUri('Login'), ResponseInterface::RESPONSE_STATUS_CODE_SEE_OTHER);
            } else {
                return $this->view('Authentication/register', [
                    'pageTitle' => 'Register an account',
                    'csrfToken' => $this->csrfTokenService->getCsrfToken('register'),
                    'errorMessage' => $errorMessage,
                ]);
            }
        } else {
            return $this->error('Invalid request.');
        }
    }
}
