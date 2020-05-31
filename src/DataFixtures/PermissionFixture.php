<?php

declare(strict_types=1);

namespace Alpabit\ApiSkeleton\DataFixtures;

use Alpabit\ApiSkeleton\Entity\Permission;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@alpabit.com>
 */
class PermissionFixture extends AbstractFixture implements DependentFixtureInterface
{
    protected function createNew()
    {
        return new Permission();
    }

    protected function getReferenceKey(): string
    {
        return 'permission';
    }

    public function getDependencies()
    {
        return [
            GroupFixture::class,
            MenuFixture::class,
        ];
    }
}
