<?php

namespace Pkr\BuzzBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * Pkr\BuzzBundle\Entity\Author
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Pkr\BuzzBundle\Entity\AuthorRepository")
 * @DoctrineAssert\UniqueEntity(fields={"name", "topic"})
 */
class Author
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
     * @var ArrayCollection $aliases
     *
     * @ORM\OneToMany(targetEntity="Author", mappedBy="aliasTo")
     */
    private $aliases;

    /**
     * @var Author $aliasTo
     *
     * @ORM\ManyToOne(targetEntity="Author", inversedBy="aliases")
     * @ORM\JoinColumn(name="aliasToId", referencedColumnName="id")
     * @Assert\Type(type="Pkr\BuzzBundle\Entity\Author")
     */
    private $aliasTo;

    /**
     * @var ArrayCollection $feedEntries
     *
     * @ORM\ManyToMany(targetEntity="FeedEntry", mappedBy="authors")
     * @ORM\OrderBy({"dateCreated" = "DESC"})
     */
    private $feedEntries;

    /**
     * @var Topic $topic
     *
     * @ORM\ManyToOne(targetEntity="Topic", inversedBy="authors")
     * @ORM\JoinColumn(name="topicId", referencedColumnName="id")
     * @Assert\NotNull()
     * @Assert\Type(type="Pkr\BuzzBundle\Entity\Topic")
     */
    private $topic;

    public function __construct()
    {
        $this->aliases = new ArrayCollection();
        $this->feedEntries = new ArrayCollection();
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
     * Get aliases
     *
     * @return ArrayCollection
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * Set aliasTo
     *
     * @param Author $aliasTo
     */
    public function setAliasTo(Author $author)
    {
        $this->aliasTo = $author;
    }

    /**
     * Get aliasTo
     *
     * @return Author
     */
    public function getAliasTo()
    {
        return $this->aliasTo;
    }

    /**
     * Get feedEntries
     *
     * @return ArrayCollection
     */
    public function getFeedEntries()
    {
        return $this->feedEntries;
    }

    /**
     * Set topic
     *
     * @param Topic $topic
     */
    public function setTopic(Topic $topic)
    {
        $this->topic = $topic;
    }

    /**
     * Get topic
     *
     * @return Topic
     */
    public function getTopic()
    {
        return $this->topic;
    }

    public function getScore()
    {
        return $this->feedEntries->count();
    }
}
