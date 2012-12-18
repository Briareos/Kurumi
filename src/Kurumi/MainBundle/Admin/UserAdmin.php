<?php

namespace Kurumi\MainBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Kurumi\MainBundle\Manager\UserManager;

class UserAdmin extends Admin
{
    protected $baseRouteName = 'user_admin';

    protected $baseRoutePattern = 'user';

    /**
     * @var UserManager
     */
    protected $userManager;

    protected $formOptions = array(
        'validation_groups' => array('admin'),
    );

    /**
     * @param UserManager
     */
    public function setUserManager(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
          ->add('name')
          ->add('email')
          ->add('timezone')
          ->add('created');
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $user = $this->getSubject();
        $formMapper
          ->with("General")
          ->add(
            'email',
            null,
            array(
                'attr' => array(
                    'autocomplete' => 'off',
                ),
            )
        )
          ->add(
            'plainPassword',
            'password',
            array(
                'attr' => array(
                    'autocomplete' => 'off',
                ),
                'label' => "Password",
                'required' => false,
            )
        );

        if ($user->getId()) {
            $formMapper->add(
                'passwordClear',
                'checkbox',
                array(
                    'required' => false,
                )
            );
        }

        $formMapper
          ->add('name')
          ->add(
            'timezone',
            'timezone',
            array(
                'required' => false,
                'empty_value' => '-',
            )
        )
          ->end();
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
          ->addIdentifier('email')
          ->add('name')
          ->add(
            'profile',
            null,
            array(
                'template' => ':UserCRUD:list_profile.html.twig',
            )
        )
          ->add(
            'oauth',
            null,
            array(
                'template' => ':UserCRUD:list_oauth.html.twig'
            )
        )
          ->add(
            '_actions',
            'actions',
            array(
                'actions' => array(
                    'view' => array(),
                    'edit' => array(),
                )
            )
        );
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
          ->add('name')
          ->add('email');
    }

    public function toString($object)
    {
        return $object->getUsername();
    }


}