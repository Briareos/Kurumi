<?php

namespace App\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use App\UserBundle\Form\Type\RegisterFormType;
use App\UserBundle\Entity\User;
use App\UserBundle\Entity\Profile;
use App\UserBundle\Entity\City;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RegisterController extends Controller
{

    /**
     * @Route("/register", name="register")
     * @Template()
     */
    public function registerAction(Request $request)
    {
        $defaultUser = new User();
        $defaultProfile = new Profile();
        $defaultCity = new City();
        $defaultProfile->setCity($defaultCity);
        $defaultUser->setProfile($defaultProfile);

        $form = $this->createForm(new RegisterFormType(), $defaultUser);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $user = $form->getData();

                $em = $this->getDoctrine()->getManager();
                $city = $this->get('city_manager')->manageCity($user->getProfile()->getCity());
                if(!$city->getId()) {
                    $em->persist($city);
                } else {
                    $user->getProfile()->setCity($city);
                }
                $em->persist($user->getProfile());
                $user->setPassword($this->encodePassword($user, $user->getPlainPassword()));
                $em->persist($user);
                $user->setActive(true);
                $em->flush();
                $this->authenticateUser($user);
                return new RedirectResponse($this->generateUrl('front'));
            }
        }

        if ($request->isXmlHttpRequest()) {
            $response = new Response(json_encode(array(
                'form' => array(
                    'id' => $form->getName(),
                    'body' => $this->render('UserBundle:Register:register_form.html.twig', array(
                        'form' => $form->createView(),
                    ))->getContent(),
                )
            )));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            return array(
                'form' => $form->createView(),
            );
        }
    }

    private function authenticateUser(UserInterface $user)
    {
        $providerKey = 'main';
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
        $this->get('security.context')->setToken($token);
    }

    private function encodePassword(UserInterface $user, $plainPassword) {
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        return $encoder->encodePassword($plainPassword, $user->getSalt());
    }

    private function facebookCheckAction() {
        $this->getUser();
    }
}