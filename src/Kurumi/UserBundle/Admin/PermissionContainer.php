<?php

namespace Kurumi\UserBundle\Admin;

use Briareos\AclBundle\Security\Authorization\PermissionContainerInterface;

class PermissionContainer implements PermissionContainerInterface
{
    public function getPermissions()
    {
        return array(
            'admin' => array(
                '__children' => array(
                    'userbundle_user' => array(
                        'weight' => 1,
                        '__children' => array(
                            'create' => array(),
                            'list' => array(),
                            'edit' => array(),
                            'delete' => array(),
                        ),
                    ),
                    'userbundle_profile' => array(
                        'weight' => 2,
                        '__children' => array(
                            'create' => array(),
                            'list' => array(),
                            'edit' => array(),
                            'delete' => array(),
                        ),
                    ),
                ),
            ),
        );
    }

}