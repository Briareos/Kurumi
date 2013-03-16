<?php

namespace Kurumi\MainBundle\EventListener;


use Doctrine\ORM\EntityManager;
use Kurumi\MainBundle\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class LocaleListener implements EventSubscriberInterface
{
    private $em;

    function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Sets the request's locale to session's _locale key.
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->getRequestType() !== HttpKernel::MASTER_REQUEST) {
            return;
        }

        $request = $event->getRequest();
        $session = $request->getSession();

        if ($session === null || !$session->has('_locale')) {
            return;
        }

        $locale = $session->get('_locale');
        $request->setLocale($locale);
    }

    /**
     * If the user has set a session _locale variable, persist it to the database.
     * Else, try to pull the locale from his entity and save it to his session.
     *
     * @param \Symfony\Component\Security\Http\Event\InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        if (!$user instanceof User) {
            return;
        }

        $session = $event->getRequest()->getSession();

        if ($session === null) {
            return;
        }

        if ($session->has('_locale')) {
            $currentLocale = $session->get('_locale');
            $savedLocale = $user->getLocale();
            if ($currentLocale === $savedLocale) {
                $user->setLocale($currentLocale);
                $this->em->persist($user);
                $this->em->flush();
            }
        } elseif ($user->getLocale() !== null) {
            $session->set('_locale', $user->getLocale());
        }


    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            // Just after \Symfony\Component\HttpKernel\EventListener\LocaleListener (priority 16), which pulls
            // the locale from route.
            KernelEvents::REQUEST => [['onKernelRequest', 15]],
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
        ];
    }


}
