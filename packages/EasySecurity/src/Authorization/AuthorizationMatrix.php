<?php
declare(strict_types=1);

namespace EonX\EasySecurity\Authorization;

use EonX\EasySecurity\Authorization\Helpers\AuthorizationMatrixFormatter;
use EonX\EasySecurity\Interfaces\Authorization\AuthorizationMatrixInterface;

final class AuthorizationMatrix implements AuthorizationMatrixInterface
{
    /**
     * @var null|\EonX\EasySecurity\Interfaces\PermissionInterface[]
     */
    private $cachePermissions;

    /**
     * @var \EonX\EasySecurity\Interfaces\PermissionInterface[]
     */
    private $permissions = [];

    /**
     * @var \EonX\EasySecurity\Interfaces\RoleInterface[]
     */
    private $roles = [];

    public function __construct(array $roles, array $permissions)
    {
        foreach (AuthorizationMatrixFormatter::formatRoles($roles) as $role) {
            $this->roles[$role->getIdentifier()] = $role;
        }

        foreach (AuthorizationMatrixFormatter::formatPermissions($permissions) as $permission) {
            $this->permissions[$permission->getIdentifier()] = $permission;
        }
    }

    /**
     * @return \EonX\EasySecurity\Interfaces\PermissionInterface[]
     */
    public function getPermissions(): array
    {
        if ($this->cachePermissions !== null) {
            return $this->cachePermissions;
        }

        $permissions = [];

        foreach ($this->getRoles() as $role) {
            foreach ($role->getPermissions() as $permission) {
                $permissions[$permission->getIdentifier()] = $permission;
            }
        }

        foreach ($this->permissions as $permission) {
            $permissions[$permission->getIdentifier()] = $permission;
        }

        return $this->cachePermissions = $permissions;
    }

    /**
     * @param string[] $identifiers
     *
     * @return \EonX\EasySecurity\Interfaces\PermissionInterface[]
     */
    public function getPermissionsByIdentifiers(array $identifiers): array
    {
        $permissions = [];

        foreach ($this->getPermissions() as $identifier => $permission) {
            if (\in_array($identifier, $identifiers, true) === false) {
                continue;
            }

            $permissions[] = $permission;
        }

        return $permissions;
    }

    /**
     * @return \EonX\EasySecurity\Interfaces\RoleInterface[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param string[] $identifiers
     *
     * @return \EonX\EasySecurity\Interfaces\RoleInterface[]
     */
    public function getRolesByIdentifiers(array $identifiers): array
    {
        $roles = [];

        foreach ($this->getRoles() as $identifier => $role) {
            if (\in_array($identifier, $identifiers, true) === false) {
                continue;
            }

            $roles[] = $role;
        }

        return $roles;
    }

    public function isPermission(string $permission): bool
    {
        return isset($this->getPermissions()[$permission]);
    }

    public function isRole(string $role): bool
    {
        return isset($this->getRoles()[$role]);
    }
}
