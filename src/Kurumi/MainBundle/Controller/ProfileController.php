<?php

namespace Kurumi\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kurumi\MainBundle\Form\Type\ProfilePhotoFormType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Kurumi\MainBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\DiExtraBundle\Annotation as DI;
use Briareos\AjaxBundle\Ajax;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Kurumi\MainBundle\Entity\Picture;

class ProfileController extends Controller
{
    /**
     * @DI\Inject("templating.ajax.helper")
     *
     * @var \Briareos\AjaxBundle\Ajax\Helper
     */
    private $ajaxHelper;

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
     * @DI\Inject("profile.info.provider")
     *
     * @var \Kurumi\MainBundle\InfoProvider\ProfileInfoProvider
     */
    private $profileInfo;

    /**
     * @Route("/profile/{id}", name="profile", defaults={"id"=null})
     * @ParamConverter("subject", class="KurumiMainBundle:User")
     * @Secure(roles="authenticated_user")
     */
    public function viewAction(User $subject = null)
    {
        /** @var $user User */
        $user = $this->getUser();
        if ($subject === null) {
            $subject = $user;
        }
        $ownProfile = $user->getId() === $subject->getId();

        $templateFile = ':Profile:view.html.twig';
        $templateParams = array(
            'user' => $user,
            'subject' => $subject,
            'own_profile' => $ownProfile,
        );

        if ($ownProfile) {
            $url = $this->router->generate('front');
        } else {
            $url = $this->router->generate('profile', array('id' => $subject->getId()));
        }

        if ($this->getRequest()->isXmlHttpRequest()) {
            return $this->ajaxHelper->renderPjaxBlock($templateFile, $templateParams, $url, 'profile_page');
        } else {
            return $this->render($templateFile, $templateParams);
        }
    }

    /**
     * @Route("/profile/{id}/photo", name="profile_photos")
     * @ParamConverter("subject", class="KurumiMainBundle:User")
     */
    public function photosAction(User $subject = null)
    {
        /** @var $user User */
        $user = $this->getUser();
        if ($subject === null) {
            $subject = $user;
        }
        $ownProfile = $user->getId() === $subject->getId();

        if (!$ownProfile && !$this->profileInfo->hasPhotos($user->getProfile())) {
            return $this->redirect($this->generateUrl('profile', array('id' => $subject->getId())));
        }

        $templateFile = ':Profile:photos.html.twig';
        $templateParams = array(
            'user' => $user,
            'subject' => $subject,
            'own_profile' => $ownProfile,
        );

        if ($this->getRequest()->isXmlHttpRequest()) {
            return $this->ajaxHelper->renderPjaxBlock($templateFile, $templateParams, $this->router->generate('profile_photos', array('id' => $subject->getId())), 'profile_page');
        } else {
            return $this->render($templateFile, $templateParams);
        }
    }

    /**
     * @Route("/profile/{id}/photo/add/{type}", name="profile_photo_add", requirements={"gallery"="(profile|public|private)"})
     * @ParamConverter("subject", class="KurumiMainBundle:User")
     *
     * @param \Kurumi\MainBundle\Entity\User $subject
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function photoAddAction(User $subject = null, $type)
    {
        /** @var $user User */
        $user = $this->getUser();
        if ($subject === null) {
            $subject = $user;
        }
        $ownProfile = $user->getId() === $subject->getId();

        if (!$ownProfile) {
            throw new AccessDeniedException();
        }

        $photo = new Picture();
        switch ($type) {
            case 'profile':
                $gallery = $subject->getProfile()->getGalleryProfile();
                if ($gallery === null) {
                    $gallery = new Gallery();
                    $subject->getProfile()->setGalleryProfile($gallery);
                }
                break;
            case 'public':
                $gallery = $subject->getProfile()->getGalleryPublic();
                if ($gallery === null) {
                    $gallery = new Gallery();
                    $subject->getProfile()->setGalleryPublic($gallery);
                }
                break;
            case 'private':
                $gallery = $subject->getProfile()->getGalleryPrivate();
                if ($gallery === null) {
                    $gallery = new Gallery();
                    $subject->getProfile()->setGalleryPrivate($gallery);
                }
                break;
            default:
                throw new AccessDeniedException('Invalid type specified, must be either "profile", "public" or "private".');
        }
        $profilePhotoForm = $this->createForm(new ProfilePhotoFormType(), $photo);

        if ($this->getRequest()->isMethod('post')) {
            $profilePhotoForm->bind($this->getRequest());
            if ($profilePhotoForm->isValid()) {
                /** @var $gallery \Application\Sonata\MediaBundle\Entity\Gallery */
            }
        }

        $templateFile = ':Profile:photo_add.html.twig';
        $templateParams = array(
            'user' => $user,
            'subject' => $subject,
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
