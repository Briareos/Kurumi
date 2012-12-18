<?php

namespace Kurumi\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
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
        /** @var $user \Kurumi\MainBundle\Entity\User */
        $user = $options['data'];
        if ($user->getPassword() !== null) {
            $builder->add('currentPassword', 'password', array());
        }
        $builder->add('plainPassword', 'repeated', array(
            'type' => 'password',
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'Kurumi\MainBundle\Entity\User',
            'validation_groups' => function (FormInterface $form) {
                /** @var $user \Kurumi\MainBundle\Entity\User */
                $user = $form->getData();
                if ($user->getPassword() === null) {
                    return 'set_password';
                } else {
                    return 'edit_password';
                }
            }
        ));
    }


}