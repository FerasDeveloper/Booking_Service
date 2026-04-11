<?php

namespace App\Domains\Booking\Read\Actions;

use App\Domains\Booking\Repositories\Interface\ResourceRepositoryInterface;
use App\Models\Resource;

class ShowResourceAction
{
  public function __construct(
    private readonly ResourceRepositoryInterface $repository,
  ) {}

  public function execute(int $id): ?Resource
  {
    return $this->repository->findById($id);
  }
}
