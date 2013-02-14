<?php


namespace Zenstruck\Bundle\FormBundle\Tests\Form\Type;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Form\Form;
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
        $formView = $this->createFormView(
            $client, array('class' => 'FormTestBundle:Author')
        );

        $this->assertTrue($formView instanceof FormView);
        $this->assertFalse(isset($formView->vars['attr']['data-ajax-url']));
        $this->assertFalse(isset($formView->vars['attr']['data-entity']));
        $this->assertFalse(isset($formView->vars['attr']['data-property']));
        $this->assertFalse(isset($formView->vars['attr']['data-method']));
    }

    public function testCustomUrl()
    {
        $client = $this->prepareEnvironment();
        $formView = $this->createFormView(
            $client, array(
                'class' => 'FormTestBundle:Author',
                'url' => '/foo/bar'
            )
        );

        $this->assertTrue($formView instanceof FormView);
        $this->assertEquals('/foo/bar', $formView->vars['attr']['data-ajax-url']);
        $this->assertFalse(isset($formView->vars['attr']['data-entity']));
        $this->assertFalse(isset($formView->vars['attr']['data-property']));
        $this->assertFalse(isset($formView->vars['attr']['data-method']));
    }

    public function testAutoUrl()
    {
        $client = $this->prepareEnvironment();
        $manager = $this->createManager($client);
        $formView = $this->createFormView(
            $client,
            array(
                'class' => 'FormTestBundle:Author',
                'property' => 'name',
                'use_controller' => true,
                'url' => '/foo/bar'
            ),
            true
        );

        $this->assertTrue($formView instanceof FormView);
        $this->assertEquals('/_entity_find', $formView->vars['attr']['data-ajax-url']);
        $this->assertTrue(isset($formView->vars['attr']['data-entity']));
        $this->assertTrue(isset($formView->vars['attr']['data-property']));
        $this->assertEquals('FormTestBundle:Author', $manager->decriptString($formView->vars['attr']['data-entity']));
        $this->assertEquals('name', $manager->decriptString($formView->vars['attr']['data-property']));

        $formView = $this->createFormView(
            $client,
            array(
                'class' => 'FormTestBundle:Author',
                'property' => 'name',
                'method' => 'findActive',
                'use_controller' => true,
                'url' => '/foo/bar'
            ),
            true
        );

        $this->assertTrue(isset($formView->vars['attr']['data-entity']));
        $this->assertTrue(isset($formView->vars['attr']['data-method']));
        $this->assertFalse(isset($formView->vars['attr']['data-property']));
        $this->assertEquals('FormTestBundle:Author', $manager->decriptString($formView->vars['attr']['data-entity']));
        $this->assertEquals('findActive', $manager->decriptString($formView->vars['attr']['data-method']));

        $this->setExpectedException('Symfony\Component\OptionsResolver\Exception\MissingOptionsException');

        $this->createFormView(
            $client,
            array(
                'class' => 'FormTestBundle:Author',
                'property' => 'name',
                'use_controller' => true,
                'url' => '/foo/bar'
            ),
            false
        );
    }

    /**
     * @param \Symfony\Bundle\FrameworkBundle\Client $client
     * @param array $formOptions
     * @param bool $controllerEnabled
     *
     * @return \Symfony\Component\Form\FormView
     */
    protected function createFormView(Client $client, array $formOptions, $controllerEnabled = false)
    {
        $registry = $client->getContainer()->get('doctrine');
        $router = $client->getContainer()->get('router');
        $manager = new AjaxEntityManager($registry, '1234', $controllerEnabled);

        /** @var $form Form */
        $form = $client->getContainer()->get('form.factory')->create(
            new AjaxEntityType($registry, $router, $manager),
            null,
            $formOptions
        );

        return $form->createView();
    }

    /**
     * @param \Symfony\Bundle\FrameworkBundle\Client $client
     * @param bool $controllerEnabled
     *
     * @return \Zenstruck\Bundle\FormBundle\Form\AjaxEntityManager
     */
    protected function createManager(Client $client, $controllerEnabled = true)
    {
        $registry = $client->getContainer()->get('doctrine');
        return new AjaxEntityManager($registry, '1234', $controllerEnabled);
    }
}