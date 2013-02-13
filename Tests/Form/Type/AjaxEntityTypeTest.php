<?php


namespace Zenstruck\Bundle\FormBundle\Tests\Form\Type;

use Symfony\Component\Form\FormView;
use Zenstruck\Bundle\FormBundle\Tests\Functional\WebTestCase;
use Zenstruck\Bundle\FormBundle\Form\Type\AjaxEntityType;
use Zenstruck\Bundle\FormBundle\Form\AjaxEntityManager;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class AjaxEntityTypeTest extends WebTestCase
{
    public function testDefault()
    {
        $client = $this->prepareEnvironment();
        $registry = $client->getContainer()->get('doctrine');
        $manager = new AjaxEntityManager($registry, $client->getContainer()->get('router'), '1234', false);

        /** @var $formView FormView */
        $formView = $client->getContainer()->get('form.factory')->create(
            new AjaxEntityType($registry, $manager),
            null,
            array(
                'class' => 'FormTestBundle:Author'
            )
        )->createView();

        $this->assertTrue($formView instanceof FormView);
        $this->assertFalse(isset($formView->vars['attr']['data-ajax-url']));
    }

    public function testCustomUrl()
    {
        $client = $this->prepareEnvironment();
        $registry = $client->getContainer()->get('doctrine');
        $manager = new AjaxEntityManager($registry, $client->getContainer()->get('router'), '1234', false);

        /** @var $formView FormView */
        $formView = $client->getContainer()->get('form.factory')->create(
            new AjaxEntityType($registry, $manager),
            null,
            array(
                'class' => 'FormTestBundle:Author',
                'url' => '/foo/bar'
            )
        )->createView();

        $this->assertTrue($formView instanceof FormView);
        $this->assertEquals('/foo/bar', $formView->vars['attr']['data-ajax-url']);
    }

    public function testAutoUrl()
    {
        $client = $this->prepareEnvironment();
        $registry = $client->getContainer()->get('doctrine');
        $manager = new AjaxEntityManager($registry, $client->getContainer()->get('router'), '1234', true);

        /** @var $formView FormView */
        $formView = $client->getContainer()->get('form.factory')->create(
            new AjaxEntityType($registry, $manager),
            null,
            array(
                'class' => 'FormTestBundle:Author',
                'property' => 'name',
                'use_controller' => true,
                'url' => '/foo/bar'
            )
        )->createView();
        $url = '/_entity_property/'.$manager->encriptString('FormTestBundle:Author').'/'.$manager->encriptString('name');

        $this->assertTrue($formView instanceof FormView);
        $this->assertEquals($url, $formView->vars['attr']['data-ajax-url']);

        $formView = $client->getContainer()->get('form.factory')->create(
            new AjaxEntityType($registry, $manager),
            null,
            array(
                'class' => 'FormTestBundle:Author',
                'property' => 'name',
                'method' => 'findActive',
                'use_controller' => true,
                'url' => '/foo/bar'
            )
        )->createView();
        $url = '/_entity_method/'.$manager->encriptString('FormTestBundle:Author').'/'.$manager->encriptString('findActive');

        $this->assertEquals($url, $formView->vars['attr']['data-ajax-url']);

        $this->setExpectedException('Symfony\Component\OptionsResolver\Exception\MissingOptionsException');

        $manager = new AjaxEntityManager($registry, $client->getContainer()->get('router'), '1234', false);
        $client->getContainer()->get('form.factory')->create(
            new AjaxEntityType($registry, $manager),
            null,
            array(
                'class' => 'FormTestBundle:Author',
                'property' => 'name',
                'use_controller' => true,
                'url' => '/foo/bar'
            )
        )->createView();
    }
}