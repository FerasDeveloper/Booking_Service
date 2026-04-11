<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resource extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'data_entry_id',
    'project_id',
    'name',
    'type',
    'capacity',
    'status',
    'settings',
  ];

  protected $casts = [
    'capacity' => 'integer',
    'settings' => 'array',
  ];

  // ─── Statuses ─────────────────────────────────────────────────────────────

  const STATUS_ACTIVE   = 'active';
  const STATUS_INACTIVE = 'inactive';

  // ─── Relationships ────────────────────────────────────────────────────────

  public function availabilities(): HasMany
  {
    return $this->hasMany(ResourceAvailability::class);
  }

  public function activeAvailabilities(): HasMany
  {
    return $this->hasMany(ResourceAvailability::class)
      ->where('is_active', true);
  }

  public function cancellationPolicies(): HasMany
  {
    return $this->hasMany(BookingCancellationPolicy::class)
      ->orderByDesc('hours_before'); // ترتيب تنازلي للمطابقة الصحيحة
  }

  public function bookings(): HasMany
  {
    return $this->hasMany(Booking::class);
  }

  // ─── Helpers ──────────────────────────────────────────────────────────────

  public function isActive(): bool
  {
    return $this->status === self::STATUS_ACTIVE;
  }

  public function availabilityForDay(int $dayOfWeek): ?ResourceAvailability
  {
    return $this->activeAvailabilities()
      ->where('day_of_week', $dayOfWeek)
      ->first();
  }
}
