<?php

namespace App\Domains\Booking\Repositories\Interface;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Collection;

interface BookingRepositoryInterface
{
  public function findById(int $id): ?Booking;

  public function listByResource(
    int     $resourceId,
    ?string $status = null,
    ?string $from   = null,
    ?string $to     = null,
  ): Collection;

  public function listByUser(int $userId, ?string $status = null): Collection;
}
