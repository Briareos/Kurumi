<?php

namespace Kurumi\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\DiExtraBundle\Annotation as DI;
use Kurumi\UserBundle\Entity\User;
use Kurumi\UserBundle\Entity\Profile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Kurumi\UserBundle\Form\Type\EditProfileFormType;
use Kurumi\UserBundle\Form\Type\EditUserFormType;
use Briareos\AjaxBundle\Ajax;
use JMS\SecurityExtraBundle\Annotation\Secure;

class EditAccountController extends Controller
{
    /**
     * @DI\Inject("templating.ajax")
     *
     * @var \Briareos\AjaxBundle\Twig\AjaxEngine
     */
    private $ajax;

    /**
     * @DI\Inject("doctrine.orm.default_entity_manager")
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @DI\Inject("session")
     *
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    private $session;

    /**
     * @DI\Inject("router")
     *
     * @var \Symfony\Component\Routing\Router
     */
    private $router;

    /**
     * @DI\Inject("form.csrf_provider")
     *
     * @var \Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface
     */
    private $csrfProvider;

    /**
     * @DI\Inject("nodejs.authenticator")
     *
     * @var \Briareos\NodejsBundle\Nodejs\Authenticator
     */
    private $nodejsAuthenticator;

    /**
     * @Route("/account", name="account_overview")
     */
    public function accountOverviewAction()
    {
        /** @var $user \Kurumi\UserBundle\Entity\User */
        $user = $this->getUser();
        $this->nodejsAuthenticator->authenticate($this->getRequest()->getSession(), $user);
        $templateParams = array(
            'user' => $user,
            'nodejs_auth_token' => $this->nodejsAuthenticator->generateAuthToken($this->getRequest()->getSession(), $user),
        );

        return $this->render('UserBundle:Account:overview_page.html.twig', $templateParams);
    }

    /**
     * @Route("/edit-account", name="edit_account")
     * @Secure(roles="IS_AUTHENTICATED_REMEMBERED")
     */
    public function editAccountAction()
    {
        /** @var $user \Kurumi\UserBundle\Entity\User */
        $user = $this->getUser();

        $editUserForm = $this->createForm(new EditUserFormType(), $user);
        $editProfileForm = $this->createForm(new EditProfileFormType(), $user->getProfile());

        if ($this->getRequest()->isMethod('post')) {
            $editProfileForm->bind($this->getRequest());
            if ($editProfileForm->isValid()) {
                $this->em->persist($user->getProfile());
                $this->em->flush();
                $this->session->getFlashBag()->add('success', "Profile successfully updated.");
            }
        }

        /** @var $nodejsAuthenticator \Briareos\NodejsBundle\Nodejs\Authenticator */
        $nodejsAuthenticator = $this->get('nodejs.authenticator');
        $nodejsAuthenticator->authenticate($this->getRequest()->getSession(), $user);
        $templateFile = 'UserBundle:Account:edit_account_page.html.twig';
        $templateParams = array(
            'user' => $user,
            'nodejs_auth_token' => $nodejsAuthenticator->generateAuthToken($this->getRequest()->getSession(), $user),
            'edit_user_form' => $editUserForm->createView(),
            'edit_profile_form' => $editProfileForm->createView(),
        );
        if ($this->getRequest()->isXmlHttpRequest()) {
            $commands = array();
            $commands[] = new Ajax\Command\Page($this->ajax->renderBlock($templateFile, 'title', $templateParams), $this->ajax->renderBlock($templateFile, 'body', $templateParams));
            return new Ajax\Response($commands);
        } else {
            return $this->render($templateFile, $templateParams);
        }
    }

    /**
     * @Route("/upload-picture", name="upload_user_picture")
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

    /**
     * @Route("/edit-user", name="edit_user")
     */
    public function editUserAction()
    {
        $editUserForm = $this->createForm(new EditUserFormType(), $user);

        if ($this->getRequest()->isMethod('post')) {
            $editUserForm->bind($this->getRequest());
            if ($editUserForm->isValid()) {
                $this->em->persist($user->getProfile());
                $this->em->flush();
                $this->session->getFlashBag()->add('success', "Account info successfully updated.");
            }
        }
    }

    /**
     * @Route("/edit-profile", name="edit_profile")
     */
    public function editProfileAction()
    {
        $editProfileForm = $this->createForm(new EditProfileFormType(), $user->getProfile());

        if ($this->getRequest()->isMethod('post')) {
            $editProfileForm->bind($this->getRequest());
            if ($editProfileForm->isValid()) {
                $this->em->persist($user->getProfile());
                $this->em->flush();
                $this->session->getFlashBag()->add('success', "Profile info successfully updated.");
            }
        }

        if ($this->getRequest()->isXmlHttpRequest()) {
            $commands = array();
            $commands[] = new Ajax\Command\Form($this->renderView('UserBundle:Form:edit_profile_form.html.twig'));
        }
        return $this->redirect($this->router->generate('edit_account'));
    }
}
