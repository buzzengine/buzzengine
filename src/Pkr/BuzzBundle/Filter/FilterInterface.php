<?php

namespace Pkr\BuzzBundle\Filter;

use Zend\Feed\Reader\Entry;

interface FilterInterface
{
    public function isAccepted(Entry $entry);
}
