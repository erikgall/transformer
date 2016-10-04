<?php

namespace EGALL\Transformer;

use Illuminate\Support\ServiceProvider;
use EGALL\Transformer\Contracts\Transformer as TransformerContract;
use EGALL\Transformer\Contracts\CollectionTransformer as CollectionTransformerContract;

/**
 * Transformer service provider.
 *
 * @author Erik Galloway <erik@mybarnapp.com>
 */
class TransformerServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

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
        $this->app->bind(CollectionTransformerContract::class, CollectionTransformer::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [TransformerContract::class, CollectionTransformerContract::class];
    }
}
