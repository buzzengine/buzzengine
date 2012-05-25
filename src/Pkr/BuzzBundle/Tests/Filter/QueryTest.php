<?php

namespace Pkr\BuzzBundle\Tests\Filter;

use Pkr\BuzzBundle\Filter\Query;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $filter = new Query();
        $this->assertInstanceOf('Pkr\BuzzBundle\Filter\FilterInterface', $filter);
    }

    public function testFilter()
    {
        $this->assertTrue($this->_testFilter('node.js', 'Lorem ipsum dolor sit amet, node.js sadipscing elitr.'));
        $this->assertTrue($this->_testFilter('node.js', 'Lorem ipsum dolor sit amet, NODE.JS sadipscing elitr.'));
        $this->assertFalse($this->_testFilter('node.js', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'));

        $this->assertTrue($this->_testFilter('node.js development', 'Lorem ipsum dolor sit amet, node.js development sadipscing elitr.'));
        $this->assertTrue($this->_testFilter('node.js development', 'Lorem ipsum dolor sit amet, node.js sadipscing elitr development.'));
        $this->assertTrue($this->_testFilter('node.js development', 'Lorem development dolor sit amet, node.js sadipscing elitr.'));
        $this->assertFalse($this->_testFilter('node.js development', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'));
        $this->assertFalse($this->_testFilter('node.js development', 'Lorem ipsum dolor sit amet, node.js sadipscing elitr.'));
        $this->assertFalse($this->_testFilter('node.js development', 'Lorem ipsum dolor sit amet, development sadipscing elitr.'));

        $this->assertTrue($this->_testFilter('"node.js development"', 'Lorem ipsum dolor sit amet, node.js development sadipscing elitr.'));
        $this->assertTrue($this->_testFilter('"node.js development"', 'Lorem ipsum dolor sit amet, node.js   development sadipscing elitr.'));
        $this->assertTrue($this->_testFilter('"node.js development"', 'Lorem ipsum dolor sit amet, <em>node.js</em> development sadipscing elitr.'));
        $this->assertFalse($this->_testFilter('"node.js development"', 'Lorem ipsum dolor sit amet, node.js sadipscing elitr development.'));
        $this->assertFalse($this->_testFilter('"node.js development"', 'Lorem development dolor sit amet, node.js sadipscing elitr.'));
        $this->assertFalse($this->_testFilter('"node.js development"', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'));
        $this->assertFalse($this->_testFilter('"node.js development"', 'Lorem ipsum dolor sit amet, node.js sadipscing elitr.'));
        $this->assertFalse($this->_testFilter('"node.js development"', 'Lorem ipsum dolor sit amet, development sadipscing elitr.'));

        $this->assertTrue($this->_testFilter('node.js -npm', 'Lorem ipsum dolor sit amet, node.js sadipscing elitr.'));
        $this->assertFalse($this->_testFilter('node.js -npm', 'Lorem ipsum dolor sit amet, node.js sadipscing elitr npm.'));

        $this->assertTrue($this->_testFilter('"node.js development" -"npm use"', 'Lorem ipsum dolor sit amet, node.js development sadipscing elitr.'));
        $this->assertTrue($this->_testFilter('"node.js development" -"npm use"', 'Lorem ipsum dolor sit amet, node.js development sadipscing elitr use npm.'));
        $this->assertFalse($this->_testFilter('"node.js development" -"npm use"', 'Lorem ipsum dolor sit amet, node.js development sadipscing elitr npm use.'));
        $this->assertFalse($this->_testFilter('"node.js development" -"npm use"', 'Lorem ipsum dolor sit amet, node.js development sadipscing elitr npm   use.'));
    }

    protected function _testFilter($query, $content)
    {
        $filter = new Query();
        $filter->addQuery($query);

        $mockEntry = $this->getMock('Zend\Feed\Reader\Entry');
        $mockEntry->expects($this->any())
                  ->method('getTitle')
                  ->will($this->returnValue($content));

        return $filter->isAccepted($mockEntry);
    }
}
