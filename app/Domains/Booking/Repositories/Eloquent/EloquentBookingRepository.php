<?php

namespace App\Domains\Booking\Repositories\Eloquent;

use App\Domains\Booking\Repositories\Interface\BookingRepositoryInterface;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Collection;

class EloquentBookingRepository implements BookingRepositoryInterface
{
  public function findById(int $id): ?Booking
  {
    return Booking::with('resource')->find($id);
  }

  public function listByResource(
    int     $resourceId,
    ?string $status = null,
    ?string $from   = null,
    ?string $to     = null,
  ): Collection {
    $query = Booking::where('resource_id', $resourceId)
      ->with('resource')
      ->orderBy('start_at');

    if ($status) {
      $query->where('status', $status);
    }

    if ($from) {
      $query->where('start_at', '>=', $from);
    }

    if ($to) {
      $query->where('start_at', '<=', $to);
    }

    return $query->get();
  }

  /**
   * حجوزات مستخدم معين
   */
  public function listByUser(int $userId, ?string $status = null): Collection
  {
    $query = Booking::where('user_id', $userId)
      ->with('resource')
      ->orderByDesc('start_at');

    if ($status) {
      $query->where('status', $status);
    }

    return $query->get();
  }
}
