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
