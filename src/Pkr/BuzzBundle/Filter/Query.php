<?php

namespace Pkr\BuzzBundle\Filter;

use Zend\Feed\Reader\Entry;

class Query implements FilterInterface
{
    protected $_patterns = array ();

    public function addQuery($value)
    {
        /*
         * ~-?"[^"\f\n\r\t\v]+"|-?\S+~i oder ~-?"[^"]+"|-?\S+~i
         *
         * ~(?=.*JavaScript)(?=.*jQuery)(?!.*php)~
         */

        $matches = null;
        if (preg_match_all('~-?"[^"\f\n\r\t\v]+"|-?\S+~i', $value, $matches))
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

            $this->_patterns[$value] = '~' . $pattern . '~i';
        }
        else
        {
            throw new \Exception('query does not match pattern');
        }
    }

    public function isAccepted(Entry $entry)
    {
        foreach ($this->_patterns as $pattern)
        {
            if (!(preg_match($pattern, strip_tags($entry->getTitle()))
                || preg_match($pattern, strip_tags($entry->getDescription()))
                || preg_match($pattern, strip_tags($entry->getContent()))))
            {
                return false;
            }
        }

        return true;
    }
}
