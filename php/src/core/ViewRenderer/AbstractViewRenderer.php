<?php

namespace teamwork\core\ViewRenderer;

use Exception;

abstract class AbstractViewRenderer
{
    /** @var array View parameters. */
    protected array $viewParameters;

    /**
     * ViewRenderer constructor.
     * @param array $viewParameters
     */
    protected function __construct(array $viewParameters)
    {
        $this->viewParameters = $viewParameters;
    }

    /**
     * Render this view.
     * @throws Exception Default page view template is missing.
     */
    public abstract function Render(): void;
}
