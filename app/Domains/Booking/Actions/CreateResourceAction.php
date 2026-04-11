<?php

namespace App\Domains\Booking\Actions;

use App\Domains\Booking\DTOs\ResourceDTO;
use App\Domains\Booking\Repositories\Interface\ResourceRepositoryInterface;
use App\Models\Resource;

class CreateResourceAction
{
  public function __construct(
    private readonly ResourceRepositoryInterface $repository,
  ) {}

  public function execute(ResourceDTO $dto): Resource
  {
    return $this->repository->create($dto);
  }
}
