<?php declare(strict_types=1);

namespace ShopwareAcademy\StorefrontController\Storefront\Controller;

use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Response;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route(defaults: ['_routeScope' => ['storefront'], 'XmlHttpRequest' => true])]
class ImageController extends StorefrontController
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly SystemConfigService $systemConfigService
    )
    {
    }

    #[Route(
        path: '/image',
        name: 'frontend.image.show',
        methods: ['GET']
    )]
    public function showImage(SalesChannelContext $context): Response
    {
        $apiAccessKey = $this->systemConfigService->get('AcademyStorefrontController.config.apiAccessKey', $context->getSalesChannelId());
        $apiProvider = $this->systemConfigService->get('AcademyStorefrontController.config.apiProvider', $context->getSalesChannelId());

        $response = $this->client->request('GET', 'https://api.'.$apiProvider.'.com/v1/images/search', [
            'headers' => [
                'x-api-key' => $apiAccessKey
            ]
        ]);

        $data = $response->toArray();
        $imageUrl = $data[0]['url'];

        return $this->renderStorefront('@AcademyStorefrontController/storefront/page/image.html.twig', [
            'imageUrl' => $imageUrl
        ]);
    }
}
