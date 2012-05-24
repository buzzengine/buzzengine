<?php

namespace Pkr\BuzzBundle\Filter;

use Zend\Feed\Reader\Entry;

class Query implements FilterInterface
{
    protected $_pattern = null;

    public function __construct($value)
    {
        $this->setQuery($value);
    }

    public function setQuery($value)
    {
        # ~-?"[^"\f\n\r\t\v]+"|-?\S+~i oder ~-?"[^"]+"|-?\S+~i

        $matches = null;
        if (preg_match_all('~-?"[^"\f\n\r\t\v]+"|-?\S+~i', $value, $matches))
        {
            $matches = current($matches);
            $pattern = '';

            foreach ($matches as $match)
            {
                $matches2 = null;
                $val = null;
                if (preg_match('~-?"?(.+)"?~', $match, $matches2))
                {
                    $val = $matches2[1];
                }
                else
                {
                    throw new \Exception('pattern failed');
                }

                if (0 === strpos($match, '-'))
                {
                    $pattern .= '(?!.*' . $val . ')';
                }
                else
                {
                    $pattern .= '(?=.*' . $val . ')';
                }
            }

            # ~(?=.*JavaScript)(?=.*jQuery)(?!.*php)~
            $this->_pattern = '~' . $pattern . '~i';
        }
        else
        {
            throw new \Exception('query does not match pattern');
        }
    }

    public function getPattern()
    {
        return $this->_pattern;
    }

    public function setPattern($pattern)
    {
        $this->_pattern = $pattern;
    }

    public function isAccepted(Entry $entry)
    {
        return true;


        if (preg_match($this->_pattern, $entry->getTitle())
            || preg_match($this->_pattern, $entry->getDescription())
            || preg_match($this->_pattern, $entry->getContent()))
        {
            return true;
        }

        return false;
    }
}
