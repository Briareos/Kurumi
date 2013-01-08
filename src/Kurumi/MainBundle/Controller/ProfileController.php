<?php

namespace Kurumi\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Kurumi\MainBundle\Entity\User;
use Kurumi\MainBundle\Form\Type\ProfilePhotoFormType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Kurumi\MainBundle\Entity\Profile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use JMS\DiExtraBundle\Annotation\Inject;
use Briareos\AjaxBundle\Ajax;
use Kurumi\MainBundle\Entity\Picture;

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
        $templateParams = array(
            'profile' => $profile,
            'own_profile' => $ownProfile,
            'pjax_container' => $pjaxContainer,
            'timeline' => $timeline,
        );

        $url = $this->router->generate('profile', array('id' => $profile->getId()));

        if ($this->getRequest()->isXmlHttpRequest()) {

            return $this->ajaxHelper->renderPjaxBlock($templateFile, $templateParams, $url, $pjaxContainer);
        } else {
            return $this->render($templateFile, $templateParams);
        }
    }

    /**
     * @Route("/profile/{id}/photo", name="profile_photos")
     * @ParamConverter("profile", class="KurumiMainBundle:Profile")
     * @SecureParam(name="profile", permissions="VIEW")
     */
    public function photosAction(Profile $profile)
    {
        /** @var $user User */
        $user = $this->getUser();

        $ownProfile = $profile->isUser($user);

        if (!$ownProfile && !$this->profileInfo->hasPhotos($profile)) {
            // This user has no photos and it's not the current user's profile
            $redirect = $this->generateUrl('profile', array('id' => $profile->getId()));

            return $this->redirect($redirect);
        }

        $pjaxContainer = sprintf('profile_page-%s', $profile->getId());
        $templateFile = ':Profile:photos.html.twig';
        $templateParams = array(
            'profile' => $profile,
            'own_profile' => $ownProfile,
            'pjax_container' => $pjaxContainer,
        );

        if ($this->getRequest()->isXmlHttpRequest()) {
            $url = $this->router->generate('profile_photos', array('id' => $profile->getId()));

            return $this->ajaxHelper->renderPjaxBlock($templateFile, $templateParams, $url, $pjaxContainer);
        } else {
            return $this->render($templateFile, $templateParams);
        }
    }

    /**
     * @Route("/profile/{profile_id}/photo/{id}", name="profile_photo")
     * @ParamConverter("profile", class="KurumiMainBundle:Profile", options={"id" = "profile_id"})
     * @ParamConverter("picture", class="KurumiMainBundle:Picture")
     * @SecureParam(name="picture", permissions="VIEW")
     */
    public function photoAction(Profile $profile, Picture $picture, Request $request)
    {
        if ($profile->getId() !== $picture->getProfile()->getId()) {
            throw $this->createNotFoundException();
        }

        /** @var $user User */
        $user = $this->getUser();

        $ownProfile = $profile->isUser($user);

        $templateFile = ':Profile:photo.html.twig';
        $modalTemplateFile = ':Profile:photo_modal.html.twig';
        $templateParams = array(
            'profile' => $profile,
            'own_profile' => $ownProfile,
            'picture' => $picture,
        );

        if ($request->isXmlHttpRequest()) {

            return $this->ajaxHelper->renderModal($modalTemplateFile, $templateParams);
        }
    }

    /**
     * @Route("/profile/{id}/photo/add/{type}", name="profile_photo_add", requirements={"gallery"="(profile|public|private)"})
     * @ParamConverter("profile", class="KurumiMainBundle:Profile")
     *
     * @param \Kurumi\MainBundle\Entity\User $profile
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function photoAddAction(User $profile = null, $type)
    {
        /** @var $user User */
        $user = $this->getUser();
        if ($profile === null) {
            $profile = $user;
        }
        $ownProfile = $user->getId() === $profile->getId();

        if (!$ownProfile) {
            throw new AccessDeniedException();
        }

        $photo = new Picture();
        switch ($type) {
            case 'profile':
                $gallery = $profile->getProfile()->getGalleryProfile();
                if ($gallery === null) {
                    $gallery = new Gallery();
                    $profile->getProfile()->setGalleryProfile($gallery);
                }
                break;
            case 'public':
                $gallery = $profile->getProfile()->getGalleryPublic();
                if ($gallery === null) {
                    $gallery = new Gallery();
                    $profile->getProfile()->setGalleryPublic($gallery);
                }
                break;
            case 'private':
                $gallery = $profile->getProfile()->getGalleryPrivate();
                if ($gallery === null) {
                    $gallery = new Gallery();
                    $profile->getProfile()->setGalleryPrivate($gallery);
                }
                break;
            default:
                throw new AccessDeniedException('Invalid type specified, must be either "profile", "public" or "private".');
        }
        $profilePhotoForm = $this->createForm(new ProfilePhotoFormType(), $photo);

        if ($this->getRequest()->isMethod('post')) {
            $profilePhotoForm->bind($this->getRequest());
            if ($profilePhotoForm->isValid()) {
            }
        }

        $templateFile = ':Profile:photo_add.html.twig';
        $templateParams = array(
            'user' => $user,
            'profile' => $profile,
            'own_profile' => $ownProfile,
            'form' => $profilePhotoForm->createView(),
        );

        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->query->getInt('modal', 0)) {
                $modalTemplateFile = ':Form:add_photo_modal_form.html.twig';
            }
        } else {
            return $this->render($templateFile, $templateParams);
        }
    }
}
