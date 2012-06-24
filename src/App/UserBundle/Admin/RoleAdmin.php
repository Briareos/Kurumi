<?php

namespace App\UserBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use App\UserBundle\Entity\User;

class RoleAdmin extends Admin
{

    protected $baseRouteName = 'role_admin';

    protected $baseRoutePattern = 'role';

    protected $formOptions = array(
        'validation_groups' => array('admin'),
    );

    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name');
    }

    public function configureFormFields(FormMapper $form)
    {
        $form
            ->with("General")
            ->add('name')
            ->end();
    }

    public function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('name')
            ->add('_actions', 'actions', array(
            'actions' => array(
                'view' => array(),
                'edit' => array(),
            )
        ));
    }


}