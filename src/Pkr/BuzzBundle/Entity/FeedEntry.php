<?php

namespace Pkr\BuzzBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * Pkr\BuzzBundle\Entity\FeedEntry
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Pkr\BuzzBundle\Entity\FeedEntryRepository")
 * @DoctrineAssert\UniqueEntity(fields={"title", "domain"})
 */
class FeedEntry
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
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\MaxLength(255)
     */
    private $title;

    /**
     * @var text $description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var text $content
     *
     * @ORM\Column(name="content", type="text")
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @var datetime $dateCreated
     *
     * @ORM\Column(name="dateCreated", type="datetime")
     */
    private $dateCreated;

    /**
     * @var datetime $dateModified
     *
     * @ORM\Column(name="dateModified", type="datetime")
     */
    private $dateModified;

    /**
     * @var array $links
     *
     * @ORM\Column(name="links", type="array", nullable=true)
     */
    private $links;

    /**
     * @var ArrayCollection $queries
     *
     * @ORM\ManyToMany(targetEntity="Query", inversedBy="feedEntries")
     * @ORM\JoinTable(name="FeedEntryQuery",
     *      joinColumns={@ORM\JoinColumn(name="feedEntryId", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="queryId", referencedColumnName="id")}
     * )
     */
    private $queries;

    /**
     * @var Domain $domain
     *
     * @ORM\ManyToOne(targetEntity="Domain", inversedBy="feedEntries")
     * @ORM\JoinColumn(name="domainId", referencedColumnName="id")
     * @Assert\NotNull()
     * @Assert\Type(type="Pkr\BuzzBundle\Entity\Domain")
     */
    private $domain;

    /**
     * @var ArrayCollection $authors
     *
     * @ORM\ManyToMany(targetEntity="Author", inversedBy="feedEntries")
     * @ORM\JoinTable(name="FeedEntryAuthor",
     *      joinColumns={@ORM\JoinColumn(name="feedEntryId", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="authorId", referencedColumnName="id")}
     * )
     */
    private $authors;

    public function __construct()
    {
        $this->queries = new ArrayCollection();
        $this->authors = new ArrayCollection();
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
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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
     * Set content
     *
     * @param text $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Get content
     *
     * @return text
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set dateCreated
     *
     * @param DateTime $dateCreated
     */
    public function setDateCreated(\DateTime $dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * Get dateCreated
     *
     * @return DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set dateModified
     *
     * @param DateTime $dateModified
     */
    public function setDateModified(\DateTime $dateModified)
    {
        $this->dateModified = $dateModified;
    }

    /**
     * Get dateModified
     *
     * @return DateTime
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }

    /**
     * Set links
     *
     * @param array $links
     */
    public function setLinks($links)
    {
        $this->links = $links;
    }

    /**
     * Get links
     *
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * Set queries
     *
     * @param ArrayCollection $queries
     */
    public function setQueries(ArrayCollection $queries)
    {
        $this->queries = $queries;
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
     * Set domain
     *
     * @param Domain $domain
     */
    public function setDomain(Domain $domain)
    {
        $this->domain = $domain;
    }

    /**
     * Get domain
     *
     * @return Domain
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set authors
     *
     * @param ArrayCollection $authors
     */
    public function setAuthors(ArrayCollection $authors)
    {
        $this->authors = $authors;
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
}
