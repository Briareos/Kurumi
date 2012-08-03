<?php

namespace Kurumi\UserBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\ORM\EntityManager;

class CityToCityNameTransformer implements DataTransformerInterface
{

    private $cityClass;

    private $repository;

    private $appid;

    public function __construct(EntityManager $em, $cityClass, $appid)
    {
        $this->cityClass = $cityClass;
        $this->repository = $em->getRepository($this->cityClass);
        $this->appid = $appid;
    }

    /**
     * Transforms a value from the original representation to a transformed representation.
     *
     * This method is called on two occasions inside a form field:
     *
     * 1. When the form field is initialized with the data attached from the datasource (object or array).
     * 2. When data from a request is bound using {@link Field::bind()} to transform the new input data
     *    back into the renderable format. For example if you have a date field and bind '2009-10-10' onto
     *    it you might accept this value because its easily parsed, but the transformer still writes back
     *    "2009/10/10" onto the form field (for further displaying or other purposes).
     *
     * This method must be able to deal with empty values. Usually this will
     * be NULL, but depending on your implementation other empty values are
     * possible as well (such as empty strings). The reasoning behind this is
     * that value transformers must be chainable. If the transform() method
     * of the first value transformer outputs NULL, the second value transformer
     * must be able to process that value.
     *
     * By convention, transform() should return an empty string if NULL is
     * passed.
     *
     * @param  mixed $value              The value in the original representation
     *
     * @return mixed                     The value in the transformed representation
     *
     * @throws UnexpectedTypeException   when the argument is not a string
     * @throws TransformationFailedException  when the transformation fails
     */
    function transform($value)
    {
        if (!$value instanceof $this->cityClass) {
            return $value;
        }

        /** @var $value \Kurumi\UserBundle\Entity\City */
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
     * Transforms a value from the transformed representation to its original
     * representation.
     *
     * This method is called when {@link Field::bind()} is called to transform the requests tainted data
     * into an acceptable format for your data processing/model layer.
     *
     * This method must be able to deal with empty values. Usually this will
     * be an empty string, but depending on your implementation other empty
     * values are possible as well (such as empty strings). The reasoning behind
     * this is that value transformers must be chainable. If the
     * reverseTransform() method of the first value transformer outputs an
     * empty string, the second value transformer must be able to process that
     * value.
     *
     * By convention, reverseTransform() should return NULL if an empty string
     * is passed.
     *
     * @param  mixed $value              The value in the transformed representation
     *
     * @return mixed                     The value in the original representation
     *
     * @throws UnexpectedTypeException   when the argument is not of the expected type
     * @throws TransformationFailedException  when the transformation fails
     */
    function reverseTransform($value)
    {
        if (null === $value) {
            return new $this->cityClass();
        }

        try {
            $callback = sprintf('http://where.yahooapis.com/geocode?flags=PG&appid=%s&location=%s', urlencode($this->appid), urlencode($value));
            $ch = curl_init($callback);
            curl_setopt_array($ch, array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 2,
                CURLOPT_TIMEOUT => 2,
            ));
            $location = unserialize(curl_exec($ch));
            if (!isset($location['ResultSet']['Result'][0])) {
                return null;
            }
            $result = $location['ResultSet']['Result'][0];

            /** @var $city \Kurumi\UserBundle\Entity\City */
            $city = new $this->cityClass();
            if ($result['quality'] < 39) {
                return null;
            }
            $city->setLatitude($result['latitude']);
            $city->setLongitude($result['longitude']);
            $city->setCountryCode($result['level0code']);
            $city->setCountryName($result['level0']);
            if ($result['level4'] !== '') {
                $city->setName($result['level4']);
            } else {
                $city->setName($result['level3']);
            }
            if ($result['level0code'] === 'US' || $result['level0code'] === 'CA') {
                $city->setState($result['level1']);
            }
            if ($existingCity = $this->repository->findOneBy(array(
                'latitude' => $city->getLatitude(),
                'longitude' => $city->getLongitude(),
            ))
            ) {
                return $existingCity;
            }
            return $city;
        } catch (\Exception $e) {
            throw $e;
            return null;
        }
    }


}