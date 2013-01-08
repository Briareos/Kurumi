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

class PopulatePicturesCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
          ->setName('kurumi:profile:populate-pictures')
          ->setDescription('Populate profiles with pictures from a specified directory')
          ->addArgument('directory', InputArgument::REQUIRED, 'Directory name')
          ->addOption('offset', 'o', InputOption::VALUE_OPTIONAL, 'Directory offset', 0)
          ->addArgument('offset', InputArgument::OPTIONAL, 'Directory offset', 0)
          ->setHelp(
            <<<EOT
The <info>kurumi:profile:populate-pictures</info> command populates profiles without any pictures with pictures randomly selected from a specified directory:

  <info>php app/console kurumi:profile:populate-pictures C:/Users/Me/Pictures/Profile</info>
  <info>php app/console kurumi:profile:populate-pictures /home/me/pictures/profile</info>
  <info>php app/console kurumi:profile:populate-pictures /home/me/pictures/profile --offset=10</info>
EOT
        );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = $input->getArgument('directory');
        $offset = $input->getArgument('offset');

        $finder = new Finder();
        $finder->in($directory);

        $directories = $finder->directories();
        $output->writeln(sprintf('Importing directory %s, total of %s subdirectories found.', $directory, $directories->count()));
        $index = 0;
        foreach ($directories as $pictureDirectory) {
            if ($index < $offset) {
                continue;
            }
            $index++;
            $profile = $this->getProfile();
            if ($profile === null) {
                $output->writeln('No profiles found, exiting.');

                return;
            }

            /** @var $pictureDirectory SplFileInfo */
            $output->write(sprintf('Importing directory: %s (%s/%s).', $pictureDirectory->getRelativePathname(), $index, $directories->count()));
            $pictureFiles = $this->findPictures($pictureDirectory);
            $output->writeln(sprintf(' - %s picture(s) found.', $pictureFiles->count()));

            foreach ($pictureFiles as $pictureFile) {
                $picture = $this->getPicture($pictureFile);
                if ($profile->getPicture() === null) {
                    $pictureType = Picture::PROFILE_PICTURE;
                    $profile->setPicture($picture);
                    $this->getEm()->persist($profile);
                } else {
                    $rand = rand(1, 100);
                    if ($rand <= 40) {
                        $pictureType = Picture::PROFILE_PICTURE;
                    } elseif ($rand <= 70) {
                        $pictureType = Picture::PUBLIC_PICTURE;
                    } else {
                        $pictureType = Picture::PRIVATE_PICTURE;
                    }
                }
                $picture->setPictureType($pictureType);
                $picture->setProfile($profile);
                $this->getEm()->persist($picture);

                $this->getEm()->flush();
            }

        }
        $output->writeln(sprintf('Directory "%s" has completed iterating.', $directory));
    }

    private function getEm()
    {
        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        return $em;
    }

    private function getPicture(SplFileInfo $pictureFile)
    {
        $picture = new Picture();
        $pictureUploadedFile = new UploadedFile($pictureFile->getRealPath(), $pictureFile->getFilename());
        $picture->setFile($pictureUploadedFile);

        return $picture;
    }

    private function getProfile()
    {
        $em = $this->getEm();

        /** @var $profile \Kurumi\MainBundle\Entity\Profile|null */
        $profile = $em->createQuery('Select p From KurumiMainBundle:Profile p Where p.picture Is Null And p.gender = 2')
          ->setMaxResults(1)
          ->getOneOrNullResult();

        return $profile;
    }

    /**
     * @param \Symfony\Component\Finder\SplFileInfo $directory
     * @return Finder
     */
    private function findPictures(SplFileInfo $directory)
    {
        $finder = new Finder();
        $finder
          ->in($directory->getRealPath())
          ->files()
          ->name('*.jpg')
          ->name('*.jpeg')
          ->name('*.png');

        return $finder;
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('directory')) {
            $directory = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please enter a directory name:',
                function ($directory) {
                    if (empty($directory)) {
                        throw new \Exception('Directory name can not be empty');
                    }

                    return $directory;
                }
            );
            $input->setArgument('directory', $directory);
        }
    }
}