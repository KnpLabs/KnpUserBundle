<?php

namespace Bundle\FOS\UserBundle\Validator\Doctrine\ORM;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

class FooEntity {}

class UniqueValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        if (!class_exists('\Doctrine\ORM\EntityManager')) {
            $this->markTestSkipped('No ORM installed');
        }
    }

    public function testAreTheSame()
    {
        $fooA = new FooEntity();
        $fooB = new FooEntity();

        $validator = new UniqueValidator($this->getEntityManagerMock());

        $this->assertTrue($validator->areTheSame($fooA, $fooA));
        $this->assertFalse($validator->areTheSame($fooA, $fooB));
    }

    public function testGetCriteria()
    {
        // $entity = new FooEntity();

        // $classMetadata = $this->getClassMetadataMock();
        // $classMetadata->expects($this->any())
        //               ->method('hasField')
        //               ->with($this->equalTo('username'))
        //               ->will($this->returnValue(true));

        // $classMetadata->expects($this->any())
        //               ->method('getFieldValue')
        //               ->with($this->isInstanceOf(get_class($entity)), 'username')
        //               ->will($this->returnValue('john'));

        // $entityManager = $this->getEntityManagerMock();
        // $entityManager->expects($this->any())
        //               ->method('getClassMetadata')
        //               ->will($this->returnValue($classMetadata));

        // $validator = new UniqueValidator($entityManager);

        // return $this->assertEquals(array('username' => 'john'), $validator->getCriteria($entity, array('username')));

        $this->markTestIncomplete();
    }

    public function testExtractFieldNames()
    {
        $validator = new UniqueValidator($this->getEntityManagerMock());

        $this->assertEquals(array('A', 'B', 'C'), $validator->extractFieldNames('A, B, C'));
        $this->assertEquals(array('A', 'B', 'C'), $validator->extractFieldNames(' A,B,C '));
        $this->assertEquals(array('A', 'B', 'C'), $validator->extractFieldNames(' A , B , C '));
    }

    protected function getEntityManagerMock()
    {
        return $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);
    }

    protected function getRepositoryMock()
    {
        return $this->getMock('Doctrine\ORM\EntityRepository', array(), array(), '', false);
    }

    protected function getClassMetadataMock()
    {
        return $this->getMock('Doctrine\ORM\Mapping\ClassMetadata', array(), array(), '', false);
    }
}
