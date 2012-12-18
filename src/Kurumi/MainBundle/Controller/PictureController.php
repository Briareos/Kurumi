<?php

namespace Kurumi\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Briareos\AjaxBundle\Ajax;
use Application\Sonata\MediaBundle\Entity\Media;
use Kurumi\MainBundle\Form\Type\UserPictureFormType;
use JMS\DiExtraBundle\Annotation as DI;

class PictureController extends Controller
{
    /**
     * @DI\Inject("form.csrf_provider")
     *
     * @var \Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface
     */
    private $csrfProvider;

    /**
     * @DI\Inject("session")
     *
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    private $session;

    /**
     * @DI\Inject("templating.ajax.helper")
     *
     * @var \Briareos\AjaxBundle\Ajax\Helper
     */
    private $ajaxHelper;

    public function uploadPictureAction()
    {
        /** @var $user \Kurumi\MainBundle\Entity\User */
        $user = $this->getUser();
        $currentPicture = $user->getPicture();
        $picture = new Media();
        $request = $this->getRequest();

        $form = $this->createForm(new UserPictureFormType(), $picture);

        if ($request->isMethod('post')) {
            $form->bind($request);
            if ($form->isValid()) {
                /** @var $em \Doctrine\ORM\EntityManager */
                $em = $this->getDoctrine()->getManager();
                /** @var $userPicture Media */
                $userPicture = $form->getData();
                $userPicture->setContext('user_picture');
                $user->setPicture($userPicture);
                if ($currentPicture !== null) {
                    $em->remove($currentPicture);
                }
                $em->persist($userPicture);
                $em->persist($user);
                $em->flush();
            }
        }

        $modalTemplateFile = ':Form:user_picture_modal.html.twig';
        $templateParams = array(
            'form' => $form->createView(),
            'user' => $user,
        );

        if ($request->isXmlHttpRequest()) {
            if ($form->isBound() && $form->isValid()) {
                return $this->ajaxHelper->renderModal($modalTemplateFile, $templateParams);
            } elseif ($form->isBound()) {
                return $this->ajaxHelper->renderAjaxForm($modalTemplateFile, $templateParams);
            } else {
                return $this->ajaxHelper->renderModal($modalTemplateFile, $templateParams);
            }
        } else {
            if ($form->isBound() && $form->isValid()) {
                return $this->redirect($this->generateUrl('front'));
            } else {
                return $this->render(':Form:user_picture.html.twig', $templateParams);
            }
        }
    }

    /**
     * @Route("/delete-picture/{token}", name="delete_user_picture")
     */
    public function deletePictureAction($token)
    {
        /** @var $user \Kurumi\MainBundle\Entity\User */
        $user = $this->getUser();
        /** @var $picture Media */
        $picture = $user->getPicture();
        if ($picture !== null && $this->csrfProvider->isCsrfTokenValid('delete_user_picture', $token)) {
            $this->session->getFlashBag()->set('success', "Your picture was deleted.");
            /** @var $em \Doctrine\ORM\EntityManager */
            $em = $this->getDoctrine()->getManager();
            $user->setPicture(null);
            $em->remove($picture);
            $em->flush();
        }

        return $this->uploadPictureAction();
    }
}