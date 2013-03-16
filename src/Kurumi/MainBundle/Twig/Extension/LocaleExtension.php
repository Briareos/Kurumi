<?php

namespace Kurumi\MainBundle\Twig\Extension;


class LocaleExtension extends \Twig_Extension
{
    protected $enabledLocales;

    function __construct(array $enabledLocales)
    {
        $this->enabledLocales = $enabledLocales;
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'locale';
    }

    public function getGlobals()
    {
        return [
            'enabled_locales' => $this->enabledLocales,
        ];
    }

    public function getFunctions()
    {
        return [
            'locale_name' => new \Twig_Function_Method($this, 'localeName'),
        ];
    }

    public function localeName($locale, $inLocale = null)
    {
        return \Locale::getDisplayLanguage($locale, $inLocale);
    }
}
