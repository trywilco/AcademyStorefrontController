<?php declare(strict_types=1);

namespace StorefrontControllerPlugin\Storefront\Controller;

use GuzzleHttp\Client;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Shopware\Core\System\SystemConfig\SystemConfigService;

#[Route(defaults: ['_routeScope' => ['storefront'], 'XmlHttpRequest' => true])]
class ImageController extends StorefrontController
{
    private Client $client;
    private SystemConfigService $systemConfigService;

    public function __construct(Client $client, SystemConfigService $systemConfigService)
    {
        $this->client = $client;
        $this->systemConfigService = $systemConfigService;
    }

    #[Route(
        path: '/image',
        name: 'frontend.image.show',
        methods: ['GET']
    )]
    public function showImage(Request $request, SalesChannelContext $context): Response
    {
        $unsplashAccessKey = $this->systemConfigService->get('StorefrontControllerPlugin.config.unsplashAccessKey');

        $response = $this->client->request('GET', 'https://api.unsplash.com/photos/random', [
            'headers' => [
                'Authorization' => 'Client-ID ' . $unsplashAccessKey
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        $imageUrl = $data['urls']['regular'];

        return $this->renderStorefront('@StorefrontControllerPlugin/storefront/page/image.html.twig', [
            'imageUrl' => $imageUrl
        ]);
    }
}