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
     * @Route("/cache/temporary/{path}", name="media_temporary_cache", requirements={"path":".+"})
     */
    public function temporaryAction($path)
    {

    }

    /**
     * @Route("/private/{path}", name="media_private", requirements={"path":".+"})
     * @Route("/cache/private/{path}", name="media_private_cache", requirements={"path":".+"})
     */
    public function privateAction($path)
    {

    }

    /**
     * This should be only accessible in development environment.
     *
     * @Route("/picture/public-{path}", name="picture_public")
     * @Route("/cache/picture/public-{path}", name="picture_public_cache")
     */
    public function publicAction($path)
    {
        $name = sprintf('public-%s', $path);
        $response->headers->set('X-Sendfile', $realpath);

        return $response;
    }
}