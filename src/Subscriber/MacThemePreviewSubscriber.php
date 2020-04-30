<?php declare(strict_types=1);

namespace MacThemePreview\Subscriber;

use Shopware\Core\SalesChannelRequest;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class MacThemePreviewSubscriber implements EventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onRequest',
        ];
    }

    public function onRequest(RequestEvent $event) {
        $request = $event->getRequest();
        if (!empty($request->get('preview-theme-id'))) {
            $themeId = $request->get('preview-theme-id');
            $request->attributes->set(SalesChannelRequest::ATTRIBUTE_THEME_ID, $themeId);
        }
    }
}