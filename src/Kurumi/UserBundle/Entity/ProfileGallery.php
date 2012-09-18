<?php

namespace Kurumi\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kurumi\UserBundle\Entity\Profile;
use Application\Sonata\MediaBundle\Entity\Gallery;

/**
 * Kurumi\UserBundle\Entity\ProfileGallery
 *
 * @ORM\Table(name="profile_gallery")
 * @ORM\Entity(repositoryClass="Kurumi\UserBundle\Entity\ProfileGalleryRepository")
 */
class ProfileGallery
{
    const GALLERY_PROFILE = 1;

    const GALLERY_PUBLIC = 2;

    const GALLERY_PRIVATE = 3;

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer $type
     *
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;

    /**
     * @var Profile
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="profileGalleries")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $profile;

    /**
     * @var Gallery
     *
     * @ORM\OneToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Gallery", inversedBy="profileGalleries")
     * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $gallery;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return ProfileGallery
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }
}
