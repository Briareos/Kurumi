<?php

namespace Kurumi\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Briareos\ChatBundle\Entity\ChatSubjectRepositoryInterface;
use Briareos\ChatBundle\Entity\ChatSubjectInterface;

class UserRepository extends EntityRepository implements ChatSubjectRepositoryInterface
{
    public function getPresentSubjects(ChatSubjectInterface $subject)
    {
        $presentSubjects = $this->findAll();
        /** @var $presentSubject ChatSubjectInterface */
        foreach ($presentSubjects as $i => $presentSubject) {
            if ($presentSubject->getId() === $subject->getId()) {
                unset($presentSubjects[$i]);
                break;
            }
        }
        return $presentSubjects;
    }
}