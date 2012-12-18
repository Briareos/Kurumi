<?php

namespace Kurumi\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\DiExtraBundle\Annotation\Inject;

/**
 * @Route("/media")
 */
class MediaController extends Controller
{
    /**
     * @var \Kurumi\MainBundle\StreamWrapper\StreamWrapperManager
     *
     * @Inject("stream_wrapper.manager")
     */
    private $wrapperManager;

    /**
     * @var \Doctrine\ORM\EntityManager
     *
     * @Inject("doctrine.orm.entity_manager")
     */
    private $em;

    /**
     * @var \Symfony\Component\Templating\Helper\CoreAssetsHelper
     *
     * @Inject("templating.helper.assets")
     */
    private $assetsHelper;

    /**
     * @Route("/temporary/{path}", name="media_temporary", requirements={"path":".+"})
     * @Route("/cache/temporary/{path}", name="file_cache_temporary", requirements={"path":".+"})
     */
    public function temporaryAction($path)
    {

    }

    /**
     * @Route("/private/{path}", name="media_private", requirements={"path":".+"})
     * @Route("/cache/private/{path}", name="file_cache_private", requirements={"path":".+"})
     */
    public function privateAction($path)
    {

    }

    /**
     * @Route("/public/{path}", name="media_public", requirements={"path":".+"})
     * @Route("/cache/public/{path}", name="file_cache_public", requirements={"path":".+"})
     */
    public function publicAction($path)
    {
        $uri = sprintf('public://%s', $path);

        if (!file_exists($uri)) {
            throw $this->createNotFoundException();
        }

        $realpath = $this->wrapperManager->realpath($uri);
        $response = new Response();
        $response->headers->set('X-Sendfile', $realpath);

        return $response;

        $url = $this->wrapperManager->getExternalUrl($uri);
        $baseUrl = $this->getRequest()->getBaseUrl();
        // $url may not always an asset URL, as it might be something like /app_dev.php/media/asset.jpg,
        // in which case $baseUrl will be equal to /app_dev.php, so we need to strip it.
        if (strlen($baseUrl)) {
            $url = substr($url, strlen($baseUrl));
        }
        $response = $this->redirect($url, 301);

        return $response;
    }
}