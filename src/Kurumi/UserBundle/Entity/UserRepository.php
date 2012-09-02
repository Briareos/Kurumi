<?php

namespace Kurumi\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Briareos\ChatBundle\Entity\ChatSubjectRepositoryInterface;
use Briareos\ChatBundle\Entity\ChatSubjectInterface;
use Kurumi\UserBundle\Entity\User;
use Kurumi\UserBundle\Entity\City;

class UserRepository extends EntityRepository implements ChatSubjectRepositoryInterface
{
    public function getPresentSubjects(ChatSubjectInterface $subject)
    {
        $presences = $this->getEntityManager()->createQuery('Select p From BriareosNodejsBundle:NodejsPresence p Inner Join p.subject s Where p.subject Is Not Null Group By p.subject Order By p.seenAt Desc')->execute();
        $subjects = array();
        /** @var $presence \Briareos\NodejsBundle\Entity\NodejsPresence */
        foreach ($presences as $i => $presence) {
            if ($presence->getSubject()->getId() !== $subject->getId()) {
                $subjects[] = $presence->getSubject();
            }
        }
        return $subjects;
    }

    public function getNearestUsers(User $profile, City $city)
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
            ':latitude' => $city->getLatitude(),
            ':longitude' => $city->getLongitude(),
        ))
            ->fetchAll(\PDO::FETCH_CLASS);
        return $result;
    }
}