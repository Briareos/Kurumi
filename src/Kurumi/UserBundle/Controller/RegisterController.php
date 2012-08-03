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

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                /** @var $em \Doctrine\ORM\EntityManager */
                $em = $this->getDoctrine()->getManager();
                if ($user->getProfile()->getCity() !== null) {
                    $em->persist($user->getProfile()->getCity());
                }
                $em->persist($user->getProfile());
                $em->persist($user);
                $em->flush();
                $this->authenticateUser($user);
            }
        }

        if ($request->isXmlHttpRequest()) {
            if ($form->isBound() && $form->isValid()) {
                $data['location'] = array(
                    'url' => $this->generateUrl('front'),
                    'replace' => true,
                );
            } elseif ($form->isBound()) {
                $data['form'] = array(
                    'id' => $form->getName(),
                    'body' => $this->renderView('UserBundle:Form:register_form.html.twig', array('form' => $form->createView())),
                );
            } else {
                $data['modal'] = array(
                    'body' => $this->renderView('UserBundle:Form:register_form.html.twig', array('form' => $form->createView())),
                );
            }
            return new Response(json_encode($data), 200, array(
                'Content-Type' => 'application/json',
            ));
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
        $providerKey = $this->container->getParameter('user.firewall_name');
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