<?php

namespace Pkr\BuzzBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Pkr\BuzzBundle\Entity\FilterLanguageDetectlanguageCom
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Pkr\BuzzBundle\Entity\FilterLanguageDetectlanguageComRepository")
 */
class FilterLanguageDetectlanguageCom extends AbstractFilter
{
    /**
     * @var array $allowedLanguages
     *
     * @ORM\Column(name="allowedLanguages", type="array")
     * @Assert\NotNull()
     * @Assert\Type(type="array")
     */
    private $allowedLanguages;

    /**
     * @var Topic $topic
     *
     * @ORM\OneToOne(targetEntity="Topic", inversedBy="filterLanguageDetectlanguageCom")
     * @ORM\JoinColumn(name="topicId", referencedColumnName="id")
     * @Assert\NotNull()
     * @Assert\Type(type="Pkr\BuzzBundle\Entity\Topic")
     */
    private $topic;

    public function getClass()
    {
        return 'Filter\Language\DetectlanguageCom';
    }

    /**
     * Set allowedLanguages
     *
     * @param array $allowedLanguages
     */
    public function setAllowedLanguages($allowedLanguages)
    {
        $this->allowedLanguages = $allowedLanguages;
    }

    /**
     * Get allowedLanguages
     *
     * @return array
     */
    public function getAllowedLanguages()
    {
        return $this->allowedLanguages;
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
