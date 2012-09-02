<?php

namespace Kurumi\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraint\UserPassword;
use Symfony\Component\Form\FormBuilderInterface;

class UserPasswordFormType extends AbstractType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'user_password';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('currentPassword', 'password', array(
        ));
        $builder->add('plainPassword', 'repeated', array(
            'type' => 'password',
            'data' => '',
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'UserBundle:User',
            'validation_groups' => function(FormInterface $form)
            {
                /** @var $user \Kurumi\UserBundle\Entity\User */
                $user = $form->getData();
                if ($user->getPassword()) {
                    return 'edit_password';
                } else {
                    return 'set_password';
                }
            }
        ));
    }


}