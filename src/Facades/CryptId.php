<?php

declare(strict_types=1);

namespace Tx\CryptId\Facades;

use Illuminate\Support\Facades\Facade;

class CryptId extends Facade
{
        protected static function getFacadeAccessor()
        {
                return \Iceberg\CryptId\CryptId::class;
        }
}
