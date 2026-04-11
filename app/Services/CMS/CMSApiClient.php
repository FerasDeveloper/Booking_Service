<?php

namespace App\Services\CMS;

use App\Domains\Core\Traits\HasProjectHeaders;
use Illuminate\Support\Facades\Http;

class CMSApiClient
{
  use HasProjectHeaders;

  protected string $baseUrl;

  public function __construct()
  {
    $this->baseUrl = rtrim(config('services.cms_service.url'), '/');
  }

  public function resolveProject()
  {
    $response = Http::withHeaders(
      $this->projectHeaders()
    )->get("{$this->baseUrl}/api/projects/resolve");
    if ($response->failed()) {
      $error = $response->json('message')
        ?? substr($response->body(), 0, 200);

      throw new \Exception("Failed to resolve project in CMS: " . $error);
    }

    return $response->json()['original'];
  }

  public function createCollection(array $data): array
  {
    $response = Http::withHeaders(
      $this->projectHeaders()
    )->post("{$this->baseUrl}/api/cms/collections", $data);

    if ($response->failed()) {
      $error = $response->json('message')
        ?? substr($response->body(), 0, 200);

      throw new \Exception("Failed to create collection in CMS: " . $error);
    }

    return $response->json();
  }
}
