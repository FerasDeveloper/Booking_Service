<?php

namespace App\Domains\Booking\Actions;

use App\Domains\Booking\DTOs\CancellationPolicyDTO;
use App\Domains\Booking\Repositories\Interface\ResourceRepositoryInterface;
use App\Models\Resource;

class SetCancellationPolicyAction
{
  public function __construct(
    private readonly ResourceRepositoryInterface $repository,
  ) {}

  public function execute(Resource $resource, array $policies): void
  {
    $dtos = array_map(
      fn(array $item) => CancellationPolicyDTO::fromArray($item, $resource->id),
      $policies
    );

    $this->repository->setPolicies($resource, $dtos);
  }
}
