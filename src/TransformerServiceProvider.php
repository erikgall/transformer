<?php

namespace EGALL\Transformer;

use Illuminate\Support\ServiceProvider;
use EGALL\Transformer\Contracts\Transformer as TransformerContract;

/**
 * Transformer service provider.
 *
 * @package EGALL\Transformer
 * @author Erik Galloway <erik@mybarnapp.com>
 */
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
