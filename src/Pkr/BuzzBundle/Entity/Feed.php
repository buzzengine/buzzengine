<?php

namespace Pkr\BuzzBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Pkr\BuzzBundle\Entity\Feed
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Pkr\BuzzBundle\Entity\FeedRepository")
 */
class Feed
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
     * @var boolean $disabled
     *
     * @ORM\Column(name="disabled", type="boolean")
     * @Assert\NotNull()
     */
    private $disabled = false;

    /**
     * @var Topic $topic
     *
     * @ORM\ManyToOne(targetEntity="Topic", inversedBy="feeds")
     * @ORM\JoinColumn(name="topicId", referencedColumnName="id")
     * @Assert\NotNull()
     * @Assert\Type(type="Pkr\BuzzBundle\Entity\Topic")
     */
    private $topic;

    /**
     * @var RawFeed $rawFeed
     *
     * @ORM\ManyToOne(targetEntity="RawFeed", inversedBy="feeds")
     * @ORM\JoinColumn(name="rawFeedId", referencedColumnName="id")
     * @Assert\Type(type="Pkr\BuzzBundle\Entity\RawFeed")
     */
    private $rawFeed;

    /**
     * @var Query $query
     *
     * @ORM\ManyToOne(targetEntity="Query", inversedBy="feeds")
     * @ORM\JoinColumn(name="queryId", referencedColumnName="id")
     * @Assert\Type(type="Pkr\BuzzBundle\Entity\Query")
     */
    private $query;

    public function __construct($rawFeed = null, $query = null)
    {
        if (!(is_null($rawFeed) && is_null($query)))
        {
            $this->rawFeed = $rawFeed;
            $this->query = $query;

            $this->url = $rawFeed->getFeedUrl($query);
        }
    }

    public function detachFromRawFeed()
    {
        $this->rawFeed = null;
        $this->disabled = true;
    }

    public function detachFromQuery()
    {
        $this->query = null;
        $this->disabled = true;
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

    /**
     * Set rawFeed
     *
     * @param RawFeed $rawFeed
     */
    public function setRawFeed(RawFeed $rawFeed)
    {
        $this->rawFeed = $rawFeed;
    }

    /**
     * Get rawFeed
     *
     * @return RawFeed
     */
    public function getRawFeed()
    {
        return $this->rawFeed;
    }

    /**
     * Set query
     *
     * @param Query $query
     */
    public function setQuery(Query $query)
    {
        $this->query = $query;
    }

    /**
     * Get query
     *
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
    }
}
