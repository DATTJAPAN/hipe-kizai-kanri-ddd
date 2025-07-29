<?php

declare(strict_types=1);

namespace App\Support\Traits\Model;

use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

trait ModelExtension
{
    // ------------------------------------------------------------------------------
    // Model Helper Methods
    // ------------------------------------------------------------------------------
    public function readableClassName(bool $isSingular = false): string
    {
        if ($isSingular) {
            return Str::singular(class_basename($this));
        }

        return Str::plural(class_basename($this));
    }

    public function isNew(bool $strict = true): bool
    {
        $isStrictEmpty = $strict ? empty($this->getAttributes()) : ! empty($this->getAttributes());

        return ! $this->exists && $isStrictEmpty;
    }

    /**
     * Filter data attributes base on fillable defined in the model
     *
     * @return array Filtered attributes
     */
    public function filterFillableAttributes(array $attributes): array
    {
        return array_intersect_key($attributes, array_flip($this->getFillable()));
    }

    // ------------------------------------------------------------------------------
    // Model Useful Methods
    // ------------------------------------------------------------------------------
    /**
     * Get the model version base on update_at attribute
     *
     * @return int Returns the Unix timestamp representing the date else 0
     */
    public function modelVersion(): int
    {
        $baseOnAttribute = 'updated_at';

        $value = $this->{$baseOnAttribute};

        if ($value instanceof DateTimeInterface) {
            return $value->getTimestamp();
        }

        return 0;
    }

    /**
     * Compare this model's version against another version or instance of the same class.
     *
     * @param  int|self  $versionOrModel  Either a version timestamp or an instance of the same class
     * @param  bool  $detailComparison  If true, returns 'newer'|'outdated'|'match';
     *                                  if false, returns 'match'|'mismatch'
     * @return string The comparison result of the current against the target
     */
    public function compareModelVersion(int|self $versionOrModel, bool $detailComparison = false): string
    {
        $compareVersion = $versionOrModel instanceof self
            ? $versionOrModel->modelVersion()
            : $versionOrModel;

        if ($detailComparison) {
            if ($this->modelVersion() > $compareVersion) {
                return 'new';
            }

            if ($this->modelVersion() < $compareVersion) {
                return 'old';
            }

            return 'match';
        }

        return $this->modelVersion() === $compareVersion ? 'match' : 'mismatch';
    }

    // ------------------------------------------------------------------------------
    // Spatie Related Methods
    // ------------------------------------------------------------------------------
    /**
     * Expose the spatie generation of prefixed ID
     *
     * @return string|null Returns null if no trait for spatie prefixed
     */
    public function getGeneratedPrefixedId(): ?string
    {
        if (method_exists($this, 'generatePrefixedId')) {
            return $this->generatePrefixedId();
        }

        return null;
    }

    public function isUsingSoftDeletes(): bool
    {
        $targetClass = SoftDeletes::class;

        return in_array($targetClass, class_uses_recursive($this), true);
    }
}
