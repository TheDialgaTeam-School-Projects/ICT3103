<?php

namespace teamwork\controller;

use teamwork\core\application\ApplicationInterface;
use teamwork\core\controller\AbstractController;
use teamwork\core\response\ResponseInterface;
use teamwork\service\AuthenticationService;
use teamwork\service\CsrfTokenService;
use teamwork\service\MySqlService;

class HomeController extends AbstractController
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

    public function beforeDispatch(): ?ResponseInterface
    {
        if (!$this->authenticationService->isLoggedIn()) {
            return $this->error('User is not logged in.', ResponseInterface::RESPONSE_STATUS_CODE_FORBIDDEN);
        } else {
            return parent::beforeDispatch();
        }
    }

    public function actionIndex(): ResponseInterface {
        return $this->view('Home/home', [
            'pageTitle' => 'Welcome',
        ]);
    }
}
