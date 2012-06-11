<?php

namespace Pkr\BuzzBundle\Filter;

use Zend\Feed\Reader\Entry;

class Regex implements FilterInterface
{
    protected $_regex = null;

    public function __construct($value = null)
    {
        if (!is_null($value))
        {
            $this->setValue($value);
        }
    }

    public function setValue($value)
    {
        $this->_regex = $value;
    }

    public function isAccepted(Entry $entry)
    {
        if (preg_match($this->_regex, strip_tags($entry->getTitle()))
            || preg_match($this->_regex, strip_tags($entry->getDescription()))
            || preg_match($this->_regex, strip_tags($entry->getContent())))
        {
            return true;
        }

        return false;
    }
}
