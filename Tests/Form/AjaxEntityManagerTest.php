<?php

namespace Zenstruck\Bundle\FormBundle\Tests\Form;

use Zenstruck\Bundle\FormBundle\Form\AjaxEntityManager;


class AjaxEntityManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testEncryption()
    {
        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');

        $manager = new AjaxEntityManager($registry, '1234');

        $this->assertEquals('FooBar', $manager->decriptString($manager->encriptString('FooBar')));
        $this->assertNotEquals('FooBar', $manager->encriptString('FooBar'));
    }
}