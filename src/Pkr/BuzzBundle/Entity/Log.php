<?php

namespace Pkr\BuzzBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Pkr\BuzzBundle\Entity\Log
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Pkr\BuzzBundle\Entity\LogRepository")
 */
class Log
{
    const ERROR = 'Error';
    const WARNING = 'Warning';
    const NOTICE = 'Notice';

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $level
     *
     * @ORM\Column(name="level", type="string", length=10)
     * @Assert\NotBlank()
     * @Assert\Choice(choices = {"Error", "Warning", "Notice"})
     */
    private $level;

    /**
     * @var text $message
     *
     * @ORM\Column(name="message", type="text")
     * @Assert\NotBlank()
     */
    private $message;

    /**
     * @var datetime $create
     *
     * @ORM\Column(name="`create`", type="datetime")
     * @Assert\Type(type="DateTime")
     */
    private $create;

    public function __construct()
    {
        $this->create = new \DateTime();
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
     * Set level
     *
     * @param string $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * Get level
     *
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set message
     *
     * @param text $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Get message
     *
     * @return text
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set create
     *
     * @param datetime $create
     */
    public function setCreate($create)
    {
        $this->create = $create;
    }

    /**
     * Get create
     *
     * @return datetime
     */
    public function getCreate()
    {
        return $this->create;
    }
}
