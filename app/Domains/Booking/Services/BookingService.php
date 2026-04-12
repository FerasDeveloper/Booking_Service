<?php

namespace App\Domains\Booking\Services;

use App\Domains\Booking\Read\Actions\GetResourceBookingsAction;
use App\Domains\Booking\Read\DTOs\GetResourceBookingsDTO;
use App\Domains\Booking\Read\DTOs\GetResourceSlotsDTO;
use App\Domains\Booking\Repositories\Interface\ResourceRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class BookingService
{
  public function __construct(
    private readonly SlotGeneratorService        $slotGenerator,
    private readonly ResourceRepositoryInterface $resourceRepository,
    private readonly GetResourceBookingsAction   $getResourceBookingsAction,
  ) {}

  public function getAvailableSlots(GetResourceSlotsDTO $dto): array
  {
    $resource = $this->resourceRepository->findById($dto->resourceId);

    throw_if(! $resource,           \Exception::class, 'Resource not found.');
    throw_if(! $resource->isActive(), \Exception::class, 'Resource is not active.');

    $carbon = Carbon::parse($dto->date);

    throw_if(
      $carbon->isPast() && ! $carbon->isToday(),
      \Exception::class,
      'Cannot view slots for past dates.'
    );

    return [
      'resource_id' => $dto->resourceId,
      'date'        => $carbon->format('Y-m-d'),
      'day'         => $carbon->format('l'),
      'slots'       => $this->slotGenerator->generate($resource, $carbon),
    ];
  }

  public function getResourceBookings(GetResourceBookingsDTO $dto): Collection
  {
    return $this->getResourceBookingsAction->execute($dto);
  }
}
