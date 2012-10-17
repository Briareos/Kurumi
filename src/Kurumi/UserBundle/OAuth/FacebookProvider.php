<?php

namespace Kurumi\UserBundle\OAuth;

use Kurumi\UserBundle\OAuth\OAuthProviderInterface;

class FacebookProvider implements OAuthProviderInterface
{
    private $facebook;

    public function __construct(\Facebook $facebook)
    {
        $this->facebook = $facebook;
    }

    public function getLoginUrl($returnUrl)
    {
        $this->facebook->getLoginUrl(array(
            'scope' => array('email'),
            'redirect_uri' => $returnUrl
        ));
    }

}