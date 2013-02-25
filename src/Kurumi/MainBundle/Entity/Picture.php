<?php

namespace Kurumi\MainBundle\Entity;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Picture
 *
 * @Vich\Uploadable
 */
class Picture
{
    const PROFILE_PICTURE = 1;

    const PUBLIC_PICTURE = 2;

    const PRIVATE_PICTURE = 3;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
    private $fileMime;

    /**
     * @var integer
     */
    private $fileSize;

    /**
     * @var boolean
     */
    private $temporary;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var integer
     */
    private $pictureType;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="picture", fileNameProperty="uri")
     */
    private $file;

    /**
     * @var Profile
     */
    private $profile;

    /**
     * @var PictureComment[]
     */
    private $comments;


    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->temporary = false;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function setFileMime($fileMime)
    {
        $this->fileMime = $fileMime;

        return $this;
    }

    public function getFileMime()
    {
        return $this->fileMime;
    }

    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    public function getFileSize()
    {
        return $this->fileSize;
    }

    public function setTemporary($temporary)
    {
        $this->temporary = $temporary;

        return $this;
    }

    public function getTemporary()
    {
        return $this->temporary;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile(File $file = null)
    {
        $this->file = $file;

        if ($file instanceof UploadedFile) {
            $this->fileMime = $file->getMimeType();
            $this->fileSize = $file->getSize();
            $this->fileName = $file->getClientOriginalName();
        }
    }

    public function getPictureType()
    {
        return $this->pictureType;
    }

    public function setPictureType($pictureType)
    {
        $this->pictureType = $pictureType;
    }

    public function getProfile()
    {
        return $this->profile;
    }

    public function setProfile(Profile $profile)
    {
        $this->profile = $profile;
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function setComments($comments)
    {
        $this->comments = $comments;
    }
}
