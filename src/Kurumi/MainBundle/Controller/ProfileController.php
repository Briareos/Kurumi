<?php

namespace Kurumi\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Kurumi\MainBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Kurumi\MainBundle\Entity\Profile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use JMS\DiExtraBundle\Annotation\Inject;
use Briareos\AjaxBundle\Ajax;

class ProfileController extends Controller
{
    /**
     * @var \Briareos\AjaxBundle\Ajax\Helper
     *
     * @Inject("templating.ajax.helper")
     */
    private $ajaxHelper;

    /**
     * @var \Doctrine\ORM\EntityManager
     *
     * @Inject("doctrine.orm.default_entity_manager")
     */
    private $em;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     *
     * @Inject("router")
     */
    private $router;

    /**
     * @var \Kurumi\MainBundle\InfoProvider\ProfileInfoProvider
     *
     * @Inject("profile.info.provider")
     */
    private $profileInfo;

    /**
     * @var \Spy\Timeline\Driver\ActionManagerInterface
     *
     * @Inject("spy_timeline.action_manager")
     */
    private $actionManager;

    /**
     * @var \Spy\Timeline\Driver\TimelineManagerInterface
     *
     * @Inject("spy_timeline.timeline_manager")
     */
    private $timelineManager;

    /**
     * @Route("/profile/{id}", name="profile")
     * @ParamConverter("profile", class="KurumiMainBundle:Profile")
     * @SecureParam(name="profile", permissions="VIEW")
     */
    public function viewAction(Profile $profile)
    {
        $user = $this->getUser();
        $ownProfile = $profile->isUser($user);

        $subject = $this->actionManager->findOrCreateComponent($profile);
        $timeline = $this->timelineManager->getTimeline($subject);

        $pjaxContainer = sprintf('profile_page-%s', $profile->getId());
        $templateFile = ':Profile:view.html.twig';
        $templateParams = [
            'profile' => $profile,
            'own_profile' => $ownProfile,
            'pjax_container' => $pjaxContainer,
            'timeline' => $timeline,
        ];

        $url = $this->router->generate('profile', ['id' => $profile->getId()]);

        if ($this->getRequest()->isXmlHttpRequest()) {
            $ajaxResponse = $this->ajaxHelper->renderPjaxBlock($templateFile, $templateParams, $url, $pjaxContainer);
            $ajaxResponse->getContent()->add(new Ajax\Command\ModalClose());
            return $ajaxResponse;
        } else {
            return $this->render($templateFile, $templateParams);
        }
    }

    /**
     * @Route("/profile/{id}/pictures", name="profile_pictures")
     * @ParamConverter("profile", class="KurumiMainBundle:Profile")
     * @SecureParam(name="profile", permissions="VIEW")
     */
    public function picturesAction(Profile $profile)
    {
        /** @var $user User */
        $user = $this->getUser();

        $ownProfile = $profile->isUser($user);

        if (!$ownProfile && !$this->profileInfo->hasPictures($profile)) {
            // This user has no photos and it's not the current user's profile
            $redirect = $this->generateUrl('profile', ['id' => $profile->getId()]);

            return $this->redirect($redirect);
        }

        $pjaxContainer = sprintf('profile_page-%s', $profile->getId());
        $templateFile = ':Profile:pictures.html.twig';
        $templateParams = [
            'profile' => $profile,
            'own_profile' => $ownProfile,
            'pjax_container' => $pjaxContainer,
        ];

        if ($this->getRequest()->isXmlHttpRequest()) {
            $url = $this->router->generate('profile_pictures', ['id' => $profile->getId()]);

            return $this->ajaxHelper->renderPjaxBlock($templateFile, $templateParams, $url, $pjaxContainer);
        } else {
            return $this->render($templateFile, $templateParams);
        }
    }
}
