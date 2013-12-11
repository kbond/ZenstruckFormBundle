<?php

namespace Zenstruck\Bundle\FormBundle\Tests\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Zenstruck\Bundle\FormBundle\Form\DataTransformer\AjaxEntityTransformer;
use Zenstruck\Bundle\FormBundle\Tests\Fixtures\App\FormTestBundle\Entity\Author;
use Zenstruck\Bundle\FormBundle\Tests\Functional\WebTestCase;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class AjaxEntityTransformerTest extends WebTestCase
{
    public function testSingleTransform()
    {
        $client = $this->prepareEnvironment();
        $registry = $client->getContainer()->get('doctrine');
        $transformer = new AjaxEntityTransformer($registry, 'FormTestBundle:Author', false, 'name');

        $author = $registry->getRepository('FormTestBundle:Author')->find(1);
        $transformedValue = $transformer->transform($author);

        $this->assertTrue(is_array($transformedValue));
        $this->assertEquals('1', $transformedValue['id']);
        $this->assertEquals('Kevin', $transformedValue['text']);
    }

    public function testSingleReverseTransform()
    {
        $client = $this->prepareEnvironment();
        $registry = $client->getContainer()->get('doctrine');
        $transformer = new AjaxEntityTransformer($registry, 'FormTestBundle:Author', false, 'name');

        $transformedValue = $transformer->reverseTransform(1);

        $this->assertTrue($transformedValue instanceof Author);
        $this->assertEquals(1, $transformedValue->getId());
        $this->assertEquals('Kevin', $transformedValue->getName());

        $this->setExpectedException('Symfony\Component\Form\Exception\TransformationFailedException');
        $transformedValue = $transformer->reverseTransform(3);
    }

    public function testMultipleTransform()
    {
        $client = $this->prepareEnvironment();
        $registry = $client->getContainer()->get('doctrine');
        $transformer = new AjaxEntityTransformer($registry, 'FormTestBundle:Author', true, 'name');

        $authors = $registry->getRepository('FormTestBundle:Author')->findAll();
        $transformedValue = $transformer->transform($authors);

        $this->assertTrue(is_array($transformedValue));
        $this->assertEquals('1', $transformedValue[0]['id']);
        $this->assertEquals('Kevin', $transformedValue[0]['text']);
    }

    public function testMultipleReverseTransform()
    {
        $client = $this->prepareEnvironment();
        $registry = $client->getContainer()->get('doctrine');
        $transformer = new AjaxEntityTransformer($registry, 'FormTestBundle:Author', true, 'name');

        $transformedValue = $transformer->reverseTransform("1,2");

        $this->assertTrue($transformedValue instanceof ArrayCollection);
        $this->assertEquals(1, $transformedValue->first()->getId());
        $this->assertEquals('Kevin', $transformedValue->first()->getName());
        $this->assertEquals(2, $transformedValue->get(1)->getId());
        $this->assertEquals('James', $transformedValue->get(1)->getName());
    }

    public function testNullTransform()
    {
        $client = $this->prepareEnvironment();
        $registry = $client->getContainer()->get('doctrine');
        $transformer = new AjaxEntityTransformer($registry, 'FormTestBundle:Author', false, 'name');

        $this->assertNull($transformer->transform(null));
        $this->assertNull($transformer->reverseTransform(null));

        $client = $this->prepareEnvironment();
        $registry = $client->getContainer()->get('doctrine');
        $transformer = new AjaxEntityTransformer($registry, 'FormTestBundle:Author', true, 'name');

        $this->assertNull($transformer->transform(null));
        $this->assertTrue(is_array($transformer->reverseTransform(null)));
        $this->assertEmpty($transformer->reverseTransform(null));
    }
}
