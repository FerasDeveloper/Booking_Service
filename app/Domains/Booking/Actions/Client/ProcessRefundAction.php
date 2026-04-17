<?php

namespace App\Domains\Booking\Actions\Client;

use App\Services\CMS\CMSApiClient;

class ProcessRefundAction
{
  public function __construct(
    protected CMSApiClient $cmsClient
  ) {}

  public function execute($booking, float $amount): void
  {
    $this->cmsClient->refundBooking([
      'payment_id' => $booking->payment_id,
      'amount'     => $amount,
    ]);
  }
}
