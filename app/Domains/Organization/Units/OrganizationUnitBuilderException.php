<?php

declare(strict_types=1);

namespace App\Domains\Organization\Units;

use Exception;

class OrganizationUnitBuilderException extends Exception
{
    public static function doesntNeedParent(): self
    {
        return new self('Division units cannot have a parent unit');
    }

    public static function parentInStrictButChildIsNot(): self
    {
        return new self('The Parent is in strict mode but the child is not');
    }

    public static function parentIsNotOnStrictMode(): self
    {
        return new self('Cannot set strict hierarchy when parent unit is not in strict mode');
    }

    public static function parentTypeIsInvalid(): self
    {
        return new self('Parent unit has an invalid organizational unit type');
    }

    public static function parentHierarchyIsInvalid(): self
    {
        return new self('Invalid organizational hierarchy: parent unit type does not match allowed hierarchy structure');
    }
}
