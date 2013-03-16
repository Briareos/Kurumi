<?php

namespace Kurumi\MainBundle\Controller;

use Kurumi\MainBundle\Entity\PictureComment;
use Kurumi\MainBundle\Form\Type\PictureFormType;
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
     * @Route("/picture/{id}", name="picture")
     * @ParamConverter("picture", class="KurumiMainBundle:Picture")
     * @SecureParam(name="picture", permissions="VIEW")
     */
    public function viewAction(Picture $picture, Request $request)
    {
        /** @var $user User */
        $user = $this->getUser();

        $profile = $picture->getProfile();
        $ownProfile = $profile->isUser($user);

        $comment = new PictureComment();
        $form = $this->createPictureCommentForm($comment);

        //$comments = $this->getComments($picture, $request->query->getDigits('page', 1), 10, false);
        //$this->paginatorHelper

        $templateFile = ':Picture:view.html.twig';
        $modalTemplateFile = ':Picture:view_modal.html.twig';
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
                $url = $this->generateUrl('picture', ['id' => $picture->getId()] + $this->ajaxHelper->getPjaxParameters());
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
     * @Route("/profile/{id}/picture-add/{type}", name="picture_add", requirements={"type"="(profile|public|private)"})
     * @ParamConverter("profile", class="KurumiMainBundle:Profile")
     */
    public function addAction(Profile $profile, $type)
    {
        /** @var $user User */
        $user = $this->getUser();
        $ownProfile = $profile->isUser($user);

        if (!$ownProfile) {
            throw new AccessDeniedHttpException();
        }

        $pictureTypes = [
            'profile' => Picture::PROFILE_PICTURE,
            'public' => Picture::PUBLIC_PICTURE,
            'private' => Picture::PRIVATE_PICTURE,
        ];

        $picture = new Picture();
        $picture->setPictureType($pictureTypes[$type]);
        $form = $this->createForm(new PictureFormType(), $picture);

        if ($this->getRequest()->isMethod('post')) {
            $form->bind($this->getRequest());
            if ($form->isValid()) {
                $this->em->persist($picture);
            }
        }

        $templateFile = ':Picture:add.html.twig';
        $modalTemplateFile = ':Picture:add_modal.html.twig';
        $formTemplateFile = ':Form:add_picture.html.twig';
        $modalFormTemplateFile = ':Form:add_picture_modal.html.twig';
        $templateParams = array(
            'profile' => $profile,
            'own_profile' => $ownProfile,
            'type' => $type,
            'form' => $form->createView(),
        );

        // Handle ajax.
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->ajaxHelper->isModal()) {
                if ($form->isBound() && $form->isValid()) {
                    // Do what now?
                } elseif ($form->isBound()) {
                    return $this->ajaxHelper->renderAjaxForm($modalFormTemplateFile, $templateParams);
                } else {
                    return $this->ajaxHelper->renderModal($modalTemplateFile, $templateParams);
                }
            }
        } elseif ($form->isBound() && $form->isValid()) {
            $url = $this->generateUrl('profile_pictures', ['id' => $profile->getId()]);
            return $this->redirect($url);
        }

        return $this->render($templateFile, $templateParams);
    }
}
