<?php

namespace Tx\CryptId\Facades;

use Illuminate\Support\Facades\Facade;

class CryptId extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Tx\CryptId\CryptId::class;
    }
}