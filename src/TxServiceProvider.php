<?php

namespace Tx\CryptId;

use Illuminate\Support\ServiceProvider;

class TxServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(CryptId::class, function () {
            return new CryptId();
        });
    }

    public function boot()
    {
        //
    }
}