<?php

namespace Kurumi\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kurumi\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\DiExtraBundle\Annotation as DI;
use Briareos\AjaxBundle\Ajax;
use JMS\SecurityExtraBundle\Annotation\Secure;

class FacebookController extends Controller
{
    /**
     * @DI\Inject("templating.ajax")
     *
     * @var \Briareos\AjaxBundle\Twig\AjaxEngine
     */
    private $ajax;

    /**
     * @DI\Inject("router")
     *
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    private $router;

    /**
     * @Route("/facebook/connect", name="facebook_connect")
     */
    public function viewAction(User $subject = null)
    {
        /** @var $user User */
        $user = $this->getUser();
        if ($subject === null) {
            $subject = $user;
        }
        $own = $user->getId() === $subject->getId();

        $templateFile = 'UserBundle:Profile:view.html.twig';
        $templateParams = array(
            'user' => $user,
            'subject' => $subject,
            'own' => $own,
        );

        if ($this->getRequest()->isXmlHttpRequest()) {
            $commands = new Ajax\CommandContainer();
            $commands->add(new Ajax\Command\Page($this->ajax->renderBlock($templateFile, 'title', $templateParams), $this->ajax->renderBlock($templateFile, 'body', $templateParams), $this->router->generate('front')));
            return new Ajax\Response($commands);
        } else {
            return $this->render($templateFile, $templateParams);
        }
    }

    /**
     * @Route("/profile/{id}/photos", name="profile_photos")
     * @ParamConverter("subject", class="UserBundle:User")
     * @Secure(roles="IS_AUTHENTICATED_REMEMBERED")
     */
    public function photosAction(User $subject = null)
    {

    }
}
