<?php

namespace teamwork\controller;

use Exception;
use teamwork\core\AbstractController;
use teamwork\service\AuthenticationService;

abstract class AbstractSecureController extends AbstractController
{
    public function beforeDispatch(): void
    {
        // Ensure that user is logged in or throw unauthorized error.
        $authenticationService = new AuthenticationService($this->getDb());
        if (!$authenticationService->isLoggedIn()) throw new Exception('User is not logged in!');
    }
}
