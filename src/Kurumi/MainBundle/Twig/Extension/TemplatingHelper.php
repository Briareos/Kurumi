<?php

namespace Kurumi\MainBundle\Twig\Extension;

use Symfony\Component\Security\Core\SecurityContextInterface;

class TemplatingHelper extends \Twig_Extension
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * @var \Kurumi\MainBundle\Entity\User|null
     */
    private $user;

    function __construct()
    {
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'templating_helper';
    }

    public function getGlobals()
    {
        return array(
        );
    }
}