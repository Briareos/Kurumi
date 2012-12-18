<?php

namespace Kurumi\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\DiExtraBundle\Annotation as Di;

class SitemapController extends Controller
{
    /**
     * @DI\Inject("doctrine.orm.default_entity_manager")
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @DI\Inject("router")
     *
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    private $router;

    /**
     * @Route("/user-sitemap.xml")
     */
    public function userSitemapAction()
    {
        /** @var $users \Kurumi\MainBundle\Entity\User[] */
        $users = $this->em->getRepository('KurumiMainBundle:User')->findAll();

        $document = new \DOMDocument('1.0', 'UTF-8');
        $xml = $document->createElement('xml');
        $xml->setAttribute('version', '1.0');
        $xml->setAttribute('encoding', 'UTF-8');
        $urlSet = $document->createElement('urlset');
        $urlSet->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $document->appendChild($xml);
        $xml->appendChild($urlSet);

        // Add the front page
        $url = $document->createElement('url');
        $url->appendChild($document->createElement('loc', $this->router->generate('front', array(), true)));
        $url->appendChild($document->createElement('lastmod', (new \DateTime())->format('Y-m-d')));
        $url->appendChild($document->createElement('changefreq', 'hourly'));
        $url->appendChild($document->createElement('priority', '1.0'));
        $urlSet->appendChild($url);

        foreach ($users as $i => $user) {
            if ($i > 100) break;
            $url = $document->createElement('url');
            $url->appendChild($document->createElement('loc', $this->router->generate('profile', array('id' => $user->getId()), true)));
            $url->appendChild($document->createElement('lastmod', (new \DateTime())->format('Y-m-d')));
            $url->appendChild($document->createElement('changefreq', 'monthly'));
            $url->appendChild($document->createElement('priority', '0.5'));
            $urlSet->appendChild($url);
        }

        $response = new Response($document->saveHTML(), 200, array(
            'Content-Type' => 'application/xml',
        ));

        return $response;
    }
}