<?php

declare(strict_types=1);

namespace Alpabit\ApiSkeleton\Controller\Cron;

use Alpabit\ApiSkeleton\Cron\CronService;
use Alpabit\ApiSkeleton\Cron\Model\CronInterface;
use Alpabit\ApiSkeleton\Entity\Cron;
use Alpabit\ApiSkeleton\Form\FormFactory;
use Alpabit\ApiSkeleton\Form\Type\CronType;
use Alpabit\ApiSkeleton\Security\Annotation\Permission;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Psr\Log\LoggerInterface;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Permission(menu="CRON", actions={Permission::ADD})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@alpabit.com>
*/
final class Post extends AbstractFOSRestController
{
    private FormFactory $formFactory;

    private CronService $service;

    private LoggerInterface $logger;

    public function __construct(FormFactory $formFactory, CronService $service, LoggerInterface $auditLogger)
    {
        $this->formFactory = $formFactory;
        $this->service = $service;
        $this->logger = $auditLogger;
    }

    /**
    * @Rest\Post("/cronjobs")
    *
    * @SWG\Tag(name="Cron")
    * @SWG\Parameter(
    *     name="cron",
    *     in="body",
    *     type="object",
    *     description="Cron form",
    *     @Model(type=CronType::class)
    * )
    * @SWG\Response(
    *     response=201,
    *     description="Crate new cron",
    *     @SWG\Schema(
    *         type="object",
    *         ref=@Model(type=Cron::class, groups={"read"})
    *     )
    * )
    *
    * @Security(name="Bearer")
    *
    * @param Request $request
    *
    * @return View
    */
    public function __invoke(Request $request): View
    {
        $form = $this->formFactory->submitRequest(CronType::class, $request);
        if (!$form->isValid()) {
            return $this->view((array) $form->getErrors(), Response::HTTP_BAD_REQUEST);
        }

        /** @var CronInterface $cron */
        $cron = $form->getData();
        $this->service->save($cron);

        $this->logger->info(sprintf('[%s][%s][%s]', $this->getUser()->getUsername(), __CLASS__, $request->getContent()));

        return $this->view($this->service->get($cron->getId()), Response::HTTP_CREATED);
    }
}
