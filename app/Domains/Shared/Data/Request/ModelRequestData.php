<?php

declare(strict_types=1);

namespace App\Domains\Shared\Data\Request;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class ModelRequestData extends Data
{
    public function __construct(
        public bool $trashed = false,
    ) {}

    public static function fromRequest(Request $request): static
    {
        return new static(
            trashed: $request->boolean(key: 'trashed', default: false)
        );
    }
}
