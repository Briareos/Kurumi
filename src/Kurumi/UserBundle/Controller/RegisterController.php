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

class RegisterController extends Controller
{

    /**
     * @Route("/register", name="register")
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $cityToCityNameTransformer = $this->get('city_to_city_name_transformer');

        $form = $this->createForm(new RegisterFormType($cityToCityNameTransformer), $user);

        if ($request->isMethod('post')) {
            $form->bind($request);
            if ($form->isValid()) {
                /** @var $em \Doctrine\ORM\EntityManager */
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                $this->authenticateUser($user);
            }
        }

        if ($request->isXmlHttpRequest()) {
            $commands = array();
            if ($form->isBound() && $form->isValid()) {
                return $this->redirect($this->get('router')->generate('front'));
            } elseif ($form->isBound()) {
                $commands[] = new Ajax\Command\Form($this->renderView('UserBundle:Form:register_form.html.twig', array('form' => $form->createView())));
            } else {
                $commands[] = new Ajax\Command\Modal($this->renderView('UserBundle:Form:register_form.html.twig', array('form' => $form->createView())));
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
        $this->get('security.context')->setToken($token);
    }

    /**
     * @Route("/facebook_check", name="facebook_check")
     */
    private function facebookCheckAction()
    {
        $this->getUser();
    }
}