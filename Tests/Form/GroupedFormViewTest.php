<?php

namespace Zenstruck\Bundle\FormBundle\Tests\Form;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Forms;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Zenstruck\Bundle\FormBundle\Form\GroupedFormView;
use Zenstruck\Bundle\FormBundle\Tests\Functional\WebTestCase;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class GroupedFormViewTest extends WebTestCase
{
    public function testOrder()
    {
        $client = $this->prepareEnvironment();
        $formBuilder = $client->getContainer()->get('form.factory')->createBuilder();

        $form = $formBuilder
            ->add('name', 'text', array('group' => 'first'))
            ->add('address', 'text', array('group' => 'second'))
            ->add('notes', 'text')
            ->add('posts', 'text', array('group' => 'third'))
            ->getForm()
        ;

        $groupedForm = new GroupedFormView($form->createView());
        $this->assertEquals(array('Default', 'first', 'second', 'third'), $groupedForm->getGroupNames());

        $groupedForm = new GroupedFormView($form->createView(), 'Default', array('third'));
        $this->assertEquals(array('third', 'first', 'second', 'Default'), $groupedForm->getGroupNames());

        $groupedForm = new GroupedFormView($form->createView(), 'Default', array('third', 'second'));
        $this->assertEquals(array('third', 'second', 'first', 'Default'), $groupedForm->getGroupNames());

        $groupedForm = new GroupedFormView($form->createView(), 'Default', array('foo', 'third', 'second', 'bar'));
        $this->assertEquals(array('third', 'second', 'first', 'Default'), $groupedForm->getGroupNames());
    }

    public function testValid()
    {
        $collectionConstraint = new Collection(array(
            'name' => new NotBlank(),
            'address' => new NotBlank(),
            'notes' => new NotBlank(),
            'posts' => new NotBlank(),
        ));

        $client = $this->prepareEnvironment();
        $formBuilder = $client->getContainer()->get('form.factory')->createBuilder('form', null, array(
                'validation_constraint' => $collectionConstraint
            )
        );

        $form = $formBuilder
            ->add('name', 'text', array('group' => 'first'))
            ->add('address', 'text', array('group' => 'second'))
            ->add('notes', 'text')
            ->add('posts', 'text', array('group' => 'third'))
            ->getForm()
        ;

        $data = array(
            'name' => 'Kevin',
            'address' => 'Canada',
            'notes' => 'Foo',
        );

        $form->bind($data);

        $groupedForm = new GroupedFormView($form->createView());

        $this->assertFalse($groupedForm->isValid());
        $this->assertTrue($groupedForm->isValid('first'));
        $this->assertTrue($groupedForm->isValid('second'));
        $this->assertTrue($groupedForm->isValid('Default'));
        $this->assertFalse($groupedForm->isValid('third'));

        $data['posts'] = 'Bar';

        $form = $formBuilder
            ->add('name', 'text', array('group' => 'first'))
            ->add('address', 'text', array('group' => 'second'))
            ->add('notes', 'text')
            ->add('posts', 'text', array('group' => 'third'))
            ->getForm()
        ;
        $form->bind($data);

        $groupedForm = new GroupedFormView($form->createView());

        $this->assertTrue($groupedForm->isValid());
    }

    public function testGroups()
    {
        $client = $this->prepareEnvironment();
        $formBuilder = $client->getContainer()->get('form.factory')->createBuilder();

        $form = $formBuilder
            ->add('name', 'text', array('group' => 'first'))
            ->add('address', 'text', array('group' => 'second'))
            ->add('notes', 'text')
            ->add('posts', 'text', array('group' => 'third'))
            ->getForm()
        ;

        $groupedForm = new GroupedFormView($form->createView());

        $this->assertEquals(4, count($groupedForm->getGroups()));
        $this->assertArrayHasKey('first', $groupedForm->getGroups());
        $this->assertArrayHasKey('second', $groupedForm->getGroups());
        $this->assertArrayHasKey('third', $groupedForm->getGroups());
        $this->assertArrayHasKey('Default', $groupedForm->getGroups());
    }

    public function testSetData()
    {
        $client = $this->prepareEnvironment();
        $formBuilder = $client->getContainer()->get('form.factory')->createBuilder();

        $form = $formBuilder
            ->add('name', 'text')
            ->add('address', 'text')
            ->add('notes', 'text')
            ->add('posts', 'text')
            ->getForm()
        ;

        $groupedForm = new GroupedFormView($form->createView());
        $groupedForm->setData('foo', 'bar');

        $this->assertEquals('bar', $groupedForm->getData('foo'));
        $this->assertEquals('baz', $groupedForm->getData('bar', 'baz'));
    }

    public function testGroupedFormViewDefault()
    {
        $client = $this->prepareEnvironment();
        $formBuilder = $client->getContainer()->get('form.factory')->createBuilder();

        $form = $formBuilder
            ->add('name', 'text')
            ->add('address', 'text')
            ->add('notes', 'text')
            ->add('posts', 'text')
            ->getForm()
        ;

        $groupedForm = new GroupedFormView($form->createView());

        $this->assertEquals(1, count($groupedForm->getGroups()));
        $this->assertArrayHasKey('Default', $groupedForm->getGroups());

        $groups = $groupedForm->getGroups();
        $this->assertEquals(4, count($groups['Default']));

        $groupedForm = new GroupedFormView($form, 'Main');

        $this->assertArrayHasKey('Main', $groupedForm->getGroups());
    }
}