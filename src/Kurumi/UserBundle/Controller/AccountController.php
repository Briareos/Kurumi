<?php

namespace Kurumi\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kurumi\UserBundle\Form\Type\FillProfileFormType;
use Briareos\NodejsBundle\Nodejs\Message;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
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
        $templateFile = 'UserBundle:Account:overview.html.twig';
        $templateParams = array(
            'user' => $this->getUser(),
        );

        if ($this->getRequest()->isXmlHttpRequest()) {
            $url = $this->router->generate('account_overview');
            return $this->ajaxHelper->renderPjaxBlock($templateFile, $templateParams, $url, 'edit_account');
        } else {
            return $this->render($templateFile, $templateParams);
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
            if ($userNameForm->isBound()) {
                if ($userNameForm->isValid()) {
                    return $this->redirect($this->router->generate('account_overview', $this->ajaxHelper->getPjaxParameters()));
                } else {
                    return $this->ajaxHelper->renderAjaxForm('UserBundle:Form:edit_user_name_form.html.twig', $templateParams);
                }
            } else {
                $url = $this->router->generate('account_edit_name');
                return $this->ajaxHelper->renderPjaxBlock($templateFile, $templateParams, $url, 'edit_account');
            }
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
            if ($editEmailForm->isBound()) {
                if ($editEmailForm->isValid()) {
                    return $this->redirect($this->router->generate('account_overview', $this->ajaxHelper->getPjaxParameters()));
                } else {
                    return $this->ajaxHelper->renderAjaxForm('UserBundle:Form:edit_user_email_form.html.twig', $templateParams);
                }
            } else {
                $url = $this->router->generate('account_edit_email');
                return $this->ajaxHelper->renderPjaxBlock($templateFile, $templateParams, $url, 'edit_account');
            }
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
            if ($editPasswordForm->isBound()) {
                if ($editPasswordForm->isValid()) {
                    return $this->redirect($this->router->generate('account_overview', $this->ajaxHelper->getPjaxParameters()));
                } else {
                    return $this->ajaxHelper->renderAjaxForm('UserBundle:Form:edit_user_password_form.html.twig', $templateParams);
                }
            } else {
                $url = $this->router->generate('account_edit_password');
                return $this->ajaxHelper->renderPjaxBlock($templateFile, $templateParams, $url, 'edit_account');
            }
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
            if ($this->ajaxHelper->isModal()) {
                $modalTemplateFile = 'UserBundle:Form:edit_user_picture_modal_form.html.twig';
                if ($userPictureForm->isBound()) {
                    if ($userPictureForm->isValid()) {
                        return $this->ajaxHelper->renderModal($modalTemplateFile, $templateParams);
                    } elseif ($userPictureForm->isBound()) {
                        return $this->ajaxHelper->renderAjaxForm($modalTemplateFile, $templateParams);
                    }
                } else {
                    return $this->ajaxHelper->renderModal($modalTemplateFile, $templateParams);
                }
            } else {
                if ($userPictureForm->isBound()) {
                    if ($userPictureForm->isValid()) {
                        return $this->redirect($this->router->generate('account_overview', $this->ajaxHelper->getPjaxParameters()));
                    } elseif ($userPictureForm->isBound()) {
                        return $this->ajaxHelper->renderAjaxForm('UserBundle:Form:edit_user_picture_form.html.twig', $templateParams);
                    }
                } else {
                    $url = $this->router->generate('account_edit_picture');
                    return $this->ajaxHelper->renderPjaxBlock($templateFile, $templateParams, $url, 'edit_account');
                }
            }
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

    /**
     * @Route("/account/fill", name="account_fill", defaults={"id"=null})
     * @Secure(roles="IS_AUTHENTICATED_REMEMBERED")
     */
    public function fillAction($route, $id = null)
    {
        /** @var $user User */
        $user = $this->getUser();
        if ($user->getProfile() === null) {
            $this->session->getFlashBag()->add('warning', "You don't have a profile. If you would like to create one, please fill in the form below.");
            $profile = new Profile();
            $user->setProfile($profile);
        } else {
            //$this->session->getFlashBag()->add('warning', "Your profile is incomplete. Please fill in the information below.");
        }


        $createProfileForm = $this->createForm(new FillProfileFormType(), $user->getProfile());

        if ($this->getRequest()->isMethod('post')) {
            $createProfileForm->bind($this->getRequest());
            if ($createProfileForm->isValid()) {
                $this->em->persist($user);
                $this->em->flush();
                $this->session->getFlashBag()->add('success', "Your profile is ready. Good luck!");
            }
        }

        $activeRoute = $route;
        if ($activeRoute === 'profile' && in_array($id, array(null, $user->getId()))) {
            $route = 'front';
            $activeRoute = 'home';
        }

        $templateFile = 'UserBundle:Account:fill_profile_page.html.twig';
        $templateParams = array(
            'user' => $user,
            'form' => $createProfileForm->createView(),
            'active' => $activeRoute,
            'route' => $route,
        );

        if ($createProfileForm->isBound() && $createProfileForm->isValid()) {
            return $this->redirect($this->router->generate($route, $this->ajaxHelper->getPjaxParameters()));
        }

        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($createProfileForm->isBound()) {
                return $this->ajaxHelper->renderAjaxForm('UserBundle:Form:fill_profile_form.html.twig', $templateParams);
            } else {
                $url = $this->router->generate($route);
                return $this->ajaxHelper->renderPjaxBlock($templateFile, $templateParams, $url);
            }
        } else {
            return $this->render($templateFile, $templateParams);
        }
    }
}
