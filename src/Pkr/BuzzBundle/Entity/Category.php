<?php

namespace Pkr\BuzzBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * Pkr\BuzzBundle\Entity\Category
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Pkr\BuzzBundle\Entity\CategoryRepository")
 * @DoctrineAssert\UniqueEntity(fields="name")
 */
class Category
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=90)
     * @Assert\NotBlank()
     * @Assert\MaxLength(90)
     */
    private $name;

    /**
     * @var ArrayCollection $topics
     *
     * @ORM\ManyToMany(targetEntity="Topic", mappedBy="categories")
     */
    private $topics;

    /**
     * @var ArrayCollection $feeds
     *
     * @ORM\OneToMany(targetEntity="Feed", mappedBy="category", cascade={"persist", "remove"})
     */
    private $feeds;

    /**
     * @var ArrayCollection $rawFeeds
     *
     * @ORM\OneToMany(targetEntity="RawFeed", mappedBy="category", cascade={"persist", "remove"})
     */
    private $rawFeeds;

    public function __construct()
    {
        $this->topics = new ArrayCollection();
        $this->feeds = new ArrayCollection();
        $this->rawFeeds = new ArrayCollection();
    }

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
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get topics
     *
     * @return ArrayCollection
     */
    public function getTopics()
    {
        return $this->topics;
    }

    /**
     * Get feeds
     *
     * @return ArrayCollection
     */
    public function getFeeds()
    {
        return $this->feeds;
    }

    /**
     * Get rawFeeds
     *
     * @return ArrayCollection
     */
    public function getRawFeeds()
    {
        return $this->rawFeeds;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
