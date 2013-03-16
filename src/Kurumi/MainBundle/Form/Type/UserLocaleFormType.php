<?php

namespace Kurumi\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UserLocaleFormType extends AbstractType
{
    private $locale;

    private $locales;

    function __construct($locale, array $locales)
    {
        $this->locale = $locale;
        $this->locales = $locales;
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'user_locale';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locales = [];
        foreach ($this->locales as $locale) {
            $locales[$locale] = \Locale::getDisplayLanguage($locale, $locale);
        }
        $builder->add(
            'locale',
            'choice',
            [
                'choices' => $locales,
            ]
        );
    }


}
