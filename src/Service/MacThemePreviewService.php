<?php declare(strict_types=1);

namespace MacThemePreview\Service;

use League\Flysystem\FileNotFoundException;
use Shopware\Core\Framework\Context;
use League\Flysystem\FilesystemInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Storefront\Theme\Exception\InvalidThemeException;
use Shopware\Storefront\Theme\ThemeEntity;
use Shopware\Storefront\Theme\ThemeService;

class MacThemePreviewService
{
    /**
     * @var ThemeService
     */
    private $themeService;

    /**
     * @var FilesystemInterface
     */
    private $publicFilesystem;

    /**
     * @var EntityRepositoryInterface
     */
    private $themeRepository;

    public function __construct(
        ThemeService $themeService,
        FilesystemInterface $publicFilesystem,
        EntityRepositoryInterface $themeRepository
    ) {
        $this->themeService = $themeService;
        $this->publicFilesystem = $publicFilesystem;
        $this->themeRepository = $themeRepository;
    }

    public function themeCompile(Context $context, string $salesChannelId, string $themeId): void
    {
        $criteria = new Criteria([$themeId]);
        $criteria->addAssociation('salesChannels');

        /** @var ThemeEntity $theme */
        $theme = $this->themeRepository->search($criteria, $context)->get($themeId);
        if (!$theme) {
            throw new InvalidThemeException($themeId);
        }

        if ($this->isNeedCompile($theme, $salesChannelId)) {
            $this->themeService->compileTheme($salesChannelId, $themeId, $context);
        }
    }

    private function isNeedCompile(ThemeEntity $theme, string $salesChannelId): bool
    {
        $themePrefix = md5($theme->getId() . $salesChannelId);
        $outputPath = 'theme' . DIRECTORY_SEPARATOR . $themePrefix;
        $scriptFilepath = $outputPath . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'all.js';

        try {
            if (empty($theme->getUpdatedAt()) && !$this->publicFilesystem->has($scriptFilepath)) {
                return true;
            }

            if ($theme->getUpdatedAt()) {
                $compliedTime = $this->publicFilesystem->getTimestamp($scriptFilepath);
                if ($compliedTime < $theme->getUpdatedAt()->getTimestamp()) {
                    return true;
                }
            }
        } catch (FileNotFoundException $e) {
            return true;
        }

        return false;
    }
}
