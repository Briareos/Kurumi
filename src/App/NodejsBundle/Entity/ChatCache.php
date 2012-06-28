<?php

namespace App\NodejsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\UserBundle\Entity\User;

/**
 * App\NodejsBundle\Entity\ChatCache
 *
 * @ORM\Table(name="chat__cache")
 * @ORM\Entity(repositoryClass="App\NodejsBundle\Entity\ChatCacheRepository")
 */
class ChatCache
{
    /**
     * @var User
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="App\UserBundle\Entity\User", inversedBy="chatCache")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $user;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="active_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $active;

    /**
     * @var string $open
     *
     * @ORM\Column(name="open", type="text", nullable=true)
     */
    private $open;


    public function __construct(User $user)
    {
        $this->setUser($user);
        $this->setOpen(array());
    }


    /**
     * Set open
     *
     * @param string $open
     * @return ChatCache
     */
    public function setOpen(array $open = array())
    {
        array_unshift($open, 0);
        $this->open = implode(',', $open);
        return $this;
    }

    /**
     * Get open
     *
     * @return string
     */
    public function getOpen()
    {
        $open = explode(',', $this->open);
        $open = array_map('intval', $open);
        array_shift($open);
        return $open;
    }

    public function addOpen($add)
    {
        $open = $this->getOpen();
        $key = array_search($add, $open);
        if ($key === false) {
            $open[] = $add;
            $this->setOpen($open);
        }
        return $this;
    }

    public function removeOpen($remove)
    {
        $open = $this->getOpen();
        $key = array_search($remove, $open);
        if (false !== $key) {
            unset($open[$key]);
            $this->setOpen($open);
        }
        return $this;
    }

    /**
     * @return ChatCache
     */
    public function setUser(\App\UserBundle\Entity\User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return App\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return ChatCache
     */
    public function setActive(\App\UserBundle\Entity\User $active = null)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return App\UserBundle\Entity\User|null
     */
    public function getActive()
    {
        return $this->active;
    }

    public function getActiveId()
    {
        if (null === $this->getActive()) {
            return 0;
        } else {
            return $this->getActive()->getId();
        }
    }
}