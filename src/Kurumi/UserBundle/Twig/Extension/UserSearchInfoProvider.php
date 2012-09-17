<?php

namespace Kurumi\UserBundle\Twig\Extension;

use Kurumi\UserBundle\User\SearchInfoProvider;
use Kurumi\UserBundle\Entity\User;

class UserSearchInfoProvider extends \Twig_Extension
{
    private $searchInfoProvider;

    public function __construct(SearchInfoProvider $searchInfoProvider)
    {
        $this->searchInfoProvider = $searchInfoProvider;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    function getName()
    {
        return 'user_search_info_provider';
    }

    public function getFunctions()
    {
        return array(
            'user_search_info' => new \Twig_Function_Method($this, 'getSearchInfo'),
        );
    }

    public function getSearchInfo(User $user)
    {
        return $this->searchInfoProvider->getSearchInfo($user);
    }
}