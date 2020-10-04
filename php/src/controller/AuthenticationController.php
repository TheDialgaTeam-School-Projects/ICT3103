<?php

namespace teamwork\controller;

use teamwork\core\AbstractController;
use teamwork\core\Response;
use teamwork\service\AuthenticationService;

class AuthenticationController extends AbstractController
{
    function actionIndex()
    {
        if ($this->isGet()) {
            return $this->view('Authentication/login', [
                'pageTitle' => 'Login'
            ]);
        } else if ($this->isPost()) {
            // Csrf check
            if (!$this->getApp()->getCsrfTokenService()->validateCsrfToken('csrfToken', $_POST['csrfToken'])) {
                return $this->error('Invalid request.', Response::RESPONSE_STATUS_CODE_FORBIDDEN);
            }

            // Login attempt
            $errorMessage = null;
            $authenticationService = new AuthenticationService($this->getDb());

            if ($authenticationService->login($_POST['username'], $_POST['password'])) {
                $this->redirect($this->getRouteUri('Home'), Response::RESPONSE_STATUS_CODE_SEE_OTHER);
            } else {
                return $this->view('Authentication/login', [
                    'pageTitle' => 'Login',
                    'errorMessage' => 'Either username or password mismatch.',
                ]);
            }
        }
    }

    function actionRegister()
    {
        if ($this->isGet()) {
            return $this->view('Authentication/register', [
                'pageTitle' => 'Register an account'
            ]);
        } else if ($this->isPost()) {
            // Csrf check
            if (!$this->getApp()->getCsrfTokenService()->validateCsrfToken('csrfToken', $_POST['csrfToken'])) {
                return $this->error('Invalid request.', Response::RESPONSE_STATUS_CODE_FORBIDDEN);
            }

            // Validate and create an account.
            $errorMessage = '';
            $authenticationService = new AuthenticationService($this->getDb());

            if ($authenticationService->register($_POST, $errorMessage)) {
                $this->redirect($this->getRouteUri('login'), Response::RESPONSE_STATUS_CODE_SEE_OTHER);
            } else {
                return $this->view('Authentication/register', [
                    'pageTitle' => 'Register an account',
                    'errorMessage' => $errorMessage
                ]);
            }
        }
    }
}
