<?php

declare(strict_types=1);

namespace App\Support\Traits\Model;

use InvalidArgumentException;
use UnexpectedValueException;

trait HasSettingsTrait
{
    public static function bootHasSettingsTrait(): void
    {
        static::created(static function ($model) {
            $relationName = $model->getSettingRelation();

            if (! method_exists($model, $relationName)) {
                throw new UnexpectedValueException('Relation method does not exist: '.$relationName);
            }

            $model->{$relationName}()->create([
                self::getDefaultSettingColumn() => self::getDefaultSettingValues(),
            ]);
        });
    }

    final protected static function getDefaultSettingColumn(): string
    {
        return config('defaults.settings.tbl_column');
    }

    final protected static function getDefaultSettingValues(): array
    {
        return config('defaults.settings.tbl_value');
    }

    final protected function getSettingRelation(): string
    {
        if (property_exists($this, 'setting_relation')) {
            return $this->setting_relation;
        }

        throw new InvalidArgumentException(
            '$setting_relation property is not defined in '.get_class($this)
        );
    }
}
