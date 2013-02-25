<?php

namespace Kurumi\MainBundle\Controller;

use Kurumi\MainBundle\Entity\PictureComment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\SecurityExtraBundle\Annotation\SecureParam;
use JMS\DiExtraBundle\Annotation\Inject;
use Kurumi\MainBundle\Entity\Profile;
use Kurumi\MainBundle\Form\Type\PictureCommentFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Briareos\AjaxBundle\Ajax;
use Kurumi\MainBundle\Entity\Picture;
use Kurumi\MainBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class PictureController extends Controller
{
    /**
     * @var \Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface
     *
     * @Inject("form.csrf_provider")
     */
    private $csrfProvider;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     *
     * @Inject("session")
     */
    private $session;

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
     * @var \Kurumi\MainBundle\Paginator\PaginatorHelper
     *
     * @Inject("paginator.helper")
     */
    private $paginatorHelper;

    /**
     * @Route("/picture/{id}/comment", name="picture_comment")
     * @Method("POST")
     * @ParamConverter("picture", class="KurumiMainBundle:Picture")
     * @SecureParam(name="picture", permissions="COMMENT")
     */
    public function commentAction(Picture $picture, Request $request)
    {
        /** @var $user User */
        $user = $this->getUser();
        $profile = $user->getProfile();

        $comment = new PictureComment();
        $form = $this->createPictureCommentForm($comment);
        if ($request->isMethod('post')) {
            $form->bind($request);
            if ($form->isValid()) {
                $comment->setProfile($profile);
                $comment->setPicture($picture);
                $this->em->persist($comment);
            }
        }

        $formTemplate = ':Form:picture_comment.html.twig';
        $modalFormTemplate = ':Form:picture_comment_modal.html.twig';
        $templateParams = [
            'profile' => $profile,
            'picture' => $picture,
        ];

        if ($request->isXmlHttpRequest()) {
            if ($this->ajaxHelper->isModal()) {
                if ($form->isBound() && $form->isValid()) {
                    // Return populated comments template.
                } elseif ($form->isBound()) {
                    return $this->ajaxHelper->renderAjaxForm($modalFormTemplate, $templateParams);
                }
            } else {
                if ($form->isBound() && $form->isValid()) {

                } elseif ($form->isBound()) {
                    return $this->ajaxHelper->renderAjaxForm($formTemplate, $templateParams);
                }
            }
            // Form is unbound, Ajax GET method is not yet implemented.
            throw new HttpException(400);
        } else {
            $url = $this->generateUrl('picture', ['id' => $picture->getId()]);
            return $this->redirect($url);
        }
    }

    /**
     * @Route("/picture/{id}/comment", name="picture_comment")
     * @Method("GET")
     * @SecureParam(name="picture", permissions="VIEW_COMMENTS")
     */
    public function commentsAction(Picture $picture, Request $request)
    {
        $template = ':Picture:comments.html.twig';
    }

    /**
     * @Route("/photo/{id}", name="profile_photo")
     * @ParamConverter("picture", class="KurumiMainBundle:Picture")
     * @SecureParam(name="picture", permissions="VIEW")
     */
    public function photoAction(Picture $picture, Request $request)
    {
        /** @var $user User */
        $user = $this->getUser();

        $profile = $picture->getProfile();
        $ownProfile = $profile->isUser($user);

        $comment = new PictureComment();
        $form = $this->createPictureCommentForm($comment);

        $templateFile = ':Picture:picture.html.twig';
        $modalTemplateFile = ':Picture:picture_modal.html.twig';
        $templateParams = array(
            'profile' => $profile,
            'own_profile' => $ownProfile,
            'picture' => $picture,
            'form' => $form->createView(),
        );

        if ($request->isXmlHttpRequest()) {
            if ($this->ajaxHelper->isModal()) {
                return $this->ajaxHelper->renderModal($modalTemplateFile, $templateParams);
            } else {
                $url = $this->generateUrl('profile_photo', ['id' => $picture->getId()] + $this->ajaxHelper->getPjaxParameters());
                return $this->ajaxHelper->renderPjaxBlock($templateFile, $templateParams, $url);
            }
        } else {
            return $this->render($templateFile, $templateParams);
        }
    }

    public function createPictureCommentForm(PictureComment $comment)
    {
        $form = $this->createForm(new PictureCommentFormType(), $comment);
        return $form;
    }

    /**
     * @Route("/profile/{id}/photo-add/{type}", name="profile_photo_add", requirements={"type"="(profile|public|private)"})
     * @ParamConverter("profile", class="KurumiMainBundle:Profile")
     */
    public function photoAddAction(Profile $profile, $type)
    {
        /** @var $user User */
        $user = $this->getUser();
        $ownProfile = $profile->isUser($user);

        if (!$ownProfile) {
            throw new AccessDeniedHttpException();
        }

        $photo = new Picture();
        switch ($type) {
            case 'profile':
                break;
            case 'public':
                break;
            case 'private':
                break;
            default:
                throw new AccessDeniedHttpException('Invalid type specified, must be either "profile", "public" or "private".');
        }
        $profilePhotoForm = $this->createForm(new ProfilePictureFormType(), $photo);

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
                $modalTemplateFile = ':Form:add_picture_modal_form.html.twig';
            }
        } else {
            return $this->render($templateFile, $templateParams);
        }
    }
}
