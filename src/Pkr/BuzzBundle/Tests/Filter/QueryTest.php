<?php

namespace Pkr\BuzzBundle\Tests\Filter;

use Pkr\BuzzBundle\Filter\Query;
use Zend\Feed\Reader\Entry;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $filter = new Query();
        $this->assertInstanceOf('Pkr\BuzzBundle\Filter\FilterInterface', $filter);
    }

    public function testFilter()
    {
        $filter = new Query();
        $filter->setQuery('node.js');

        #$this->assertTrue($filter->isAccepted(//TODO));
    }
}
