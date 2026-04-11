<?php

namespace App\Domains\Booking\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateResourceRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'data_entry_id' => ['required', 'integer'],
      'name'          => ['required', 'string', 'max:255'],
      'type'          => ['required', 'string', 'max:100'],
      'capacity'      => ['nullable', 'integer', 'min:1'],
      'settings'      => ['nullable', 'array'],
    ];
  }
}
