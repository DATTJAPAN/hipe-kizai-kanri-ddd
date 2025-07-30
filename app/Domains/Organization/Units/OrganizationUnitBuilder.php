<?php

declare(strict_types=1);

namespace App\Domains\Organization\Units;

use App\Domains\Organization\Users\OrganizationUser;
use App\Domains\Shared\Models\Organization;
use App\Domains\Shared\Models\OrganizationUnit;

class OrganizationUnitBuilder
{
    private bool $strictHierarchy = false;

    private string $name;

    private string $code;

    private string $description = 'Provide a description';

    private OrganizationUnitType $currentType;

    private int $hierarchy = 99;

    private bool $active = true;

    private ?OrganizationUnit $supposedParent = null;

    private ?OrganizationUser $headOrgUser = null;

    private OrganizationUser $creatorOrgUser;

    private Organization $organization;

    public function __construct()
    {
        $this->code = mb_strtoupper(str()->random(6));
    }

    public function setStrictHierarchy(bool $strictHierarchy = false): self
    {
        $this->strictHierarchy = $strictHierarchy;

        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function setType(OrganizationUnitType $type): self
    {
        $this->currentType = $type;
        $this->hierarchy = $type->defaultHierarchyLevel();

        return $this;
    }

    public function setHasParentUnit(OrganizationUnit $supposedParent): self
    {
        $this->supposedParent = $supposedParent;

        return $this;
    }

    public function setHeadOrgUser(OrganizationUser $headOrgUser): self
    {
        $this->headOrgUser = $headOrgUser;

        return $this;
    }

    public function setCreatorOrgUser(OrganizationUser $creatorOrgUser): self
    {
        $this->creatorOrgUser = $creatorOrgUser;

        return $this;
    }

    public function setOrganization(Organization $organization): self
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * @throws OrganizationUnitBuilderException
     */
    public function build(): OrganizationUnit
    {
        $attributes = $this->prepareAttributes();
        $this->validateParentRelationship($attributes);

        return new OrganizationUnit(attributes: $attributes);
    }

    private function prepareAttributes(): array
    {
        return [
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'type' => $this->currentType,
            'is_active' => $this->active,
            'hierarchy' => $this->hierarchy,
            'is_strict_hierarchy' => $this->strictHierarchy,
            'head_org_user_id' => $this->headOrgUser?->id,
            'org_id' => $this->organization->id,
            'creator_org_user_id' => $this->creatorOrgUser->id,
            'parent_unit_id' => null,
        ];
    }

    private function validateParentRelationship(array &$attributes): void
    {
        if (! $this->supposedParent) {
            return;
        }

        if (OrganizationUnitType::DIVISION === $this->currentType) {
            throw OrganizationUnitBuilderException::doesntNeedParent();
        }

        // if the child is not strict but the parent is
        if ($this->supposedParent->isStrictMode() && ! $this->strictHierarchy) {
            throw OrganizationUnitBuilderException::parentInStrictButChildIsNot();
        }

        $parentType = $this->getParentType();

        // if the child is strict
        if ($this->strictHierarchy) {
            if (! $this->supposedParent->isStrictMode()) {
                throw OrganizationUnitBuilderException::parentIsNotOnStrictMode();
            }

            if (! $this->currentType->isValidHierarchy($parentType)) {
                throw OrganizationUnitBuilderException::parentHierarchyIsInvalid();
            }
        }

        $attributes['parent_unit_id'] = $this->supposedParent->id;
    }

    private function getParentType(): OrganizationUnitType
    {
        $parentType = $this->supposedParent->type;

        if (! $parentType instanceof OrganizationUnitType) {
            $parentType = OrganizationUnitType::tryFrom($parentType);

            if (null === $parentType) {
                throw OrganizationUnitBuilderException::parentTypeIsInvalid();
            }
        }

        return $parentType;
    }
}
