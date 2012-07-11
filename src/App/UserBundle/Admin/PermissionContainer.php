<?php

namespace App\UserBundle\Admin;

use Briareos\AclBundle\Security\Authorization\PermissionContainerInterface;

class PermissionContainer implements PermissionContainerInterface
{
    public function getPermissions()
    {
        return array(
            'admin' => array(
                '__children' => array(
                    'user' => array(
                        '__children' => array(
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