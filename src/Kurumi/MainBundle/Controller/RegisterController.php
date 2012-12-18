<?php

namespace Kurumi\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Kurumi\MainBundle\Form\Type\RegisterFormType;
use Kurumi\MainBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Response;
use Briareos\AjaxBundle\Ajax;
use JMS\DiExtraBundle\Annotation as DI;

class RegisterController extends Controller
{

    /**
     * @DI\Inject("router")
     *
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    private $router;

    /**
     * @DI\Inject("doctrine.orm.default_entity_manager")
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @DI\Inject("security.context")
     *
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    private $securityContext;

    /**
     * @DI\Inject("templating.ajax.helper")
     *
     * @var \Briareos\AjaxBundle\Ajax\Helper
     */
    private $ajaxHelper;

    /**
     * @Route("/register", name="register")
     * @Method({"GET","POST"})
     */
    public function registerAction(Request $request)
    {
        $user = new User();

        $form = $this->createForm(new RegisterFormType(), $user);

        if ($request->isMethod('post')) {
            $form->bind($request);
            if ($form->isValid()) {
                $this->em->persist($user);
                $this->em->flush();
                $this->authenticateUser($user);
            }
        }

        $pageTemplateName = ':Page:Register:register.html.twig';
        $formTemplateName = ':Form:user_register.html.twig';
        $templateParams = [
            'form' => $form->createView(),
        ];

        if ($request->isXmlHttpRequest()) {
            if ($this->ajaxHelper->isModal()) {
                if ($form->isBound() && $form->isValid()) {
                    // @TODO Registration successful, decide what to do next
                } elseif ($form->isBound()) {
                    return $this->ajaxHelper->renderAjaxForm($formTemplateName, $templateParams);
                } else {
                    // This is a GET request.
                    return $this->ajaxHelper->renderModal($formTemplateName, $templateParams);
                }
            } else {
                if ($form->isBound() && $form->isValid()) {
                    return $this->redirect($this->router->generate('front'));
                } elseif ($form->isBound()) {
                    return $this->ajaxHelper->renderAjaxForm($formTemplateName, $templateParams);
                } else {
                    // @TODO implement if needed
                }
            }
        } else {
            if ($form->isBound() && $form->isValid()) {
                return $this->redirect($this->generateUrl('front'));
            } else {
                return $this->render($pageTemplateName, $templateParams);
            }
        }
    }

    private function authenticateUser(UserInterface $user)
    {
        $providerKey = $this->container->getParameter('main_firewall_name');
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
        $this->securityContext->setToken($token);
    }
}