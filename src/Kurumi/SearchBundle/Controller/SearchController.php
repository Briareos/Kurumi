<?php

namespace Kurumi\SearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\DiExtraBundle\Annotation as DI;
use Briareos\AjaxBundle\Ajax;
use Kurumi\SearchBundle\Form\Type\SearchPeopleFormType;

class SearchController extends Controller
{

    /**
     * @DI\Inject("twig")
     *
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @DI\Inject("doctrine.orm.default_entity_manager")
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @DI\Inject("user_picture_provider")
     *
     * @var \Kurumi\UserBundle\User\PictureProvider
     */
    private $userPictureProvider;

    /**
     * @Route("/search", name="search")
     */
    public function searchAction()
    {
        /** @var $user \Kurumi\UserBundle\Entity\User */
        $user = $this->getUser();

        $searchPeopleForm = $this->createForm(new SearchPeopleFormType());

        /** @var $profileRepository \Kurumi\UserBundle\Entity\ProfileRepository */
        $profileRepository = $this->em->getRepository('UserBundle:Profile');
        $profiles = $profileRepository->getNearestProfiles($user->getProfile());

        /** @var $nodejsAuthenticator \Briareos\NodejsBundle\Nodejs\Authenticator */
        $nodejsAuthenticator = $this->get('nodejs.authenticator');
        $nodejsAuthenticator->authenticate($this->getRequest()->getSession(), $user);
        $templateFile = 'SearchBundle:Search:search_page.html.twig';
        $templateParams = array(
            'user' => $user,
            'nodejs_auth_token' => $nodejsAuthenticator->generateAuthToken($this->getRequest()->getSession(), $user),
            'profiles' => $profiles,
            'form' => $searchPeopleForm->createView(),
            'user_picture' => $this->userPictureProvider,
        );
        if ($this->getRequest()->isXmlHttpRequest()) {
            $commands = array();
            $template = $this->twig->loadTemplate($templateFile);
            $commands[] = new Ajax\Command\Page($template->renderBlock('title', $templateParams + $this->twig->getGlobals()), $template->renderBlock('body', $templateParams + $this->twig->getGlobals()));
            return new Ajax\Response($commands);
        } else {
            return $this->render($templateFile, $templateParams);
        }
    }
}