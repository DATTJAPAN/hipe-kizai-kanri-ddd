<?php

declare(strict_types=1);

namespace App\Domains\Shared\Data\Response;

use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;

class ResponseFormData extends Data
{
    public function __construct(
        #[WithCast(EnumCast::class, type: FormModeType::class)]
        public FormModeType $mode,
        public string|int|null $key,
        public ?string $key_type,
        public ?string $key_val_type,
        public ?array $data,
    ) {}

    public static function forCreate(): self
    {
        return new self(
            mode: FormModeType::CREATE,
            key: null,
            key_type: null,
            key_val_type: null,
            data: null,
        );
    }

    public static function forPrefixed(array $data, string $id): self
    {
        return new self(
            mode: FormModeType::MANAGE,
            key: $id,
            key_type: 'generated',
            key_val_type: 'string',
            data: $data,
        );
    }

    public static function forMissingManage(): self
    {
        return new self(
            mode: FormModeType::MANAGE,
            key: null,
            key_type: null,
            key_val_type: null,
            data: null,
        );
    }

    public static function forUnknown(): self
    {
        return new self(
            mode: FormModeType::UNKNOWN,
            key: null,
            key_type: null,
            key_val_type: null,
            data: null,
        );
    }

    public static function forId(array $data, int $id): self
    {
        return new self(
            mode: FormModeType::MANAGE,
            key: $id,
            key_type: 'sequence',
            key_val_type: 'number',
            data: $data,
        );
    }
}
