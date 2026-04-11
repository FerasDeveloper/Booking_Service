<?php

namespace App\Domains\Booking\Read\Actions;

use App\Domains\Booking\Repositories\Interface\ResourceRepositoryInterface;

class IndexResourcesAction
{
  public function __construct(
    private readonly ResourceRepositoryInterface $repository,
  ) {}

  public function execute(int $projectId)
  {
    return $this->repository->listByProject($projectId);
  }
}
