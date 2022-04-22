<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/3/9
 * Time: 4:24 下午
 */

namespace App\Services\Satis;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Boot the provider.
     */
    public function boot()
    {
    }

    /**
     * Register the provider.
     */
    public function register()
    {
        $this->app->bind('satis', function ($app) {
            $config = $app->make('config')->get('satis', []);
            return new Client($config);
        });
    }
}
