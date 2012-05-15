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
     * @var Category $category
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="rawFeeds")
     * @ORM\JoinColumn(name="categoryId", referencedColumnName="id")
     * @Assert\NotNull()
     * @Assert\Type(type="Pkr\BuzzBundle\Entity\Category")
     */
    private $category;

    /**
     * @var ArrayCollection $feeds
     *
     * @ORM\OneToMany(targetEntity="Feed", mappedBy="rawFeed", cascade={"persist"})
     */
    private $feeds;

    public function __construct()
    {
        $this->feeds = new ArrayCollection();
    }

    public function getFeedUrl(Query $query)
    {
        return str_replace(self::PLACEHOLDER, $query->getValue(), $this->url);
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
     * Get feeds
     *
     * @return ArrayCollection
     */
    public function getFeeds()
    {
        return $this->feeds;
    }
}
