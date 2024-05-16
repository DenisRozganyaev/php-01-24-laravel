<?php

namespace App\Providers;

use App\Repositories\Contract\ImageRepositoryContract;
use App\Repositories\Contract\ProductRepositoryContract;
use App\Repositories\ImageRepository;
use App\Repositories\ProductRepository;
use App\Services\Contract\FileServiceContract;
use App\Services\FileService;
use App\Services\Payments\Contract\PaypalServiceContract;
use App\Services\Payments\PaypalService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public array $bindings = [
        ProductRepositoryContract::class => ProductRepository::class,
        FileServiceContract::class => FileService::class,
        ImageRepositoryContract::class => ImageRepository::class,
        PaypalServiceContract::class => PaypalService::class
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
    }
}
