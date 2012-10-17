<?php

namespace Kurumi\UserBundle\Twig\Extension;

use Kurumi\UserBundle\OAuth\OAuthProviderInterface;

class OAuthSettings extends \Twig_Extension
{
    /**
     * @var \Kurumi\UserBundle\OAuth\OAuthProviderInterface[]
     */
    private $providers;

    public function __construct(array $providers)
    {
        foreach ($providers as $provider) {
            if (!$provider instanceof OAuthProviderInterface) {
                throw new \InvalidArgumentException('OAuth providers must implement OAuthProviderInterface.');
            }
        }
        $this->providers = $providers;
    }

    function getName()
    {
        return 'oauth_settings';
    }

    public function getFunctions()
    {
        return array(
            'oauth_login_url' => new \Twig_Function_Method($this, 'getLoginUrl'),
        );
    }

    public function getLoginUrl($providerName, $returnUrl)
    {
        return $this->providers[$providerName]->getLoginUrl($returnUrl);
    }
}