<?php

namespace Kurumi\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{

    /**
     * @Route("/payment/{$payment}", name="payment")
     */
    public function paymentAction()
    {
        $availablePayments = array(
            'fortumo',
            'paypal',
        );
        $defaultPayment = 'fortumo';

        if ($this->getRequest()->isXmlHttpRequest()) {
            $data = array();
            $data['modal'] = array(
                'body' => $this->renderView('UserBundle:Payment:payment.html.twig', array()),
            );
            return new Response(json_encode($data), 200, array(
                'Content-Type' => 'application/json',
            ));
        }
    }

    public function gatewayFortumoAction()
    {

    }
}
