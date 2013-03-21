<?php

namespace Kurumi\MainBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Kurumi\MainBundle\Entity\User;

class ProfileAdmin extends Admin
{
    protected $baseRouteName = 'profile_admin';

    protected $baseRoutePattern = 'profile';

    protected $formOptions = [
        'validation_groups' => ['admin'],
    ];

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
        $userQuery = $this
            ->getModelManager()
            ->getEntityManager($this->getClass())
            ->createQuery('Select p From KurumiMainBundle:Profile p Where p.user Is Null Order By p.id Asc');

        $formMapper
            ->with("General")
            ->add(
                'firstName',
                null,
                [
                    'required' => false,
                ]
            )
            ->add(
                'lastName',
                null,
                [
                    'required' => false,
                ]
            )
            ->add(
                'birthday',
                'birthday',
                [
                    'required' => false,
                ]
            )
            ->add(
                'gender',
                'gender',
                [
                    'required' => false,
                    'empty_value' => $this->trans('admin.gender.unspecified'),
                ]
            )
            ->add(
                'city',
                'city',
                [
                    'required' => false,
                ]
            )
            ->add(
                'user',
                'sonata_type_model',
                []
            )
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
            ->add(
                '_actions',
                'actions',
                [
                    'actions' => [
                        'view' => [],
                        'edit' => [],
                    ]
                ]
            );
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('firstName')
            ->add('lastName')
            ->add('gender');
    }
}
