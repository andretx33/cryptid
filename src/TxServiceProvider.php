<?php

declare(strict_types=1);

namespace Tx\CryptId;

use Illuminate\Support\ServiceProvider;

class TxServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CryptId::class, fn (): CryptId => new CryptId());
    }

    public function boot(): void
    {
        //
    }
}
