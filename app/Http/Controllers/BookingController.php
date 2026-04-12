<?php

namespace App\Http\Controllers;

use App\Domains\Booking\Read\DTOs\GetResourceBookingsDTO;
use App\Domains\Booking\Read\DTOs\GetResourceSlotsDTO;
use App\Domains\Booking\Requests\GetResourceBookingsRequest;
use App\Domains\Booking\Requests\GetSlotsRequest;
use App\Domains\Booking\Services\BookingService;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
  public function __construct(
    private readonly BookingService $service,
  ) {}

  public function slots(GetSlotsRequest $request, int $resourceId): JsonResponse
  {
    try {
      $dto = GetResourceSlotsDTO::fromRequest($resourceId, $request);
      $result = $this->service->getAvailableSlots($dto);

      return response()->json(['data' => $result]);
    } catch (\Exception $e) {
      return response()->json(['message' => $e->getMessage()], 422);
    }
  }

  public function resourceBookings(GetResourceBookingsRequest $request, int $resourceId): JsonResponse
  {
    $dto = GetResourceBookingsDTO::fromRequest($resourceId, $request);
    $bookings = $this->service->getResourceBookings($dto);

    return response()->json(['data' => $bookings]);
  }
}
