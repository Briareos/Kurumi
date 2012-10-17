<?php

namespace Kurumi\UserBundle\OAuth;

interface OAuthProviderInterface
{
    public function getLoginUrl($returnUrl);
}