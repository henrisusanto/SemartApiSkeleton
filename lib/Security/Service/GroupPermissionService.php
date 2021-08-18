<?php

declare(strict_types=1);

namespace KejawenLab\ApiSkeleton\Security\Service;

use KejawenLab\ApiSkeleton\Security\Model\GroupInterface;
use KejawenLab\ApiSkeleton\Security\Model\MenuRepositoryInterface;
use KejawenLab\ApiSkeleton\Security\Model\PermissionableInterface;
use KejawenLab\ApiSkeleton\Security\Model\PermissionInitiatorInterface;
use KejawenLab\ApiSkeleton\Security\Model\PermissionRemoverInterface;
use KejawenLab\ApiSkeleton\Security\Model\PermissionRepositoryInterface;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class GroupPermissionService implements PermissionInitiatorInterface, PermissionRemoverInterface
{
    private string $class;

    public function __construct(private MenuRepositoryInterface $menuRepository, private PermissionRepositoryInterface $permissionRepository)
    {
    }

    /**
     * @param GroupInterface $object
     */
    public function initiate(PermissionableInterface $object): void
    {
        foreach ($this->menuRepository->findAll() as $menu) {
            $permission = $this->permissionRepository->findPermission($object, $menu);
            if ($permission === null) {
                $permission = new $this->class();
            }

            $permission->setMenu($menu);
            $permission->setGroup($object);

            $this->permissionRepository->persist($permission);
        }
    }

    /**
     * @param GroupInterface $object
     */
    public function remove(PermissionableInterface $object): void
    {
        $this->permissionRepository->removeByGroup($object);
    }

    public function support(PermissionableInterface $object): bool
    {
        return $object instanceof GroupInterface;
    }

    public function setClass(string $class): void
    {
        $this->class = $class;
    }
}
