<?php

namespace Kurumi\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Kurumi\UserBundle\Form\Type\RegisterFormType;
use Kurumi\UserBundle\Entity\User;
use Kurumi\UserBundle\Entity\Profile;
use Kurumi\UserBundle\Entity\City;
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
     * @Route("/register", name="register")
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

        if ($request->isXmlHttpRequest()) {
            $commands = new Ajax\CommandContainer();
            if ($form->isBound() && $form->isValid()) {
                return $this->redirect($this->router->generate('front'));
            } elseif ($form->isBound()) {
                $commands->add(new Ajax\Command\Form($this->renderView('UserBundle:Form:register_form.html.twig', array('form' => $form->createView()))));
            } else {
                $commands->add(new Ajax\Command\Modal($this->renderView('UserBundle:Form:register_form.html.twig', array('form' => $form->createView()))));
            }
            return new Ajax\Response($commands);
        } else {
            if ($form->isBound() && $form->isValid()) {
                return $this->redirect($this->generateUrl('front'));
            } else {
                return $this->render('UserBundle:Register:register_page.html.twig', array(
                    'form' => $form->createView(),
                ));
            }
        }
    }

    private function authenticateUser(UserInterface $user)
    {
        $providerKey = $this->container->getParameter('main_firewall_name');
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
        $this->securityContext->setToken($token);
    }

    /**
     * @Route("/facebook_check", name="facebook_check")
     */
    private function facebookCheckAction()
    {
        $this->getUser();
    }
}