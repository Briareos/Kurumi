<?php

namespace App\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use App\UserBundle\Form\Type\RegisterFormType;
use App\UserBundle\Entity\User;
use App\UserBundle\Entity\Profile;
use App\UserBundle\Entity\City;
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
        $defaultProfile = new Profile();
        $user->setProfile($defaultProfile);
        $defaultCity = new City();
        $defaultProfile->setCity($defaultCity);

        $form = $this->createForm(new RegisterFormType(), $user);

        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                /** @var $em \Doctrine\ORM\EntityManager */
                $em = $this->getDoctrine()->getManager();
                /** @var $cityManager \App\UserBundle\Entity\CityManager */
                $cityManager = $this->get('city_manager');
                $city = $cityManager->manageCity($user->getProfile()->getCity());
                if (!$city->getId()) {
                    $em->persist($city);
                } else {
                    $user->getProfile()->setCity($city);
                }
                /** @var $userManager \App\UserBundle\Entity\UserManager */
                $userManager = $this->get('user_manager');
                $userManager->updatePassword($user);
                $em->persist($user->getProfile());
                $em->persist($user);
                $em->flush();
                $this->authenticateUser($user);
            }
        }

        if ($request->isXmlHttpRequest()) {
            if ($form->isBound() && $form->isValid()) {
                $data['success'] = true;
            } elseif ($form->isBound()) {
                $data['form'] = array(
                    'id' => $form->getName(),
                    'body' => $this->renderView('UserBundle:Form:register_form.html.twig', array('form' => $form->createView())),
                    'success' => false,
                );
            } else {
                // @TODO implement GET request for the form?
            }
            return new Response(json_encode($data), 200, array(
                'Content-Type' => 'application/json',
            ));
        } else {
            if ($form->isBound() && $form->isValid()) {
                return $this->redirect($this->generateUrl('front'));
            } else {
                return $this->render('UserBundle:Register:register.html.twig', array(
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