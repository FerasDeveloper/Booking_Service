<?php

namespace App\Domains\Booking\Actions;

use App\Domains\Booking\DTOs\ResourceDTO;
use App\Domains\Booking\Repositories\Interface\ResourceRepositoryInterface;
use App\Models\Resource;

class UpdateResourceAction
{
  public function __construct(
    private readonly ResourceRepositoryInterface $repository,
  ) {}

  public function execute(Resource $resource, ResourceDTO $dto): Resource
  {
    return $this->repository->update($resource, $dto);
  }
}
