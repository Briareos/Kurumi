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
use Kurumi\UserBundle\Entity\UserManager;

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


    public function prePersist($user)
    {
        $this->userManager->updatePassword($user);
    }

    public function preUpdate($user)
    {
        $this->userManager->updatePassword($user);
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
            ->add('email', null, array(
            'attr' => array(
                'autocomplete' => 'off',
            ),
        ))
            ->add('plainPassword', 'password', array(
            'attr' => array(
                'autocomplete' => 'off',
            ),
            'label' => "Password",
            'required' => false,
        ));

        if ($user->getId()) {
            $formMapper->add('passwordClear', 'checkbox', array(
                'required' => false,
            ));
        }

        $formMapper
            ->add('name')
            ->add('timezone', 'timezone', array(
            'required' => false,
            'empty_value' => '-',
        ))
            ->add('aclRoles', null, array(
            'property' => 'name',
            'expanded' => true,
            'required' => false,
        ))
            ->end()
            ->with("Profile")
            ->add('profile', null, array(
        ), array(
            'edit' => 'list',
        ))->end();
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('email')
            ->add('name')
            ->add('profile', null, array(
            'template' => 'UserBundle:CRUD:list_profile.html.twig',
        ))
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
            ->add('name')
            ->add('email');
    }

    public function toString($object)
    {
        return $object->getUsername();
    }


}