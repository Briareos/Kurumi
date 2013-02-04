<?php

namespace Kurumi\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UserLocaleFormType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'user_locale';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('locale', 'locale', []);
    }


}