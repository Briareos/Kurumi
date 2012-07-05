<?php

namespace App\UserBundle\Admin;

use Briareos\AclBundle\Security\Authorization\PermissionContainerInterface;

class PermissionContainer implements PermissionContainerInterface
{
    public function getPermissions()
    {
        return array(
            'user' => array(
                'children' => array(
                    'list' => array(),
                    'create' => array(),
                    'edit' => array(),
                    'delete' => array(),
                ),
            ),
        );
    }

}