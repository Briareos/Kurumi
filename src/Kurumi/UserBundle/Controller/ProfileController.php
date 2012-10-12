<?php

namespace Kurumi\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Application\Sonata\MediaBundle\Entity\Gallery;
use Kurumi\UserBundle\Form\Type\ProfilePhotoFormType;
use Application\Sonata\MediaBundle\Entity\Media;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Kurumi\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\DiExtraBundle\Annotation as DI;
use Briareos\AjaxBundle\Ajax;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Application\Sonata\MediaBundle\Entity\GalleryHasMedia;

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
     * @DI\Inject("user_profile_info_provider")
     *
     * @var \Kurumi\UserBundle\User\ProfileInfoProvider
     */
    private $profileInfo;

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
     * @Route("/profile/{id}/photos", name="profile_photos")
     * @ParamConverter("subject", class="UserBundle:User")
     * @Secure(roles="IS_AUTHENTICATED_REMEMBERED")
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

        $templateFile = 'UserBundle:Profile:photos.html.twig';
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
     * @Route("/profile/{id}/photos/add/{type}", name="profile_photo_add", requirements={"gallery"="(profile|public|private)"})
     * @ParamConverter("subject", class="UserBundle:User")
     *
     * @param \Kurumi\UserBundle\Entity\User $subject
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

        $photo = new Media();
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

        $templateFile = 'UserBundle:Profile:photo_add.html.twig';
        $templateParams = array(
            'user' => $user,
            'subject' => $subject,
            'own_profile' => $ownProfile,
            'form' => $profilePhotoForm->createView(),
        );

        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->getRequest()->query->getInt('modal', 0)) {
                $modalTemplateFile = 'UserBundle:Form:add_photo_modal_form.html.twig';
            }
        } else {
            return $this->render($templateFile, $templateParams);
        }
    }
}
