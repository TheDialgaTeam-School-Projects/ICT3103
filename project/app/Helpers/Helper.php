<?php

namespace App\Helpers;

use App\Helpers\Traits\ConfigHelperTrait;
use App\Helpers\Traits\RedirectorHelperTraits;
use App\Helpers\Traits\RouteHelperTrait;
use App\Helpers\Traits\SessionHelperTrait;
use App\Helpers\Traits\TranslatorHelperTrait;
use App\Helpers\Traits\ViewHelperTrait;

class Helper
{
    use ConfigHelperTrait,
        RedirectorHelperTraits,
        RouteHelperTrait,
        SessionHelperTrait,
        TranslatorHelperTrait,
        ViewHelperTrait;
}
