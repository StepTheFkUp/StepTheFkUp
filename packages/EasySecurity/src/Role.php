<?php
declare(strict_types=1);

namespace EonX\EasySecurity;

use EonX\EasySecurity\Interfaces\PermissionInterface;
use EonX\EasySecurity\Interfaces\RoleInterface;

final class Role implements RoleInterface
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @var mixed[]
     */
    private $metadata;

    /**
     * @var null|string
     */
    private $name;

    /**
     * @var \EonX\EasySecurity\Interfaces\PermissionInterface[]
     */
    private $permissions;

    /**
     * Role constructor.
     *
     * @param string $identifier
     * @param \EonX\EasySecurity\Interfaces\PermissionInterface[] $permissions
     * @param null|string $name
     * @param null|mixed[] $metadata
     */
    public function __construct(string $identifier, array $permissions, ?string $name = null, ?array $metadata = null)
    {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->metadata = $metadata ?? [];

        $this->setPermissions($permissions);
    }

    /**
     * Get string representation of role.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->identifier;
    }

    /**
     * Get identifier.
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Get metadata.
     *
     * @return mixed[]
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * Get name.
     *
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Get permissions.
     *
     * @return \EonX\EasySecurity\Interfaces\PermissionInterface[]
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * Set permissions.
     *
     * @param mixed[] $permissions
     *
     * @return void
     */
    private function setPermissions(array $permissions): void
    {
        // Accept only either permission instances or string to be converted
        $filterPermissions = static function ($permission): bool {
            return $permission instanceof PermissionInterface || \is_string($permission);
        };

        // Convert string to permissions
        $mapPermissions = static function ($permission): PermissionInterface {
            return $permission instanceof PermissionInterface ? $permission : new Permission($permission);
        };

        $this->permissions = \array_map($mapPermissions, \array_filter($permissions, $filterPermissions));
    }
}
