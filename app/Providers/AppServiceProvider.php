<?php

namespace App\Providers;

use App\Domains\Booking\Repositories\Eloquent\EloquentResourceRepository;
use App\Domains\Booking\Repositories\Interface\ResourceRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    $this->app->bind(ResourceRepositoryInterface::class, EloquentResourceRepository::class);
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    //
  }
}
