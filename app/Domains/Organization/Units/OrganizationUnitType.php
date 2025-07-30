<?php

declare(strict_types=1);

namespace App\Domains\Organization\Units;

use ArchTech\Enums\From;
use ArchTech\Enums\Options;
use ArchTech\Enums\Values;

enum OrganizationUnitType
{
    use From, Options, Values;

    case DIVISION;

    case DEPARTMENT;
    case BRANCH;
    case SECTION;
    case UNIT;

    case TEAM;

    public function label(): string
    {
        return match ($this) {
            self::DIVISION => 'Division',
            self::BRANCH => 'Branch',
            self::SECTION => 'Section',
            self::UNIT => 'Unit',
            self::DEPARTMENT => 'Department',
            self::TEAM => 'Team',
        };
    }

    public function defaultHierarchyLevel(): int
    {
        return match ($this) {
            self::DIVISION => 0,
            self::DEPARTMENT => 1,
            self::BRANCH => 2,
            self::SECTION => 3,
            self::UNIT => 4,
            self::TEAM => 5,
        };
    }

    public function allowedParentTypes(): array
    {
        return match ($this) {
            self::DIVISION => [],
            self::DEPARTMENT => [self::DIVISION],
            self::BRANCH => [self::DEPARTMENT],
            self::SECTION => [self::BRANCH],
            self::UNIT => [self::SECTION],
            self::TEAM => [self::UNIT, self::DEPARTMENT],
        };
    }

    public function isValidHierarchy(self $parentType, ?self $grandParentType = null): bool
    {
        // If parent is Department and current is Team, it's valid
        if (self::TEAM === $this && self::DEPARTMENT === $parentType) {
            return true;
        }

        // For all other cases, ensure linear hierarchy
        $allowedParents = $this->allowedParentTypes();
        if (! in_array($parentType, $allowedParents)) {
            return false;
        }

        // If it's not the Department->Team special case, enforce linear hierarchy
        return self::DEPARTMENT === $parentType ||
            [] === $parentType->allowedParentTypes() ||
            (null !== $grandParentType && in_array($grandParentType, $parentType->allowedParentTypes()));
    }

    public function isValidParentType(self $type): bool
    {
        return in_array($type, $this->allowedParentTypes());
    }
}
