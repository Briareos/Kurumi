<?php

namespace Kurumi\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Kurumi\UserBundle\Entity\User;
use Kurumi\UserBundle\Entity\Profile;
use Kurumi\UserBundle\Entity\City;

class RegisterFormType extends AbstractType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'user_register';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'email', array(
        ));
        $builder->add('plainPassword', 'password', array(
        ));
        $builder->add('name', 'text', array(
        ));
        $builder->add('profile', new RegisterProfileType(), array(
        ));
        $builder->add('timezone', 'detect_timezone', array(
        ));
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view['email']->set('label', 'form.register.email');
        $view['plainPassword']->set('label', 'form.register.password');
        $view['name']->set('label', 'form.register.name');
        $view['profile']['birthday']->set('label', 'form.register.birthday.label');
        $view['profile']['birthday']['year']->set('empty_value', 'form.register.birthday.year');
        $view['profile']['birthday']['month']->set('empty_value', 'form.register.birthday.month');
        $view['profile']['birthday']['day']->set('empty_value', 'form.register.birthday.day');
        $view['profile']['gender']->set('label', 'form.register.gender.label');
        $view['profile']['gender']->set('empty_value', 'Your sex:');
        $genderChoices = $view['profile']['gender']->get('choices');
        $genderChoices[0]->label = 'form.register.gender.male';
        $genderChoices[1]->label = 'form.register.gender.female';
        $view['profile']['city']->set('label', 'form.register.city');
    }


    public function  setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kurumi\UserBundle\Entity\User',
            'validation_groups' => array('registration'),
        ));
    }


}