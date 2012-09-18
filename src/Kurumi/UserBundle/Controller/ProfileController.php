<?php

namespace Kurumi\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kurumi\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\DiExtraBundle\Annotation as DI;
use Briareos\AjaxBundle\Ajax;
use JMS\SecurityExtraBundle\Annotation\Secure;

class ProfileController extends Controller
{
    /**
     * @DI\Inject("templating.ajax")
     *
     * @var \Briareos\AjaxBundle\Twig\AjaxEngine
     */
    private $ajax;

    /**
     * @DI\Inject("templating.ajax.helper")
     *
     * @var \Briareos\AjaxBundle\Ajax\Helper
     */
    private $ajaxHelper;

    /**
     * @DI\Inject("router")
     *
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    private $router;

    /**
     * @Route("/profile/{id}", name="profile", defaults={"id"=null})
     * @ParamConverter("subject", class="UserBundle:User")
     * @Secure(roles="IS_AUTHENTICATED_REMEMBERED")
     */
    public function viewAction(User $subject = null)
    {
        /** @var $user User */
        $user = $this->getUser();
        if ($subject === null) {
            $subject = $user;
        }
        $ownProfile = $user->getId() === $subject->getId();

        $templateFile = 'UserBundle:Profile:view.html.twig';
        $templateParams = array(
            'user' => $user,
            'subject' => $subject,
            'own_profile' => $ownProfile,
        );

        if ($this->getRequest()->isXmlHttpRequest()) {
            return $this->ajaxHelper->renderPjaxBlock($templateFile, $templateParams, $this->router->generate('front'));
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
