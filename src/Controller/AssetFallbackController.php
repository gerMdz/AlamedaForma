<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class AssetFallbackController extends AbstractController
{
    /**
     * Temporary fallback to serve the FORMA logo while Asset Mapper is not exposing it at /assets/images/...
     * This serves only the specific file logo-con-nombre.png to satisfy social meta preview and direct access.
     */
    #[Route('/assets/images/logo-con-nombre.png', name: 'asset_logo_con_nombre', methods: ['GET'])]
    public function serveLogo(): Response
    {
        $projectDir = $this->getParameter('kernel.project_dir');
        $path = $projectDir . '/assets/images/logo-con-nombre.png';

        if (!is_file($path)) {
            return new Response('Asset not found', Response::HTTP_NOT_FOUND);
        }

        $response = new BinaryFileResponse($path);
        $response->headers->set('Content-Type', 'image/png');
        // Allow caching by browsers and scrapers
        $response->setPublic();
        $response->setMaxAge(60 * 60 * 24 * 7); // 7 days
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, 'logo-con-nombre.png');

        return $response;
    }
}
