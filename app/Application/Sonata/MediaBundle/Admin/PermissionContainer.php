<?php

namespace Application\Sonata\MediaBundle\Admin;

use Briareos\AclBundle\Security\Authorization\PermissionContainerInterface;

class PermissionContainer implements PermissionContainerInterface
{
    public function getPermissions()
    {
        return array(
            'admin' => array(
                '__children' => array(
                    'applicationsonatamediabundle_media' => array(
                        'weight' => 11,
                        '__children' => array(
                            'create' => array(),
                            'list' => array(),
                            'edit' => array(),
                            'delete' => array(),
                        ),
                    ),
                    'applicationsonatamediabundle_gallery' => array(
                        'weight' => 12,
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