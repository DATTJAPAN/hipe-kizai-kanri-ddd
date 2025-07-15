<?php

declare(strict_types=1);

namespace App\Domains\Shared\Domains\Authorization;

use App\Domains\Shared\Domains\Authorization\Concerns\HasPermissions;
use App\Support\Traits\Model\ModelExtension;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Spatie\PrefixedIds\Models\Concerns\HasPrefixedId;

class Permission extends Model
{
    use HasPrefixedId;
    use ModelExtension;

    protected $table = 'permissions';

    protected $fillable = [
        'name',
        'guard_name',
        'target_class',
    ];

    public function extractPermissionsFromClass(string $class): array
    {
        $isValidClass = checkClassTraits($class, HasPermissions::class)
            && method_exists($class, 'getDefinedPermissionList');

        if (! $isValidClass) {
            return [];
        }

        return Arr::flatten((new $class)->getDefinedPermissionList());
    }

    public function generateDefaultPermissions(): void
    {
        $classes = config('role_permission.permission.class_with_permissions');

        foreach ($classes as $class) {
            // extract permissions from a class
            $validClassPermissions = $this->extractPermissionsFromClass($class);

            foreach ($validClassPermissions as $permission) {
                self::create([
                    'name' => $permission,
                    'display_name' => $permission,
                    'is_app_default' => true, // Indicate as permanent
                    'target_class' => is_object($class) ? get_class($class) : $class,
                ]);
            }
        }
    }

    public function syncDefaultPermissions(): void
    {
        $classes = config('role_permission.permission.class_with_permissions');

        foreach ($classes as $class) {
            $className = is_object($class) ? get_class($class) : $class;
            // extract permissions from a class
            $validClassPermissions = $this->extractPermissionsFromClass($class);

            // get the class-related permissions in the database for comparison
            $currentPermsForClass = self::select('name')
                ->where('target_class', $className)
                ->get()
                ->pluck('name')
                ->toArray();

            $permissionsToAdd = array_diff($validClassPermissions, $currentPermsForClass);
            $permissionsToRemove = array_diff($currentPermsForClass, $validClassPermissions);

            foreach ($permissionsToAdd as $permission) {
                self::create([
                    'name' => $permission,
                    'display_name' => $permission,
                    'is_app_default' => true,
                    'target_class' => $className,
                ]);
            }

            if (! empty($permissionsToRemove)) {
                self::where('guard_name', 'web')
                    ->where('target_class', $className)
                    ->whereIn('name', $permissionsToRemove)
                    ->delete();
            }
        }
    }
}
