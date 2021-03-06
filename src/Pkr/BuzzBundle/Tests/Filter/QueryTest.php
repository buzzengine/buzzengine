<?php

namespace Pkr\BuzzBundle\Tests\Filter;

use Pkr\BuzzBundle\Entity;
use Pkr\BuzzBundle\Filter;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $filter = new Filter\Query();
        $this->assertInstanceOf('Pkr\BuzzBundle\Filter\FilterInterface', $filter);
    }

    public function testIsAccepted()
    {
        $this->assertTrue($this->_testIsAccepted('node.js', 'Lorem ipsum dolor sit amet, node.js sadipscing elitr.'));
        $this->assertTrue($this->_testIsAccepted('node.js', 'Lorem ipsum dolor sit amet, NODE.JS sadipscing elitr.'));
        $this->assertFalse($this->_testIsAccepted('node.js', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'));

        $this->assertTrue($this->_testIsAccepted('node.js development', 'Lorem ipsum dolor sit amet, node.js development sadipscing elitr.'));
        $this->assertTrue($this->_testIsAccepted('node.js development', 'Lorem ipsum dolor sit amet, node.js sadipscing elitr development.'));
        $this->assertTrue($this->_testIsAccepted('node.js development', 'Lorem development dolor sit amet, node.js sadipscing elitr.'));
        $this->assertFalse($this->_testIsAccepted('node.js development', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'));
        $this->assertFalse($this->_testIsAccepted('node.js development', 'Lorem ipsum dolor sit amet, node.js sadipscing elitr.'));
        $this->assertFalse($this->_testIsAccepted('node.js development', 'Lorem ipsum dolor sit amet, development sadipscing elitr.'));

        $this->assertTrue($this->_testIsAccepted('"node.js development"', 'Lorem ipsum dolor sit amet, node.js development sadipscing elitr.'));
        $this->assertTrue($this->_testIsAccepted('"node.js development"', 'Lorem ipsum dolor sit amet, node.js   development sadipscing elitr.'));
        $this->assertTrue($this->_testIsAccepted('"node.js development"', 'Lorem ipsum dolor sit amet, <em>node.js</em> development sadipscing elitr.'));
        $this->assertFalse($this->_testIsAccepted('"node.js development"', 'Lorem ipsum dolor sit amet, node.js sadipscing elitr development.'));
        $this->assertFalse($this->_testIsAccepted('"node.js development"', 'Lorem development dolor sit amet, node.js sadipscing elitr.'));
        $this->assertFalse($this->_testIsAccepted('"node.js development"', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr.'));
        $this->assertFalse($this->_testIsAccepted('"node.js development"', 'Lorem ipsum dolor sit amet, node.js sadipscing elitr.'));
        $this->assertFalse($this->_testIsAccepted('"node.js development"', 'Lorem ipsum dolor sit amet, development sadipscing elitr.'));

        $this->assertTrue($this->_testIsAccepted('node.js -npm', 'Lorem ipsum dolor sit amet, node.js sadipscing elitr.'));
        $this->assertFalse($this->_testIsAccepted('node.js -npm', 'Lorem ipsum dolor sit amet, node.js sadipscing elitr npm.'));

        $this->assertTrue($this->_testIsAccepted('"node.js development" -"npm use"', 'Lorem ipsum dolor sit amet, node.js development sadipscing elitr.'));
        $this->assertTrue($this->_testIsAccepted('"node.js development" -"npm use"', 'Lorem ipsum dolor sit amet, node.js development sadipscing elitr use npm.'));
        $this->assertFalse($this->_testIsAccepted('"node.js development" -"npm use"', 'Lorem ipsum dolor sit amet, node.js development sadipscing elitr npm use.'));
        $this->assertFalse($this->_testIsAccepted('"node.js development" -"npm use"', 'Lorem ipsum dolor sit amet, node.js development sadipscing elitr npm   use.'));
    }

    protected function _testIsAccepted($query, $content)
    {
        $filter = new Filter\Query();
        $entity = new Entity\Query();
        $entity->setValue($query);
        $filter->addQuery($entity);

        $mockEntry = $this->getMock('Zend\Feed\Reader\Entry');
        $mockEntry->expects($this->any())
                  ->method('getTitle')
                  ->will($this->returnValue($content));

        return $filter->isAccepted($mockEntry);
    }

    public function testIsAccepted2()
    {
        $filter = new Filter\Query();
        $entity = new Entity\Query();
        $entity->setValue('query');
        $filter->addQuery($entity);

        $mockEntry = $this->getMock('Zend\Feed\Reader\Entry');
        $mockEntry->expects($this->any())
                  ->method('getTitle')
                  ->will($this->returnValue('query'));
        $mockEntry->expects($this->any())
                  ->method('getDescription')
                  ->will($this->returnValue('hello'));
        $mockEntry->expects($this->any())
                  ->method('getContent')
                  ->will($this->returnValue('world'));

        $this->assertTrue($filter->isAccepted($mockEntry));

        $mockEntry = $this->getMock('Zend\Feed\Reader\Entry');
        $mockEntry->expects($this->any())
                  ->method('getTitle')
                  ->will($this->returnValue('hello'));
        $mockEntry->expects($this->any())
                  ->method('getDescription')
                  ->will($this->returnValue('query'));
        $mockEntry->expects($this->any())
                  ->method('getContent')
                  ->will($this->returnValue('world'));

        $this->assertTrue($filter->isAccepted($mockEntry));

        $mockEntry = $this->getMock('Zend\Feed\Reader\Entry');
        $mockEntry->expects($this->any())
                  ->method('getTitle')
                  ->will($this->returnValue('hello'));
        $mockEntry->expects($this->any())
                  ->method('getDescription')
                  ->will($this->returnValue('world'));
        $mockEntry->expects($this->any())
                  ->method('getContent')
                  ->will($this->returnValue('query'));

        $this->assertTrue($filter->isAccepted($mockEntry));

        $mockEntry = $this->getMock('Zend\Feed\Reader\Entry');
        $mockEntry->expects($this->any())
                  ->method('getTitle')
                  ->will($this->returnValue('hello'));
        $mockEntry->expects($this->any())
                  ->method('getDescription')
                  ->will($this->returnValue('world'));
        $mockEntry->expects($this->any())
                  ->method('getContent')
                  ->will($this->returnValue('hello world'));

        $this->assertFalse($filter->isAccepted($mockEntry));
    }

    public function testIsAccepted3()
    {
        $filter = new Filter\Query();
        $entity = new Entity\Query();
        $entity->setValue('foo');
        $filter->addQuery($entity);

        $entity2 = new Entity\Query();
        $entity2->setValue('query');
        $filter->addQuery($entity2);

        $entity3 = new Entity\Query();
        $entity3->setValue('bar');
        $filter->addQuery($entity3);

        $mockEntry = $this->getMock('Zend\Feed\Reader\Entry');
        $mockEntry->expects($this->any())
                  ->method('getTitle')
                  ->will($this->returnValue('query'));

        $this->assertTrue($filter->isAccepted($mockEntry));

        $filter->reset();
        $entity = new Entity\Query();
        $entity->setValue('foo');
        $filter->addQuery($entity);

        $entity2 = new Entity\Query();
        $entity2->setValue('bar');
        $filter->addQuery($entity2);

        $this->assertFalse($filter->isAccepted($mockEntry));
    }
}
