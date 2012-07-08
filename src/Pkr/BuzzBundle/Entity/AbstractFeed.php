<?php

namespace Pkr\BuzzBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Pkr\BuzzBundle\Entity\AbstractFeed
 *
 * @ORM\MappedSuperclass()
 */
abstract class AbstractFeed
{
    const FETCH_HOURLY = 'fetch hourly';
    const FETCH_DAILY = 'fetch daily';
    const FETCH_WEEKLY = 'fetch weekly';
    const FETCH_MONTHLY = 'fetch monthly';

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $url
     *
     * @ORM\Column(name="url", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\MaxLength(255)
     * @Assert\Url()
     */
    protected $url;

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
     * @var boolean $disabled
     *
     * @ORM\Column(name="disabled", type="boolean")
     * @Assert\NotNull()
     */
    protected $disabled = false;

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
}
