<?php

namespace Kurumi\MainBundle\OAuth\Login;

use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Kurumi\MainBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;

class UserProvider implements OAuthAwareUserProviderInterface
{
    const LOGGED_IN_AS_DIFFERENT_USER = 'logged_in_as_different_user';

    const EMAIL_NOT_VERIFIED = 'email_not_verified';

    /**
     * @var AbstractUserProvider[]
     */
    protected $providers = array();

    protected $em;

    protected $container;

    public function __construct(array $providers, EntityManager $em, ContainerInterface $container)
    {
        $this->providers = $providers;
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * @return \Symfony\Component\Security\Core\SecurityContextInterface
     */
    public function getSecurityContext()
    {
        return $this->container->get('security.context');
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $resourceOwnerName = $response->getResourceOwner()->getName();
        $provider = $this->providers[$resourceOwnerName];
        $appUser = $this->getUser();
        $guessedUser = $provider->findUserByResponse($response);
        $oauthUser = $provider->findOAuthUserByResponse($response);

        if ($appUser instanceof User && $oauthUser instanceof User) {
            if ($appUser->getId() === $oauthUser->getId()) {
                return $appUser;
            } else {
                throw new AuthenticationException(self::LOGGED_IN_AS_DIFFERENT_USER);
            }
        } elseif ($oauthUser instanceof User) {
            return $oauthUser;
        } elseif ($appUser instanceof User) {
            $provider->createOAuth($response, $appUser);

            return $appUser;
        } elseif ($guessedUser instanceof User) {
            if ($provider->isVerifiedEmail($response)) {
                $provider->createOAuth($response, $guessedUser);

                return $guessedUser;
            } else {
                throw new AuthenticationException(self::EMAIL_NOT_VERIFIED);
            }
        } else {
            $user = $provider->createUser($response);
            $provider->createOAuth($response, $user);

            return $user;
        }
    }

    private function getUser()
    {
        $token = $this->getSecurityContext()->getToken();
        if ($token !== null) {
            $user = $token->getUser();
        } else {
            $user = null;
        }

        return $user;
    }

}