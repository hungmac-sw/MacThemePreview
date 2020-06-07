<?php declare(strict_types=1);

namespace MacThemePreview\Framework\Cookie;

use Shopware\Storefront\Framework\Cookie\CookieProviderInterface;

class CustomCookieProvider implements CookieProviderInterface {

    private $originalService;

    function __construct(CookieProviderInterface $service)
    {
        $this->originalService = $service;
    }

    public function getCookieGroups(): array
    {
        return array_merge(
            $this->originalService->getCookieGroups(),
            [
                [
                    'snippet_name' => 'cookie.theme',
                    'cookie' => 'preview-theme-id',
                ]
            ]
        );
    }
}
