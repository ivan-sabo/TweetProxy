<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $screenName;

    /**
     * @ORM\Column(type="string")
     */
    private $location;

    /**
     * @ORM\OneToMany(targetEntity="Tweet", mappedBy="user")
     */
    private $tweets;

    public function __construct()
    {
        $this->tweets = new ArrayCollection;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getScreenName()
    {
        return $this->screenName;
    }

    public function setScreenName($screen)
    {
        $this->screenName = $screen;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function setLocation($location)
    {
        $this->location = $location;
    }

    public function getTweets()
    {
        return $this->tweets;
    }

    public function setTweets($tweets)
    {
        $this->tweets = $tweets;
    }
}
