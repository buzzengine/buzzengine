<?php

namespace Pkr\BuzzBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Pkr\BuzzBundle\Entity\Query
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Pkr\BuzzBundle\Entity\QueryRepository")
 */
class Query
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
     * @var string $value
     *
     * @ORM\Column(name="value", type="string", length=90)
     * @Assert\NotBlank()
     * @Assert\MaxLength(90)
     */
    private $value;

    /**
     * @var boolean $disabled
     *
     * @ORM\Column(name="disabled", type="boolean")
     * @Assert\NotNull()
     */
    private $disabled = false;

    /**
     * @var Topic $topic
     *
     * @ORM\ManyToOne(targetEntity="Topic", inversedBy="queries")
     * @ORM\JoinColumn(name="topicId", referencedColumnName="id")
     * @Assert\NotNull()
     * @Assert\Type(type="Pkr\BuzzBundle\Entity\Topic")
     */
    private $topic;

    /**
     * @var ArrayCollection $topicFeeds
     *
     * @ORM\OneToMany(targetEntity="TopicFeed", mappedBy="query", cascade={"persist"})
     */
    private $topicFeeds;

    /**
     * @var ArrayCollection $feedEntries
     *
     * @ORM\ManyToMany(targetEntity="FeedEntry", mappedBy="queries")
     * @ORM\OrderBy({"dateCreated" = "DESC"})
     */
    private $feedEntries;

    public function __construct()
    {
        $this->topicFeeds = new ArrayCollection();
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
     * Set value
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set disabled
     *
     * @param boolean $disabled
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;
    }

    /**
     * Get disabled
     *
     * @return boolean
     */
    public function getDisabled()
    {
        return $this->disabled;
    }

    /**
     * Set value
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
     * Get feedEntries
     *
     * @return ArrayCollection
     */
    public function getFeedEntries()
    {
        return $this->feedEntries;
    }
}
