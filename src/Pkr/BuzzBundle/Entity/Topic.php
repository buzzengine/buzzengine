<?php

namespace Pkr\BuzzBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Pkr\BuzzBundle\Entity\Topic
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Pkr\BuzzBundle\Entity\TopicRepository")
 */
class Topic
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
     * @var text $description
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var ArrayCollection $categories
     *
     * @ORM\ManyToMany(targetEntity="Category", inversedBy="topics")
     * @ORM\JoinTable(name="TopicCategory",
     *      joinColumns={@ORM\JoinColumn(name="topicId", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="categoryId", referencedColumnName="id")}
     * )
     */
    private $categories;

    /**
     * @var ArrayCollection $queries
     *
     * @ORM\OneToMany(targetEntity="Query", mappedBy="topic", cascade={"persist", "remove"})
     */
    private $queries;

    /**
     * @var ArrayCollection $feeds
     *
     * @ORM\OneToMany(targetEntity="Feed", mappedBy="topic", cascade={"persist", "remove"})
     */
    private $feeds;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->queries = new ArrayCollection();
        $this->feeds = new ArrayCollection();
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
     * Set description
     *
     * @param text $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return text
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set categories
     *
     * @param ArrayCollection $categories
     */
    public function setCategories(ArrayCollection $categories)
    {
        $this->categories = $categories;
    }

    /**
     * Get categories
     *
     * @return ArrayCollection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Get queries
     *
     * @return ArrayCollection
     */
    public function getQueries()
    {
        return $this->queries;
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

    public function __toString()
    {
        return $this->getName();
    }
}
