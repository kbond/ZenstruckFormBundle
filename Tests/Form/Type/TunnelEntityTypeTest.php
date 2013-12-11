<?php

namespace Zenstruck\Bundle\FormBundle\Tests\Form\Type;

use Symfony\Component\Form\FormView;
use Zenstruck\Bundle\FormBundle\Tests\Functional\WebTestCase;
use Zenstruck\Bundle\FormBundle\Form\Type\TunnelEntityType;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class TunnelEntityTypeTest extends WebTestCase
{
    public function testCreation()
    {
        $client = $this->prepareEnvironment();

        /** @var $formView FormView */
        $formView = $client->getContainer()->get('form.factory')->create(
            new TunnelEntityType($client->getContainer()->get('doctrine')),
            null,
            array(
                'class' => 'FormTestBundle:Author',
                'callback' => 'MyApp.FindEntity'
            )
        )->createView();

        $this->assertTrue($formView instanceof FormView);
        $this->assertEquals('Select...', $formView->vars['button_text']);
        $this->assertEquals('MyApp.FindEntity', $formView->vars['callback']);
    }
}
