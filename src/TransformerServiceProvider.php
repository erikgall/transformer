<?php

namespace App\Providers;

use EGALL\Transformer\Contracts\Transformer as TransformerContract;
use EGALL\Transformer\Transformer;
use Illuminate\Support\ServiceProvider;

class TransformerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(TransformerContract::class, Transformer::class);
    }
}
