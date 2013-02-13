<?php

namespace Zenstruck\Bundle\FormBundle\Tests\Form;

use Zenstruck\Bundle\FormBundle\Form\AjaxEntityManager;


class AjaxEntityManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testEncryption()
    {
        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $router = $this->getMock('Symfony\Component\Routing\Router', array(), array(), '', false);

        $manager = new AjaxEntityManager($registry, $router, '1234');

        $this->assertEquals('FooBar', $manager->decriptString($manager->encriptString('FooBar')));
        $this->assertNotEquals('FooBar', $manager->encriptString('FooBar'));
    }
}