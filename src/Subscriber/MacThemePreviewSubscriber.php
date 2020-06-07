<?php declare(strict_types=1);

namespace MacThemePreview\Subscriber;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Shopware\Core\SalesChannelRequest;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class MacThemePreviewSubscriber implements EventSubscriberInterface
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['setTheme', 40]
            ]
        ];
    }

    public function setTheme(RequestEvent $event) {
        $request = $event->getRequest();

        if (!$request->cookies->has('preview-theme-id')) {
            return;
        }

        $themeId = $request->cookies->get('preview-theme-id');
        $theme = $this->findTheme($themeId);
        if (empty($theme)) {
            return;
        }

        $request->attributes->set(SalesChannelRequest::ATTRIBUTE_THEME_ID, $themeId);
        $request->attributes->set(SalesChannelRequest::ATTRIBUTE_THEME_NAME, $theme['themeName']);
    }

    private function findTheme($themeId) {
        /** @var Statement $statement */
        $statement = $this->connection->createQueryBuilder()
            ->select(
                [
                    'LOWER(HEX(theme.id)) themeId',
                    'theme.technical_name as themeName'
                ]
            )->from('theme')
            ->where('theme.id = UNHEX(:themeId)')
            ->setParameter('themeId', $themeId)
            ->execute();

        return $statement->fetch();
    }
}
