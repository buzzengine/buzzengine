<?php

namespace Pkr\BuzzBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * Pkr\BuzzBundle\Entity\Topic
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Pkr\BuzzBundle\Entity\TopicRepository")
 * @DoctrineAssert\UniqueEntity(fields="name")
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
     * @var ArrayCollection $topicFeeds
     *
     * @ORM\OneToMany(targetEntity="TopicFeed", mappedBy="topic", cascade={"persist", "remove"})
     */
    private $topicFeeds;

    /**
     * @var ArrayCollection $domains
     *
     * @ORM\OneToMany(targetEntity="Domain", mappedBy="topic", cascade={"persist", "remove"})
     */
    private $domains;

    /**
     * @var ArrayCollection $authors
     *
     * @ORM\OneToMany(targetEntity="Author", mappedBy="topic", cascade={"persist", "remove"})
     */
    private $authors;

    /**
     * @var FilterLanguageDetectlanguageCom $filterLanguageDetectlanguageCom
     *
     * @ORM\OneToOne(targetEntity="FilterLanguageDetectlanguageCom", mappedBy="topic", cascade={"persist", "remove"})
     */
    private $filterLanguageDetectlanguageCom;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->domains = new ArrayCollection();
        $this->queries = new ArrayCollection();
        $this->topicFeeds = new ArrayCollection();
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
     * Get queriesOrdered
     *
     * @return Array
     */
    public function getQueriesOrdered()
    {
        $array = $this->queries->toArray();

        uasort($array, function (Query $a, Query $b)
        {
            $aCount = $a->getFeedEntries()->count();
            $bCount = $b->getFeedEntries()->count();

            if ($aCount == $bCount)
            {
                return 0;
            }

            return ($aCount > $bCount) ? -1 : 1;
        });

        return $array;
    }

    /**
     * Get topicFeeds
     *
     * @return ArrayCollection
     */
    public function getTopicFeeds()
    {
        return $this->topicFeeds;
    }

    /**
     * Get domains
     *
     * @return ArrayCollection
     */
    public function getDomains()
    {
        return $this->domains;
    }

    /**
     * Get domainsOrdered
     *
     * @return Array
     */
    public function getDomainsOrdered()
    {
        $array = $this->domains->toArray();

        uasort($array, function (Domain $a, Domain $b)
        {
            $aCount = $a->getFeedEntries()->count();
            $bCount = $b->getFeedEntries()->count();

            if ($aCount == $bCount)
            {
                return 0;
            }

            return ($aCount > $bCount) ? -1 : 1;
        });

        return $array;
    }

    /**
     * Get authors
     *
     * @return ArrayCollection
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * Get authorsOrdered
     *
     * @return Array
     */
    public function getAuthorsOrdered()
    {
        $array = $this->authors->toArray();

        uasort($array, function (Author $a, Author $b)
        {
            $aCount = $a->getFeedEntries()->count();
            $bCount = $b->getFeedEntries()->count();

            if ($aCount == $bCount)
            {
                return 0;
            }

            return ($aCount > $bCount) ? -1 : 1;
        });

        return $array;
    }

    /**
     * Get filterLanguageDetectlanguageCom
     *
     * @return FilterLanguageDetectlanguageCom
     */
    public function getFilterLanguageDetectlanguageCom()
    {
        return $this->filterLanguageDetectlanguageCom;
    }

    /**
     * Get filters
     *
     * @return array
     */
    public function getFilters()
    {
        $filters = array ();

        if (!is_null($this->filterLanguageDetectlanguageCom))
        {
            $filters[] = $this->filterLanguageDetectlanguageCom;
        }

        return $filters;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
