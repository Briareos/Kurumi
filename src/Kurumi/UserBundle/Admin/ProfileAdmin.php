<?php

namespace Kurumi\UserBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Kurumi\UserBundle\Entity\User;

class ProfileAdmin extends Admin
{
    protected $baseRouteName = 'profile_admin';

    protected $baseRoutePattern = 'profile';

    protected $formOptions = array(
        'validation_groups' => array('admin'),
    );

    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('firstName')
            ->add('lastName')
            ->add('birthday')
            ->add('gender')
            ->add('created');
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $profile = $this->getSubject();
        $formMapper
            ->with("General")
            ->add('firstName', null, array(
            'required' => false,
        ))
            ->add('lastName', null, array(
            'required' => false,
        ))
            ->add('birthday', null, array(
            'required' => false,
            'empty_value' => array(
                'year' => '-',
                'month' => '-',
                'day' => '-'
            ),
            'years' => range(date('Y'), date('Y') - 100),
        ))
            ->add('gender', 'gender', array(
            'required' => false,
            'empty_value' => $this->trans('admin.gender.unspecified'),
        ))
            ->add('city', 'city', array(
            'required' => false,
        ))
            ->add('user', 'sonata_type_model', array(
        ))
            ->end();
    }

    public function getUsersWithoutProfile()
    {

    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('firstName')
            ->add('lastName')
            ->add('user')
            ->add('_actions', 'actions', array(
            'actions' => array(
                'view' => array(),
                'edit' => array(),
            )
        ));
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('firstName')
            ->add('lastName')
            ->add('gender');
    }
}