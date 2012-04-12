<?php

namespace App\UserBundle\Security\User\Provider;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use BaseFacebook;
use FacebookApiException;

class FacebookProvider implements UserProviderInterface {

    private $facebookApi;

    private $em;

    private $validator;

    public function __construct($facebookApi, $em, $validator) {
        $this->facebookApi = $facebookApi;
        $this->em = $em;
        $this->validator = $validator;
    }

    function loadUserByUsername($fbId)
    {
        return $this->em->getRepository('App\UserBundle\Entity\User')->findOneByFacebookId($fbId);
    }

    function refreshUser(UserInterface $user)
    {
        // TODO: Implement refreshUser() method.
    }

    function supportsClass($class)
    {
        // TODO: Implement supportsClass() method.
    }


}