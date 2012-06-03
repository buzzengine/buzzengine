<?php

namespace Pkr\BuzzBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * Pkr\BuzzBundle\Entity\Domain
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Pkr\BuzzBundle\Entity\DomainRepository")
 * @DoctrineAssert\UniqueEntity(fields={"url", "topic"})
 */
class Domain
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
     * @var string $url
     *
     * @ORM\Column(name="url", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\MaxLength(255)
     * @Assert\Url()
     */
    private $url;

    /**
     * @var ArrayCollection $feedEntries
     *
     * @ORM\OneToMany(targetEntity="FeedEntry", mappedBy="domain", cascade={"persist", "remove"})
     * @ORM\OrderBy({"dateCreated" = "DESC"})
     */
    private $feedEntries;

    /**
     * @var Topic $topic
     *
     * @ORM\ManyToOne(targetEntity="Topic", inversedBy="domains")
     * @ORM\JoinColumn(name="topicId", referencedColumnName="id")
     * @Assert\NotNull()
     * @Assert\Type(type="Pkr\BuzzBundle\Entity\Topic")
     */
    private $topic;

    public function __construct()
    {
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
     * Set url
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
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
}
