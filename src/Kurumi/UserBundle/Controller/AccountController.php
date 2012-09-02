<?php

namespace Kurumi\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Briareos\NodejsBundle\Nodejs\Message;
use Kurumi\UserBundle\Form\Type\UserPasswordFormType;
use Kurumi\UserBundle\Form\Type\UserEmailFormType;
use JMS\DiExtraBundle\Annotation as DI;
use Kurumi\UserBundle\Entity\User;
use Kurumi\UserBundle\Entity\Profile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Kurumi\UserBundle\Form\Type\UserNameFormType;
use Briareos\AjaxBundle\Ajax;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Application\Sonata\MediaBundle\Entity\Media;
use Kurumi\UserBundle\Form\Type\UserPictureFormType;

class AccountController extends Controller
{
    use Ajax\PjaxTrait;

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
     * @DI\Inject("nodejs.dispatcher")
     *
     * @var \Briareos\NodejsBundle\Nodejs\DispatcherInterface
     */
    private $nodejsDispatcher;

    /**
     * @Route("/account", name="account_overview")
     */
    public function overviewAction()
    {
        $templateName = 'UserBundle:Account:overview.html.twig';
        $templateParams = array(
            'user' => $this->getUser(),
        );

        if ($this->getRequest()->isXmlHttpRequest()) {
            $commands = new Ajax\CommandContainer();
            $pjax = $this->getPjaxContainers();
            if (in_array('edit_account', $pjax)) {
                $commands->add(new Ajax\Command\Page($this->ajax->renderBlock($templateName, 'title', $templateParams), $this->ajax->renderBlock($templateName, 'edit_account', $templateParams), $this->router->generate('account_overview'), 'edit_account'));
            } else {
                $commands->add(new Ajax\Command\Page($this->ajax->renderBlock($templateName, 'title', $templateParams), $this->ajax->renderBlock($templateName, 'body', $templateParams), $this->router->generate('account_overview')));
            }
            return new Ajax\Response($commands);
        } else {
            return $this->render($templateName, $templateParams);
        }
    }

    /**
     * @Route("/account/edit-name", name="account_edit_name")
     * @Secure(roles="IS_AUTHENTICATED_REMEMBERED")
     */
    public function editNameAction()
    {
        /** @var $user \Kurumi\UserBundle\Entity\User */
        $user = $this->getUser();

        $userNameForm = $this->createForm(new UserNameFormType(), $user);

        if ($this->getRequest()->isMethod('post')) {
            $userNameForm->bind($this->getRequest());
            if ($userNameForm->isValid()) {
                $this->em->persist($user);
                $this->em->flush();
                $this->session->getFlashBag()->add('success', "Your name was successfully changed.");
            }
        }

        $templateFile = 'UserBundle:Account:edit_name.html.twig';
        $templateParams = array(
            'user' => $this->getUser(),
            'form' => $userNameForm->createView(),
        );
        if ($this->getRequest()->isXmlHttpRequest()) {
            $commands = new Ajax\CommandContainer();
            $pjax = $this->getPjaxContainers();
            if ($userNameForm->isBound()) {
                if ($userNameForm->isValid()) {
                    return $this->redirect($this->router->generate('account_overview', $this->getPjaxParameters()));
                } else {
                    $commands->add(new Ajax\Command\Form($this->renderView('UserBundle:Form:edit_user_name_form.html.twig', $templateParams)));
                }
            } else {
                if (in_array('edit_account', $pjax)) {
                    $commands->add(new Ajax\Command\Page($this->ajax->renderBlock($templateFile, 'title', $templateParams), $this->ajax->renderBlock($templateFile, 'edit_account', $templateParams), $this->router->generate('account_edit_name'), 'edit_account'));
                } else {
                    $commands->add(new Ajax\Command\Page($this->ajax->renderBlock($templateFile, 'title', $templateParams), $this->ajax->renderBlock($templateFile, 'body', $templateParams), $this->router->generate('account_edit_name')));
                }
            }
            return new Ajax\Response($commands);
        } else {
            return $this->render($templateFile, $templateParams);
        }
    }

    /**
     * @Route("/account/edit-email", name="account_edit_email")
     * @Secure(roles="IS_AUTHENTICATED_REMEMBERED")
     */
    public function editEmailAction()
    {
        /** @var $user User */
        $user = $this->getUser();

        $editEmailForm = $this->createForm(new UserEmailFormType(), $user);

        if ($this->getRequest()->isMethod('post')) {
            $editEmailForm->bind($this->getRequest());
            if ($editEmailForm->isValid()) {
                $this->em->persist($user);
                $this->em->flush();
                $this->session->getFlashBag()->add('success', "Your email has been updated.");
            }
        }

        $templateFile = 'UserBundle:Account:edit_email.html.twig';
        $templateParams = array(
            'user' => $this->getUser(),
            'form' => $editEmailForm->createView(),
        );

        if ($this->getRequest()->isXmlHttpRequest()) {
            $commands = new Ajax\CommandContainer();
            $pjax = $this->getPjaxContainers();
            if ($editEmailForm->isBound() && $editEmailForm->isValid()) {
                return $this->redirect($this->router->generate('account_overview', $this->getPjaxParameters()));
            } elseif ($editEmailForm->isBound()) {
                $commands->add(new Ajax\Command\Form($this->renderView('UserBundle:Form:edit_user_email_form.html.twig', $templateParams)));
            } else {
                $ajaxRoute = $this->router->generate('account_edit_email');
                $ajaxTitle = $this->ajax->renderBlock($templateFile, 'title', $templateParams);
                if (in_array('edit_account', $pjax)) {
                    $pjaxContainer = 'edit_account';
                } else {
                    $pjaxContainer = 'body';
                }
                $ajaxBody = $this->ajax->renderBlock($templateFile, $pjaxContainer, $templateParams);
                $commands->add(new Ajax\Command\Page($ajaxTitle, $ajaxBody, $ajaxRoute, $pjaxContainer));
            }
            return new Ajax\Response($commands);
        } else {
            return $this->render($templateFile, $templateParams);
        }
    }

    /**
     * @Route("/account/edit-password", name="account_edit_password")
     * @Secure(roles="IS_AUTHENTICATED_REMEMBERED")
     */
    public function editPasswordAction()
    {
        /** @var $user User */
        $user = $this->getUser();

        $editPasswordForm = $this->createForm(new UserPasswordFormType(), $user);

        if ($this->getRequest()->isMethod('post')) {
            $editPasswordForm->bind($this->getRequest());
            if ($editPasswordForm->isValid()) {
                $this->em->persist($user);
                $this->em->flush();
                $this->session->getFlashBag()->add('success', "Your password has been updated.");
            }
        }

        $templateFile = 'UserBundle:Account:edit_password.html.twig';
        $templateParams = array(
            'user' => $this->getUser(),
            'form' => $editPasswordForm->createView(),
        );

        if ($this->getRequest()->isXmlHttpRequest()) {
            $commands = new Ajax\CommandContainer();
            $pjax = $this->getPjaxContainers();
            if ($editPasswordForm->isBound() && $editPasswordForm->isValid()) {
                return $this->redirect($this->router->generate('account_overview', $this->getPjaxParameters()));
            } elseif ($editPasswordForm->isBound()) {
                $commands->add(new Ajax\Command\Form($this->renderView('UserBundle:Form:edit_user_password_form.html.twig', $templateParams)));
            } else {
                $ajaxRoute = $this->router->generate('account_edit_password');
                $ajaxTitle = $this->ajax->renderBlock($templateFile, 'title', $templateParams);
                if (in_array('edit_account', $pjax)) {
                    $pjaxContainer = 'edit_account';
                } else {
                    $pjaxContainer = 'body';
                }
                $ajaxBody = $this->ajax->renderBlock($templateFile, $pjaxContainer, $templateParams);
                $commands->add(new Ajax\Command\Page($ajaxTitle, $ajaxBody, $ajaxRoute, $pjaxContainer));
            }
            return new Ajax\Response($commands);
        } else {
            return $this->render($templateFile, $templateParams);
        }
    }

    /**
     * @Route("/account/edit-picture", name="account_edit_picture")
     */
    public function editPictureAction()
    {
        /** @var $user \Kurumi\UserBundle\Entity\User */
        $user = $this->getUser();
        $newPicture = new Media();
        $request = $this->getRequest();

        $userPictureForm = $this->createForm(new UserPictureFormType(), $newPicture);

        if ($request->isMethod('post')) {
            $userPictureForm->bind($request);
            if ($userPictureForm->isValid()) {
                /** @var $em \Doctrine\ORM\EntityManager */
                $em = $this->getDoctrine()->getManager();
                $newPicture->setContext('user_picture');
                $oldPicture = $user->getPicture();
                $user->setPicture($newPicture);
                // Picture persist is cascaded.
                $em->persist($user);
                $em->flush();
                if ($oldPicture === null) {
                    $this->session->getFlashBag()->add('success', "Your profile picture has been set.");
                } else {
                    $em->remove($oldPicture);
                    $this->session->getFlashBag()->add('success', "Your profile picture has been updated.");
                }
                $pictureChangedMessage = new Message('pictureChanged');
                $pictureChangedMessage->setChannel(sprintf('user_%s', $user->getId()));
                $this->nodejsDispatcher->dispatch($pictureChangedMessage);
            }
        }

        $templateFile = 'UserBundle:Account:edit_picture.html.twig';
        $templateParams = array(
            'user' => $this->getUser(),
            'form' => $userPictureForm->createView(),
        );

        if ($request->isXmlHttpRequest()) {
            $commands = new Ajax\CommandContainer();
            if ($this->getRequest()->query->getInt('modal', 0)) {
                $modalTemplateFile = 'UserBundle:Form:edit_user_picture_modal_form.html.twig';
                if ($userPictureForm->isBound() && $userPictureForm->isValid()) {
                    $commands->add(new Ajax\Command\Modal($this->renderView($modalTemplateFile, $templateParams)));
                } elseif ($userPictureForm->isBound()) {
                    $commands->add(new Ajax\Command\Form($this->renderView($modalTemplateFile, $templateParams)));
                } else {
                    $commands->add(new Ajax\Command\Modal($this->renderView($modalTemplateFile, $templateParams)));
                }
            } else {
                $pjax = $this->getPjaxContainers();
                if ($userPictureForm->isBound() && $userPictureForm->isValid()) {
                    return $this->redirect($this->router->generate('account_overview', $this->getPjaxParameters()));
                } elseif ($userPictureForm->isBound()) {
                    $commands->add(new Ajax\Command\Form($this->renderView('UserBundle:Form:edit_user_picture_form.html.twig', $templateParams)));
                } else {
                    // Form is not submitted.
                    if (in_array('edit_account', $pjax)) {
                        $commands->add(new Ajax\Command\Page($this->ajax->renderBlock($templateFile, 'title', $templateParams), $this->ajax->renderBlock($templateFile, 'edit_account', $templateParams), $this->router->generate('account_edit_picture'), 'edit_account'));
                    } else {
                        $commands->add(new Ajax\Command\Page($this->ajax->renderBlock($templateFile, 'title', $templateParams), $this->ajax->renderBlock($templateFile, 'body', $templateParams), $this->router->generate('account_edit_picture')));
                    }
                }
            }
            return new Ajax\Response($commands);
        } else {
            // Not an ajax request.
            if ($userPictureForm->isBound() && $userPictureForm->isValid()) {
                return $this->redirect($this->generateUrl('account_edit_picture'));
            } else {
                return $this->render($templateFile, $templateParams);
            }
        }
    }

    /**
     * @Route("/account/delete-picture/{token}", name="account_delete_picture")
     */
    public function deletePictureAction($token)
    {
        /** @var $user \Kurumi\UserBundle\Entity\User */
        $user = $this->getUser();
        /** @var $picture Media */
        $picture = $user->getPicture();
        if ($picture !== null && $this->csrfProvider->isCsrfTokenValid('account_delete_picture', $token)) {
            /** @var $em \Doctrine\ORM\EntityManager */
            $em = $this->getDoctrine()->getManager();
            $user->setPicture(null);
            $em->remove($picture);
            $em->flush();
            $this->session->getFlashBag()->set('success', "Your picture was deleted.");
        }
        return $this->editPictureAction();
    }
}
