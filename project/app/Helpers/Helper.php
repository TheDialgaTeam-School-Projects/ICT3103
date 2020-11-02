<?php

namespace App\Helpers;

use App\Helpers\Traits\ConfigHelperTrait;
use App\Helpers\Traits\LockoutHelperTrait;
use App\Helpers\Traits\RedirectHelperTrait;
use App\Helpers\Traits\SessionHelperTrait;
use App\Helpers\Traits\TranslatorHelperTrait;
use App\Helpers\Traits\ViewHelperTrait;

class Helper
{
    use ConfigHelperTrait,
        LockoutHelperTrait,
        RedirectHelperTrait,
        SessionHelperTrait,
        TranslatorHelperTrait,
        ViewHelperTrait;
}
