<?php

namespace teamwork\core\response\view;

use teamwork\core\exception\response\view\ViewTemplateNotFoundException;
use teamwork\core\response\ResponseInterface;

/**
 * Interface for handling view response.
 * @package teamwork\core\response
 */
interface ViewResponseInterface extends ResponseInterface
{
    /**
     * Render the view.
     * @throws ViewTemplateNotFoundException view template could not be found.
     */
    public function render(): void;
}
