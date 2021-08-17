<?php

declare(strict_types=1);

namespace KejawenLab\ApiSkeleton;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class SemartApiSkeleton
{
    public const CODENAME = 'Merdeka';

    public const VERSION = '5.5.1';

    public const VERSION_MAYOR = 50000;

    public const VERSION_MINOR = 500;

    public const VERSION_PATCH = 1;

    public static function getVersionNumber(): int
    {
        return self::VERSION_MAYOR + self::VERSION_MINOR + self::VERSION_PATCH;
    }
}
