<?php

namespace Kurumi\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Kurumi\UserBundle\Entity\Profile;

/**
 * ProfileRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProfileRepository extends EntityRepository
{
    public function getNearestProfiles(Profile $profile)
    {
        $result = $this->getEntityManager()->getConnection()->executeQuery('
                SELECT p.id AS profile_id,
                  (:unit * ACOS(COS(RADIANS(:latitude)) * COS(RADIANS(c.latitude)) * COS(RADIANS(c.longitude) - RADIANS(:longitude)) + SIN(RADIANS(:latitude)) * SIN(RADIANS(c.latitude)))) AS distance
                FROM profile p
                INNER JOIN city c ON c.id = p.city_id
                WHERE p.id <> :current_profile
                ORDER BY distance ASC
            ', array(
            ':current_profile' => $profile->getId(),
            ':unit' => 6371,
            ':latitude' => $profile->getCity()->getLatitude(),
            ':longitude' => $profile->getCity()->getLongitude(),
        ))
            ->fetchAll(\PDO::FETCH_CLASS);
        return $result;
    }
}