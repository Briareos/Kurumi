<?php

namespace Kurumi\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Kurumi\MainBundle\Entity\Picture;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;

class PopulateTimelineCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
          ->setName('kurumi:profile:populate-timeline')
          ->setDescription('Populate profiles with their timelines')
          ->setHelp(
            <<<EOT
The <info>kurumi:profile:populate-timeline</info> command tries to populate missing timeline entries for profiles:

  <info>php app/console kurumi:profile:populate-timeline</info>
EOT
        );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $actionManager = $this->getActionManager();
        $profile = $this->getProfile();
        $subject = $actionManager->findOrCreateComponent('Kurumi\MainBundle\Entity\Profile', $profile->getId());
        foreach ($profile->getPictures() as $picture) {
            $pictureSubject = $actionManager->findOrCreateComponent('Kurumi\MainBundle\Entity\Picture', $picture->getId());
            /** @var $action \Spy\Timeline\Model\ActionInterface */
            $action = $actionManager->create($subject, 'picture_add', array('picture' => $pictureSubject));
            $action->setCreatedAt($picture->getCreatedAt());
            $actionManager->updateAction($action);
        }
        $profile->setUpdatedAt(new \DateTime());
        $this->getEm()->persist($profile);
        $this->getEm()->flush();

        $output->writeln(sprintf('Profile with ID %s has had its timeline updated with %s pictures.', $profile->getId(), $profile->getPictures()->count()));
    }

    private function getEm()
    {
        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        return $em;
    }

    private function getActionManager()
    {
        /** @var $actionManager \Spy\Timeline\Driver\ActionManagerInterface */
        $actionManager = $this->getContainer()->get('spy_timeline.action_manager');

        return $actionManager;
    }

    private function getProfile()
    {
        $em = $this->getEm();

        /** @var $profile \Kurumi\MainBundle\Entity\Profile|null */
        $profile = $em->createQuery('Select p From KurumiMainBundle:Profile p Inner Join p.pictures pics Where p.gender = 2 Order By p.updatedAt Asc')
          ->setMaxResults(1)
          ->getOneOrNullResult();

        return $profile;
    }

}