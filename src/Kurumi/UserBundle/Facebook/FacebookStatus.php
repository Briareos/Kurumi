<?php

namespace Kurumi\UserBundle\Facebook;

use Kurumi\UserBundle\Entity\User;
use Facebook;

class FacebookStatus
{
    private $user;

    private $facebook;

    public function __construct(User $user, Facebook $facebook)
    {
        $this->user = $user;
        $this->facebook = $facebook;
    }

    public function hasSession()
    {
        return (bool)$this->facebook->getUser();
    }

    public function isConnected()
    {
        return (bool)$this->user->getFacebook();
    }
}