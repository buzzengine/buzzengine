<?php

namespace Pkr\BuzzBundle\Filter;

use Pkr\BuzzBundle\Entity;
use Zend\Feed\Reader\Entry;

class Query implements FilterInterface
{
    protected $_matchedQueries = array ();
    protected $_queries = array ();

    public function __construct($value = null)
    {
        if (!is_null($value))
        {
            $this->addQuery($value);
        }
    }

    public function addQuery(Entity\Query $query)
    {
        /*
         * ~-?"[^"\f\n\r\t\v]+"|-?\S+~i oder ~-?"[^"]+"|-?\S+~i
         *
         * ~(?=.*JavaScript)(?=.*jQuery)(?!.*php)~
         */

        $matches = null;
        if (preg_match_all('~-?"[^"\f\n\r\t\v]+"|-?\S+~i', $query->getValue(), $matches))
        {
            $matches = current($matches);
            $pattern = '';

            foreach ($matches as $match)
            {
                $matches2 = null;
                $val = null;

                if (preg_match('~-?"?([^"\f\n\r\t\v]+)"?~', $match, $matches2))
                {
                    $val = $matches2[1];
                    $val = str_replace(' ', '\s+', $val);
                }
                else
                {
                    throw new \Exception('query does not match pattern');
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

            $pattern = '~' . $pattern . '~i';

            $this->_queries[$pattern] = $query;
        }
        else
        {
            throw new \Exception('query does not match pattern');
        }
    }

    public function reset()
    {
        $this->_matchedQueries = array ();
        $this->_queries = array ();
    }

    public function getMatchedQueries()
    {
        return $this->_matchedQueries;
    }

    public function isAccepted(Entry $entry)
    {
        $this->_matchedQueries = array ();

        foreach ($this->_queries as $pattern => $query)
        {
            if (preg_match($pattern, strip_tags($entry->getTitle()))
                || preg_match($pattern, strip_tags($entry->getDescription()))
                || preg_match($pattern, strip_tags($entry->getContent())))
            {
                $this->_matchedQueries[] = $query;
            }
        }

        if (count ($this->_matchedQueries))
        {
            return true;
        }

        return false;
    }
}
