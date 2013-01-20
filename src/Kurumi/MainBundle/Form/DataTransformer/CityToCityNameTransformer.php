<?php

namespace Kurumi\MainBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Kurumi\MainBundle\CityFinder\CityNotFoundException;
use Kurumi\MainBundle\Entity\City;
use Kurumi\MainBundle\CityFinder\CityFinderInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\ORM\EntityManager;

class CityToCityNameTransformer implements DataTransformerInterface
{

    private $cityClass;

    private $repository;

    private $cityFinder;

    public function __construct(EntityManager $em, $cityClass, CityFinderInterface $cityFinder)
    {
        $this->cityClass = $cityClass;
        $this->repository = $em->getRepository($this->cityClass);
        $this->cityFinder = $cityFinder;
    }

    /**
     * {@inheritdoc}
     */
    function transform($value)
    {
        if (!$value instanceof $this->cityClass) {
            return $value;
        }

        /** @var $value \Kurumi\MainBundle\Entity\City */
        if (!$value->getName() || !$value->getCountryName()) {
            return null;
        }
        if ($value->getCountryCode() === 'US' || $value->getCountryCode() === 'CA') {
            return sprintf('%s, %s, %s', $value->getName(), $value->getState(), $value->getCountryCode());
        } else {
            return sprintf('%s, %s', $value->getName(), $value->getCountryName());
        }
    }

    /**
     * {@inheritdoc}
     */
    function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }

        try {
            $city = $this->cityFinder->find(new City(), $value);
        } catch (CityNotFoundException $e) {
            return null;
        }

        if ($existingCity = $this->repository->findOneBy(
            array(
                'latitude' => $city->getLatitude(),
                'longitude' => $city->getLongitude(),
            )
        )
        ) {
            return $existingCity;
        }

        return $city;
    }


}