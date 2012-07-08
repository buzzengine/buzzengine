<?php

namespace Pkr\BuzzBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Pkr\BuzzBundle\Entity\TopicFeed
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Pkr\BuzzBundle\Entity\TopicFeedRepository")
 */
class TopicFeed extends AbstractFeed
{
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
     * @ORM\ManyToOne(targetEntity="RawFeed", inversedBy="topicFeeds")
     * @ORM\JoinColumn(name="rawFeedId", referencedColumnName="id")
     * @Assert\Type(type="Pkr\BuzzBundle\Entity\RawFeed")
     */
    private $rawFeed;

    /**
     * @var Query $query
     *
     * @ORM\ManyToOne(targetEntity="Query", inversedBy="topicFeeds")
     * @ORM\JoinColumn(name="queryId", referencedColumnName="id")
     * @Assert\Type(type="Pkr\BuzzBundle\Entity\Query")
     */
    private $query;

    public function __construct($rawFeed = null, $query = null)
    {
        if (!(is_null($rawFeed) || is_null($query)))
        {
            $this->rawFeed = $rawFeed;
            $this->query = $query;
            $this->topic = $query->getTopic();
            $this->fetchFrequency = $rawFeed->getFetchFrequency();
            $this->disabled = $query->getDisabled();

            $this->generateUrl();
        }
    }

    public function generateUrl()
    {
        if (!(is_null($this->rawFeed) || is_null($this->query)))
        {
            $this->url = $this->rawFeed->getFeedUrl($this->query);
        }
    }

    public function detachFromRawFeed()
    {
        $this->rawFeed = null;
    }

    public function detachFromQuery()
    {
        $this->query = null;
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
