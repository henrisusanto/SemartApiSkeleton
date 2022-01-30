<?php

declare(strict_types=1);

namespace KejawenLab\ApiSkeleton\EventSubscriber;

use DateInterval;
use KejawenLab\ApiSkeleton\ApiClient\Model\ApiClientInterface;
use KejawenLab\ApiSkeleton\SemartApiSkeleton;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class ViewCacheSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly CacheItemPoolInterface $cache)
    {
    }

    public function serve(RequestEvent $event): void
    {
        if (!$this->cachable($event)) {
            return;
        }

        $cached = $this->getCache($event);
        if (!$cached instanceof Response) {
            return;
        }

        $event->setResponse($cached);
        $event->stopPropagation();
    }

    public function invalidate(RequestEvent $event): void
    {
        if ($this->cachable($event)) {
            return;
        }

        $deviceId = $this->getDeviceId($event);
        if (empty($deviceId)) {
            return;
        }

        $pool = $this->cache->getItem($deviceId);
        if (!$pool->isHit()) {
            return;
        }

        $keys = $pool->get();
        foreach ($keys as $key => $nothing) {
            $this->cache->deleteItem($key);
        }

        $this->cache->deleteItem($deviceId);
    }

    public function populate(ResponseEvent $event): void
    {
        if (!$this->cachable($event)) {
            return;
        }

        $this->setCache($event);
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => [
                ['serve', 125],
                ['invalidate', 17],
            ],
            ResponseEvent::class => [['populate', -27]],
        ];
    }

    private function setCache(ResponseEvent $event): void
    {
        $key = $this->getCacheKey($event);
        if (empty($key)) {
            return;
        }

        $cache = $this->cache->getItem($key);
        if ($cache->isHit()) {
            return;
        }

        $deviceId = $this->getDeviceId($event);
        if (empty($deviceId)) {
            return;
        }

        $response = $event->getResponse();
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            return;
        }

        $pool = $this->cache->getItem($deviceId);
        $keys = [];
        if ($pool->isHit()) {
            $keys = $pool->get();
        }

        $keys = array_merge($keys, [$key => $response->headers->get('Content-Type')]);

        $pool->set($keys);
        $pool->expiresAfter(new DateInterval(SemartApiSkeleton::STATIC_PAGE_CACHE_PERIOD));
        $this->cache->save($pool);

        $cache->set($event->getResponse()->getContent());
        $cache->expiresAfter(new DateInterval(SemartApiSkeleton::STATIC_PAGE_CACHE_PERIOD));
        $this->cache->save($cache);
    }

    private function getCache(KernelEvent $event): ?Response
    {
        $key = $this->getCacheKey($event);
        if (empty($key)) {
            return null;
        }

        $deviceId = $this->getDeviceId($event);
        if (empty($deviceId)) {
            return null;
        }

        $pool = $this->cache->getItem($deviceId);
        if (!$pool->isHit()) {
            return null;
        }

        $keys = $pool->get();
        if (!array_key_exists($key, $keys)) {
            return null;
        }

        $item = $this->cache->getItem($key);
        if (!$item->isHit()) {
            return null;
        }

        $content = $item->get();
        if (!is_string($content)) {
            return null;
        }

        $response = new Response($content);
        $response->headers->set('Content-Type', $keys[$key]);
        $response->headers->set(SemartApiSkeleton::STATIC_CACHE_HEADER, $key);

        return $response;
    }

    private function getCacheKey(KernelEvent $event): string
    {
        $request = $event->getRequest();
        $session = $request->getSession();
        $flashes = $session->getFlashBag()->get('id');
        $id = '';
        foreach ($flashes as $flash) {
            $id = $flash;

            break;
        }

        if (!empty($id)) {
            $session->getFlashBag()->set('id', $id);

            return '';
        }

        return sprintf('%s_%s_%s', $this->getDeviceId($event), sha1($request->getPathInfo()), sha1(serialize($request->query->all())));
    }

    private function getDeviceId(KernelEvent $event): string
    {
        $deviceId = $event->getRequest()->getSession()->get(SemartApiSkeleton::USER_DEVICE_ID, '');
        if ($deviceId === ApiClientInterface::DEVICE_ID) {
            return '';
        }

        return $deviceId;
    }

    private function cachable(KernelEvent $event): bool
    {
        if (!$event->isMainRequest()) {
            return false;
        }

        $request = $event->getRequest();
        if (!empty($request->query->get(SemartApiSkeleton::DISABLE_PAGE_CACHE_QUERY_STRING))) {
            return false;
        }

        if ($request->isMethod(Request::METHOD_GET)) {
            return true;
        }

        if ($request->isMethod(Request::METHOD_HEAD)) {
            return true;
        }

        return $request->isMethod(Request::METHOD_OPTIONS);
    }
}