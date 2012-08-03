<?php

namespace Kurumi\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Application\Sonata\MediaBundle\Entity\Media;
use Kurumi\UserBundle\Form\Type\UserPictureFormType;

class PictureController extends Controller
{
    /**
     * @Route("upload-picture", name="upload_picture")
     */
    public function uploadPictureAction()
    {
        /** @var $user \Kurumi\UserBundle\Entity\User */
        $user = $this->getUser();
        $request = $this->getRequest();

        $form = $this->createForm(new UserPictureFormType(), $user);

        if ($request->isMethod('POST')) {

            $form->bind($request);

            if ($form->isValid()) {
                /** @var $em \Doctrine\ORM\EntityManager */
                $em = $this->getDoctrine()->getManager();
                $user = $form->getData();
                $userPicture = $user->getPicture();
                $userPicture->setContext('user_picture');
                $em->persist($userPicture);
                $em->persist($user);
                $em->flush();
            }
        }

        if ($request->isXmlHttpRequest()) {
            if ($form->isBound() && $form->isValid()) {
                $data['modal'] = array(
                    'body' => $this->renderView('UserBundle:Form:user_picture_form.html.twig', array(
                        'form' => $form->createView(),
                        'user' => $user,
                        'picture' => $user->getPicture(),
                    )),
                );
            } elseif ($form->isBound()) {
                $data['form'] = array(
                    'body' => $this->renderView('UserBundle:Form:user_picture_form.html.twig', array(
                        'form' => $form->createView(),
                        'user' => $user,
                        'picture' => $user->getPicture(),
                    )),
                );
            } else {
                $data['modal'] = array(
                    'body' => $this->renderView('UserBundle:Form:user_picture_form.html.twig', array(
                        'form' => $form->createView(),
                        'user' => $user,
                        'picture' => $user->getPicture(),
                    )),
                );
            }
            return new Response(json_encode($data), 200, array(
                'Content-Type' => 'application/json',
            ));
        } else {
            if ($form->isBound() && $form->isValid()) {
                return $this->redirect($this->generateUrl('front'));
            } else {
                return $this->render('UserBundle:Form:user_picture_form.html.twig', array(
                    'form' => $form->createView(),
                    'user' => $user,
                    'picture' => $user->getPicture(),
                ));
            }
        }
    }
}