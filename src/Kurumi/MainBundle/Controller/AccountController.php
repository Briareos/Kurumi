<?php

namespace Kurumi\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kurumi\MainBundle\Entity\Picture;
use Symfony\Component\HttpFoundation\Request;
use Kurumi\MainBundle\Form\Type\FillProfileFormType;
use Briareos\NodejsBundle\Nodejs\Message;
use Kurumi\MainBundle\Form\Type\UserPasswordFormType;
use Kurumi\MainBundle\Form\Type\UserEmailFormType;
use JMS\DiExtraBundle\Annotation as DI;
use Kurumi\MainBundle\Entity\User;
use Kurumi\MainBundle\Entity\Profile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Kurumi\MainBundle\Form\Type\UserNameFormType;
use Briareos\AjaxBundle\Ajax;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Kurumi\MainBundle\Form\Type\UserPictureFormType;

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
    public function overviewAction(Request $request)
    {
        $templateFile = ':Account:overview.html.twig';
        $templateParams = array(
            'user' => $this->getUser(),
        );

        if ($request->isXmlHttpRequest()) {
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
    public function editNameAction(Request $request)
    {
        /** @var $user \Kurumi\MainBundle\Entity\User */
        $user = $this->getUser();

        $userNameForm = $this->createForm(new UserNameFormType(), $user);

        if ($request->isMethod('post')) {
            $userNameForm->bind($request);
            if ($userNameForm->isValid()) {
                $this->em->persist($user);
                $this->em->flush();
                $this->session->getFlashBag()->add('success', "Your name was successfully changed.");

                return $this->redirect($this->router->generate('account_overview', $this->ajaxHelper->getPjaxParameters()));
            }
        }

        $pageTemplate = ':Account:edit_name.html.twig';
        $formTemplate = ':Form:user_name.html.twig';
        $templateParams = array(
            'user' => $this->getUser(),
            'form' => $userNameForm->createView(),
        );
        if ($request->isXmlHttpRequest()) {
            if ($userNameForm->isBound()) {
                return $this->ajaxHelper->renderAjaxForm($formTemplate, $templateParams);
            } else {
                $url = $this->router->generate('account_edit_name');

                return $this->ajaxHelper->renderPjaxBlock($pageTemplate, $templateParams, $url, 'edit_account');
            }
        } else {
            return $this->render($pageTemplate, $templateParams);
        }
    }

    /**
     * @Route("/account/edit-email", name="account_edit_email")
     * @Secure(roles="IS_AUTHENTICATED_REMEMBERED")
     */
    public function editEmailAction(Request $request)
    {
        /** @var $user User */
        $user = $this->getUser();

        $editEmailForm = $this->createForm(new UserEmailFormType(), $user);

        if ($request->isMethod('post')) {
            $editEmailForm->bind($request);
            if ($editEmailForm->isValid()) {
                $this->em->persist($user);
                $this->em->flush();
                $this->session->getFlashBag()->add('success', "Your email has been updated.");

                return $this->redirect($this->router->generate('account_overview', $this->ajaxHelper->getPjaxParameters()));
            }
        }

        $pageTemplate = ':Account:edit_email.html.twig';
        $formTemplate = ':Form:user_email.html.twig';
        $templateParams = array(
            'user' => $this->getUser(),
            'form' => $editEmailForm->createView(),
        );

        if ($request->isXmlHttpRequest()) {
            if ($editEmailForm->isBound()) {
                return $this->ajaxHelper->renderAjaxForm($formTemplate, $templateParams);
            } else {
                $url = $this->router->generate('account_edit_email');

                return $this->ajaxHelper->renderPjaxBlock($pageTemplate, $templateParams, $url, 'edit_account');
            }
        } else {
            return $this->render($pageTemplate, $templateParams);
        }
    }

    /**
     * @Route("/account/edit-password", name="account_edit_password")
     * @Secure(roles="IS_AUTHENTICATED_REMEMBERED")
     */
    public function editPasswordAction(Request $request)
    {
        /** @var $user User */
        $user = $this->getUser();

        $editPasswordForm = $this->createForm(new UserPasswordFormType(), $user);

        if ($request->isMethod('post')) {
            $editPasswordForm->bind($request);
            if ($editPasswordForm->isValid()) {
                $this->em->persist($user);
                $this->em->flush();
                $this->session->getFlashBag()->add('success', "Your password has been updated.");

                return $this->redirect($this->router->generate('account_overview', $this->ajaxHelper->getPjaxParameters()));
            }
        }

        $templateFile = ':Account:edit_password.html.twig';
        $templateParams = array(
            'user' => $this->getUser(),
            'form' => $editPasswordForm->createView(),
        );

        if ($request->isXmlHttpRequest()) {
            if ($editPasswordForm->isBound()) {
                return $this->ajaxHelper->renderAjaxForm(':Form:user_password.html.twig', $templateParams);
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
    public function editPictureAction(Request $request)
    {
        /** @var $user \Kurumi\MainBundle\Entity\User */
        $user = $this->getUser();
        $newPicture = new Picture();
        $oldPicture = $user->getPicture();

        $userPictureForm = $this->createForm(new UserPictureFormType(), $newPicture);

        if ($request->isMethod('post')) {
            $userPictureForm->bind($request);
            if ($userPictureForm->isValid()) {
                /** @var $em \Doctrine\ORM\EntityManager */
                $em = $this->getDoctrine()->getManager();
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
                /*
                 * @TODO implement asynchronous user picture refresh
                $pictureChangedMessage = new Message('pictureChanged');
                $pictureChangedMessage->setChannel(sprintf('user_%s', $user->getId()));
                $this->nodejsDispatcher->dispatch($pictureChangedMessage);
                */

            }
        }

        $templateFile = ':Account:edit_picture.html.twig';
        $formTemplate = ':Form:user_picture.html.twig';
        $modalFormTemplate = ':Form:user_picture_modal.html.twig';
        $templateParams = array(
            'user' => $this->getUser(),
            'form' => $userPictureForm->createView(),
        );

        if ($request->isXmlHttpRequest()) {
            if ($this->ajaxHelper->isModal()) {
                if ($userPictureForm->isBound()) {
                    if ($userPictureForm->isValid()) {
                        return $this->ajaxHelper->renderModal($modalFormTemplate, $templateParams);
                    } elseif ($userPictureForm->isBound()) {
                        return $this->ajaxHelper->renderAjaxForm($modalFormTemplate, $templateParams);
                    }
                } else {
                    return $this->ajaxHelper->renderModal($modalFormTemplate, $templateParams);
                }
            } else {
                if ($userPictureForm->isBound()) {
                    if ($userPictureForm->isValid()) {
                        return $this->redirect($this->router->generate('account_overview', $this->ajaxHelper->getPjaxParameters()));
                    } elseif ($userPictureForm->isBound()) {
                        return $this->ajaxHelper->renderAjaxForm($formTemplate, $templateParams);
                    }
                } else {
                    $url = $this->router->generate('account_edit_picture');

                    return $this->ajaxHelper->renderPjaxBlock($templateFile, $templateParams, $url, 'edit_account');
                }
            }
        } else {
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
    public function deletePictureAction($token, Request $request)
    {
        /** @var $user \Kurumi\MainBundle\Entity\User */
        $user = $this->getUser();
        /** @var $picture Picture */
        $picture = $user->getPicture();
        if ($picture !== null && $this->csrfProvider->isCsrfTokenValid('account_delete_picture', $token)) {
            /** @var $em \Doctrine\ORM\EntityManager */
            $em = $this->getDoctrine()->getManager();
            $user->setPicture(null);
            $em->remove($picture);
            $em->flush();
            $this->session->getFlashBag()->set('success', "Your picture was deleted.");
        }

        return $this->editPictureAction($request);
    }

    /**
     * @Route("/account/fill", name="account_fill", defaults={"id"=null})
     * @Secure(roles="IS_AUTHENTICATED_REMEMBERED")
     */
    public function fillAction($route, $id = null, Request $request)
    {
        /** @var $user User */
        $user = $this->getUser();
        if ($user->getProfile() === null) {
            $this->session->getFlashBag()->add('warning', "You don't have a profile. If you would like to create one, please fill in the form below.");
            $profile = new Profile();
            $user->setProfile($profile);
        } else {
            $this->session->getFlashBag()->add('warning', "Your profile is incomplete. Please fill in the information below.");
        }

        $createProfileForm = $this->createForm(new FillProfileFormType(), $user->getProfile());

        if ($request->isMethod('post')) {
            $createProfileForm->bind($request);
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

        if ($createProfileForm->isBound() && $createProfileForm->isValid()) {
            return $this->redirect($this->router->generate($route, $this->ajaxHelper->getPjaxParameters()));
        }

        $templateFile = ':Account:fill_profile_page.html.twig';
        $templateParams = array(
            'user' => $user,
            'form' => $createProfileForm->createView(),
            'active' => $activeRoute,
            'route' => $route,
        );

        if ($request->isXmlHttpRequest()) {
            if ($createProfileForm->isBound()) {
                return $this->ajaxHelper->renderAjaxForm(':Form:fill_profile.html.twig', $templateParams);
            } else {
                $url = $this->router->generate($route);

                return $this->ajaxHelper->renderPjaxBlock($templateFile, $templateParams, $url);
            }
        } else {
            return $this->render($templateFile, $templateParams);
        }
    }
}
