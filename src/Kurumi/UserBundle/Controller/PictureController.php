<?php

namespace Kurumi\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Briareos\AjaxBundle\Ajax;
use Application\Sonata\MediaBundle\Entity\Media;
use Kurumi\UserBundle\Form\Type\UserPictureFormType;
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
     */
    public function uploadPictureAction()
    {
        /** @var $user \Kurumi\UserBundle\Entity\User */
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

        $templateParams = array(
            'form' => $form->createView(),
            'user' => $user,
            'picture' => $user->getPicture(),
        );

        if ($request->isXmlHttpRequest()) {
            $commands = array();
            if ($form->isBound() && $form->isValid()) {
                $commands[] = new Ajax\Command\Modal($this->renderView('UserBundle:Form:user_picture_form.html.twig', $templateParams));
            } elseif ($form->isBound()) {
                $commands[] = new Ajax\Command\Form($this->renderView('UserBundle:Form:user_picture_form.html.twig', $templateParams));
            } else {
                $commands[] = new Ajax\Command\Modal($this->renderView('UserBundle:Form:user_picture_form.html.twig', $templateParams));
            }
            return new Ajax\Response($commands);
        } else {
            if ($form->isBound() && $form->isValid()) {
                return $this->redirect($this->generateUrl('front'));
            } else {
                return $this->render('UserBundle:Form:user_picture_form.html.twig', $templateParams);
            }
        }
    }

    /**
     * @Route("/delete-picture/{token}", name="delete_user_picture")
     */
    public function deletePictureAction($token)
    {
        /** @var $user \Kurumi\UserBundle\Entity\User */
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