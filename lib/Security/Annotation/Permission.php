<?php

declare(strict_types=1);

namespace KejawenLab\ApiSkeleton\Security\Annotation;

use Attribute;

/**
 * @Annotation()
 * @Target({"CLASS"})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class Permission
{
    public const ADD = 'add';

    public const EDIT = 'edit';

    public const VIEW = 'view';

    public const DELETE = 'delete';

    private readonly string $menu;

    private array $actions = [];

    private bool $ownership = false;

    public function __construct(string $menu, array $actions, bool $ownership = false)
    {
        $this->menu = $menu;
        $this->actions = $actions;
        $this->ownership = $ownership;
    }

    public function getMenu(): string
    {
        return $this->menu;
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function isOwnership(): bool
    {
        return $this->ownership;
    }
}
