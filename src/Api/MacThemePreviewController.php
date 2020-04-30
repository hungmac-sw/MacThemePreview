<?php declare(strict_types=1);

namespace MacThemePreview\Api;

use MacThemePreview\Service\MacThemePreviewService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @RouteScope(scopes={"api"})
 */
class MacThemePreviewController extends AbstractController
{
    /**
     * @var MacThemePreviewService
     */
    private $macThemePreviewService;

    public function __construct(MacThemePreviewService $macThemePreviewService)
    {
        $this->macThemePreviewService = $macThemePreviewService;
    }

    /**
     * @Route("/api/v{version}/_action/mac-theme-preview/compile", name="api.action.mac_theme_preview.compile", methods={"POST"})
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function themeCompile(RequestDataBag $dataBag, Context $context): Response
    {
        $salesChannelId = $dataBag->get('sales_channel_id');
        $themeId = $dataBag->get('theme_id');

        $this->macThemePreviewService->themeCompile($context, $salesChannelId, $themeId);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
