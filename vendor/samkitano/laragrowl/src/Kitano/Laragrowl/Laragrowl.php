<?php

namespace Kitano\Laragrowl;

use Illuminate\Support\Facades\Facade;

class Laragrowl extends Facade
{
    /**
     * Get the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laragrowl';
    }
}