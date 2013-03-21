<?php

namespace Kurumi\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Briareos\AjaxBundle\Ajax;

class PaymentController extends Controller
{

    /**
     * @Route("/payment/{$payment}", name="payment")
     */
    public function paymentAction()
    {
        $availablePayments = [
            'fortumo',
            'paypal',
        ];
        $defaultPayment = 'fortumo';

        if ($this->getRequest()->isXmlHttpRequest()) {
            $commands = new Ajax\CommandContainer();
            $commands->add(new Ajax\Command\Modal($this->renderView(':Payment:payment.html.twig', [])));
            return new Ajax\Response($commands);
        }
    }

    public function gatewayFortumoAction()
    {

    }
}
