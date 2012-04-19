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

class UserAdmin extends Admin
{
    protected $baseRouteName = 'user_admin';

    protected $baseRoutePattern = 'user';

    protected $formOptions = array(
        'validation_groups' => array('admin'),
    );

    public function prePersist($profile)
    {
        if ($profile->getId()) {
        } else {
            if ($this->getPlainPassword()) {
                $profile->setPassword($this->encodePassword($profile, $profile->getPlainPassword()));
            }
        }
    }

    private function encodePassword(UserInterface $user, $plainPassword)
    {
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        return $encoder->encodePassword($plainPassword, $user->getSalt());
    }


    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('email')
            ->add('active')
            ->add('timezone')
            ->add('created');
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $user = $this->getSubject();
        $formMapper
            ->with("General")
            ->add('email')
            ->add('plainPassword', 'password', array(
            'label' => "Password",
            'required' => false,
        ))
            ->add('name')
            ->add('active', null, array('required' => false))
            ->end();
        $profile = $user->getProfile();
        $formMapper->with("Profile")
            ->add('profile', 'sonata_type_model', array(
        ), array(
            'edit' => 'list',
        ));
    }

    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('email')
            ->add('name')
            ->add('active')
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
            ->add('email')
            ->add('active');
    }
}