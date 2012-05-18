<?php

namespace Pkr\BuzzBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pkr\BuzzBundle\Entity\FeedEntry
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Pkr\BuzzBundle\Entity\FeedEntryRepository")
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
     * @ORM\Column(name="title", type="string", length=90)
     */
    private $title;

    /**
     * @var array $authors
     *
     * @ORM\Column(name="authors", type="array")
     */
    private $authors;

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
     * @var string $permalink
     *
     * @ORM\Column(name="permalink", type="string", length=255)
     */
    private $permalink;

    /**
     * @var array $links
     *
     * @ORM\Column(name="links", type="array")
     */
    private $links;


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
     * Set authors
     *
     * @param array $authors
     */
    public function setAuthors($authors)
    {
        $this->authors = $authors;
    }

    /**
     * Get authors
     *
     * @return array
     */
    public function getAuthors()
    {
        return $this->authors;
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
     * @param datetime $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * Get dateCreated
     *
     * @return datetime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set dateModified
     *
     * @param datetime $dateModified
     */
    public function setDateModified($dateModified)
    {
        $this->dateModified = $dateModified;
    }

    /**
     * Get dateModified
     *
     * @return datetime
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }

    /**
     * Set permalink
     *
     * @param string $permalink
     */
    public function setPermalink($permalink)
    {
        $this->permalink = $permalink;
    }

    /**
     * Get permalink
     *
     * @return string
     */
    public function getPermalink()
    {
        return $this->permalink;
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
}