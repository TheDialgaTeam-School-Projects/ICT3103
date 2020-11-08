<?php

namespace App\View\Components;

use App\Helpers\Helper;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Alert extends Component
{
    /**
     * @var string
     */
    public $alertType;

    /**
     * @var string
     */
    public $alertMessage;

    /**
     * Create a new component instance.
     *
     * @param string $alertType
     * @param string $alertMessage
     */
    public function __construct(string $alertType, string $alertMessage)
    {
        $this->alertType = $alertType;
        $this->alertMessage = $alertMessage;

        switch ($alertType) {
            case 'primary':
            case 'secondary':
            case 'success':
            case 'danger':
            case 'warning':
            case 'info':
            case 'light':
            case 'dark':
                $this->alertType = $alertType;
                break;

            case 'error':
                $this->alertType = 'danger';
                break;

            default:
                $this->alertType = 'primary';
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|string
     */
    public function render()
    {
        return Helper::view('components.alert');
    }
}
