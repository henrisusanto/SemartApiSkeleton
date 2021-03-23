<?php

declare(strict_types=1);

namespace KejawenLab\ApiSkeleton\Controller;

use KejawenLab\ApiSkeleton\Application\App;
use KejawenLab\ApiSkeleton\SemartApiSkeleton;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 */
final class HealthController extends AbstractController
{
    public function __invoke()
    {
        return new JsonResponse([
            'semart' => [
                'name' => 'Semart Api Skeleton',
                'version' => [
                    'codename' => SemartApiSkeleton::CODENAME,
                    'alias' => SemartApiSkeleton::VERSION,
                    'number' => SemartApiSkeleton::getVersionNumber(),
                ],
                'author' => 'https://github.com/KejawenLab',
                'maintainer' => 'https://github.com/ad3n',
            ],
            'app' => [
                'name' => $_SERVER['APP_TITLE'],
                'description' => $_SERVER['APP_DESCRIPTION'],
                'version' => [
                    'alias' => $_SERVER['APP_VERSION'],
                    'number' => App::getVersionNumber(),
                ],
            ],
        ]);
    }
}