<?php

namespace Pkr\BuzzBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Pkr\BuzzBundle\Entity\RawFeed
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Pkr\BuzzBundle\Entity\RawFeedRepository")
 */
class RawFeed
{
    const FETCH_HOURLY = 'fetch hourly';
    const FETCH_DAILY = 'fetch daily';
    const FETCH_WEEKLY = 'fetch weekly';
    const FETCH_MONTHLY = 'fetch monthly';

    const PLACEHOLDER = '*QUERY*';

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
     * @Assert\Regex(
     *      pattern = "~\*QUERY\*~",
     *      match   = true,
     *      message = "'*QUERY*' placeholder missing"
     * )
     */
    private $url;

    /**
     * @var string $fetchFrequency
     *
     * @ORM\Column(name="fetchFrequency", type="string", length=30)
     * @Assert\NotBlank()
     * @Assert\MaxLength(30)
     * @Assert\Choice(
     *      choices = {"fetch hourly", "fetch daily", "fetch weekly", "fetch monthly"},
     *      message = "Choose a valid fetch frequency."
     * )
     */
    protected $fetchFrequency;

    /**
     * @var Category $category
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="rawFeeds")
     * @ORM\JoinColumn(name="categoryId", referencedColumnName="id")
     * @Assert\NotNull()
     * @Assert\Type(type="Pkr\BuzzBundle\Entity\Category")
     */
    private $category;

    /**
     * @var ArrayCollection $topicFeeds
     *
     * @ORM\OneToMany(targetEntity="TopicFeed", mappedBy="rawFeed", cascade={"persist"})
     */
    private $topicFeeds;

    public function __construct()
    {
        $this->topicFeeds = new ArrayCollection();
    }

    public function getFeedUrl(Query $query)
    {
        return str_replace(self::PLACEHOLDER, urlencode($query->getValue()), $this->url);
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
     * Set fetchFrequency
     *
     * @param string $fetchFrequency
     */
    public function setFetchFrequency($fetchFrequency)
    {
        $this->fetchFrequency = $fetchFrequency;
    }

    /**
     * Get fetchFrequency
     *
     * @return string
     */
    public function getFetchFrequency()
    {
        return $this->fetchFrequency;
    }

    /**
     * Set category
     *
     * @param Category $category
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
    }

    /**
     * Get category
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
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
}
