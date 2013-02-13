<?php

namespace Kurumi\MainBundle\Controller;

use Kurumi\MainBundle\Form\Type\UserLocaleFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kurumi\MainBundle\Entity\Picture;
use Symfony\Component\HttpFoundation\Request;
use Kurumi\MainBundle\Entity\User;
use Kurumi\MainBundle\Entity\Profile;
use Briareos\AjaxBundle\Ajax;
use JMS\DiExtraBundle\Annotation\Inject;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Kurumi\MainBundle\Form\Type\UserNameFormType;
use Kurumi\MainBundle\Form\Type\FillProfileFormType;
use Kurumi\MainBundle\Form\Type\UserPasswordFormType;
use Kurumi\MainBundle\Form\Type\UserEmailFormType;
use Kurumi\MainBundle\Form\Type\PictureFormType;

class AccountController extends Controller
{
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
     * @var \Symfony\Component\HttpFoundation\Session\Session
     *
     * @Inject("session")
     */
    private $session;

    /**
     * @var \Symfony\Component\Routing\Router
     *
     * @Inject("router")
     */
    private $router;

    /**
     * @var \Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface
     *
     * @Inject("form.csrf_provider")
     */
    private $csrfProvider;

    /**
     * @var \Briareos\NodejsBundle\Nodejs\DispatcherInterface
     *
     * @Inject("nodejs.dispatcher")
     */
    private $nodejsDispatcher;

    /**
     * @Route("/account", name="account_overview")
     */
    public function overviewAction(Request $request)
    {
        /** @var $user User */
        $user = $this->getUser();
        $profile = $user->getProfile();

        $templateFile = ':Account:overview.html.twig';
        $templateParams = array(
            'profile' => $profile,
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
        $profile = $user->getProfile();

        $userNameForm = $this->createForm(new UserNameFormType(), $user);

        if ($request->isMethod('post')) {
            $userNameForm->bind($request);
            if ($userNameForm->isValid()) {
                $this->em->persist($user);
                $this->em->flush();
                $this->session->getFlashBag()->add('success', "Your name was successfully changed.");
                $url = $this->router->generate('account_overview', $this->ajaxHelper->getPjaxParameters());

                return $this->redirect($url);
            }
        }

        $pageTemplate = ':Account:edit_name.html.twig';
        $formTemplate = ':Form:user_name.html.twig';
        $templateParams = array(
            'profile' => $profile,
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
        $profile = $user->getProfile();

        $editEmailForm = $this->createForm(new UserEmailFormType(), $user);

        if ($request->isMethod('post')) {
            $editEmailForm->bind($request);
            if ($editEmailForm->isValid()) {
                $this->em->persist($user);
                $this->em->flush();
                $this->session->getFlashBag()->add('success', "Your email has been updated.");
                $url = $this->router->generate('account_overview', $this->ajaxHelper->getPjaxParameters());
                return $this->redirect($url);
            }
        }

        $pageTemplate = ':Account:edit_email.html.twig';
        $formTemplate = ':Form:user_email.html.twig';
        $templateParams = array(
            'profile' => $profile,
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
        $profile = $user->getProfile();

        $editPasswordForm = $this->createForm(new UserPasswordFormType(), $user);

        if ($request->isMethod('post')) {
            $editPasswordForm->bind($request);
            if ($editPasswordForm->isValid()) {
                $this->em->persist($user);
                $this->em->flush();
                $this->session->getFlashBag()->add('success', "Your password has been updated.");
                return $this->redirect(
                    $this->router->generate('account_overview', $this->ajaxHelper->getPjaxParameters())
                );
            }
        }

        $templateFile = ':Account:edit_password.html.twig';
        $templateParams = array(
            'profile' => $profile,
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
        $profile = $user->getProfile();
        $newPicture = new Picture();
        $newPicture->setPictureType(Picture::PROFILE_PICTURE);
        $newPicture->setProfile($profile);
        $oldPicture = $profile->getPicture();

        $profilePictureForm = $this->createForm(new PictureFormType(), $newPicture);

        if ($request->isMethod('post')) {
            $profilePictureForm->bind($request);
            if ($profilePictureForm->isValid()) {
                /** @var $em \Doctrine\ORM\EntityManager */
                $em = $this->getDoctrine()->getManager();
                $profile->setPicture($newPicture);
                // Picture persist is cascaded.
                $em->persist($profile);
                if ($oldPicture === null) {
                    $this->session->getFlashBag()->add('success', "Your profile picture has been set.");
                } else {
                    // No need to remove the picture, let it stay in the album.
                    // $em->remove($oldPicture);
                    $this->session->getFlashBag()->add('success', "Your profile picture has been updated.");
                }
                /*
                 * @TODO implement asynchronous user picture refresh
                $pictureChangedMessage = new Message('pictureChanged');
                $pictureChangedMessage->setChannel(sprintf('user_%s', $user->getId()));
                $this->nodejsDispatcher->dispatch($pictureChangedMessage);
                */
                $em->flush();
            }
        }

        $templateFile = ':Account:edit_picture.html.twig';
        $modalTemplateFile = ':Account:edit_picture_modal.html.twig';
        $formTemplateFile = ':Form:profile_picture.html.twig';
        $modalFormTemplateFile = ':Form:profile_picture_modal.html.twig';
        $templateParams = array(
            'profile' => $profile,
            'form' => $profilePictureForm->createView(),
        );

        if ($request->isXmlHttpRequest()) {
            if ($this->ajaxHelper->isModal()) {
                if ($profilePictureForm->isBound() && $profilePictureForm->isValid()) {
                    return $this->ajaxHelper->renderModal($modalTemplateFile, $templateParams);
                } elseif ($profilePictureForm->isBound()) {
                    return $this->ajaxHelper->renderAjaxForm($modalFormTemplateFile, $templateParams);
                } else {
                    return $this->ajaxHelper->renderModal($modalTemplateFile, $templateParams);
                }
            } else {
                if ($profilePictureForm->isBound() && $profilePictureForm->isValid()) {
                    $url = $this->router->generate('account_overview', $this->ajaxHelper->getPjaxParameters());

                    return $this->redirect($url);
                } elseif ($profilePictureForm->isBound()) {
                    return $this->ajaxHelper->renderAjaxForm($formTemplateFile, $templateParams);
                } else {
                    $url = $this->router->generate('account_edit_picture');

                    return $this->ajaxHelper->renderPjaxBlock(
                        $templateFile,
                        $templateParams,
                        $url,
                        'account_edit_picture'
                    );
                }
            }
        } else {
            if ($profilePictureForm->isBound() && $profilePictureForm->isValid()) {
                $url = $this->generateUrl('account_overview');

                return $this->redirect($url);
            } else {
                return $this->render($templateFile, $templateParams);
            }
        }
    }

    /**
     * @Route("/account/edit-locale", name="account_edit_locale")
     */
    public function editLocaleAction(Request $request)
    {
        /** @var $user User */
        $user = $this->getUser();
        $profile = $user->getProfile();

        $localeForm = $this->createForm(new UserLocaleFormType(), $user);

        if ($request->isMethod('post')) {
            $localeForm->bind($request);

            if ($localeForm->isValid()) {
                $this->em->persist($user);
                $this->em->flush();
            }
        }

        $pageTemplate = ':Account:edit_locale.html.twig';
        $formTemplate = ':Form:user_locale.html.twig';
        $templateParams = [
            'profile' => $profile,
            'form' => $localeForm->createView(),
        ];

        if ($request->isXmlHttpRequest()) {
            if ($localeForm->isBound() && $localeForm->isValid()) {
                $url = $this->generateUrl('account_overview');

                return $this->redirect($url);
            } elseif ($localeForm->isBound()) {
                return $this->ajaxHelper->renderAjaxForm($formTemplate, $templateParams);
            } else {
                $url = $this->generateUrl('account_edit_locale');

                return $this->ajaxHelper->renderPjaxBlock($pageTemplate, $templateParams, $url, 'edit_account');
            }
        } else {
            if ($localeForm->isBound() && $localeForm->isValid()) {
                $url = $this->generateUrl('account_overview');

                return $this->redirect($url);
            } else {
                return $this->render($pageTemplate, $templateParams);
            }
        }
    }

    /**
     * @Route("/account/delete-picture/{token}", name="account_delete_picture")
     */
    public function deletePictureAction($token, Request $request, $_route)
    {
        /** @var $user \Kurumi\MainBundle\Entity\User */
        $user = $this->getUser();
        $profile = $user->getProfile();

        /** @var $picture Picture */
        $picture = $profile->getPicture();
        if ($picture !== null && $this->csrfProvider->isCsrfTokenValid($_route, $token)) {
            /** @var $em \Doctrine\ORM\EntityManager */
            $em = $this->getDoctrine()->getManager();
            $profile->removePicture();

            // Currently, Vich\UploaderBundle\Storage\AbstractStorage gets the uri property by using \ReflectionClass,
            // which bypasses doctrine proxy lazy-loading. Calling any method but getId() would make sure the entity is
            // fully loaded.
            // @TODO find an alternative to force the lazy-loading of the doctrine proxy
            $picture->getUri();

            $em->persist($profile);
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
        $profile = $user->getProfile();

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

        if (!$createProfileForm->isBound()) {
            if ($profile === null) {
                $this->session->getFlashBag()->add(
                    'warning',
                    "You don't have a profile. If you would like to create one, please fill in the form below."
                );
                $profile = new Profile();
                $user->setProfile($profile);
            } else {
                $this->session->getFlashBag()->add(
                    'warning',
                    "Your profile is incomplete. Please fill in the information below."
                );
            }
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
