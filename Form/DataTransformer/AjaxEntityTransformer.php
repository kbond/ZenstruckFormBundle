<?php

namespace Zenstruck\Bundle\FormBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class AjaxEntityTransformer implements DataTransformerInterface
{
    protected $repo;
    protected $sepatator;

    public function __construct(ManagerRegistry $registry, $class, $sepatator)
    {
        $this->repo = $registry->getManager()->getRepository($class);
        $this->sepatator = $sepatator;
    }

    public function transform($value)
    {
        if (is_object($value)) {
            if (!method_exists($value, 'getId')) {
                throw new \Exception(sprintf('Object "%s" does not have a "getId" method.', get_class($value)));
            }

            return $value->getId().$this->sepatator.(string) $value;
        }

        return null;
    }

    public function reverseTransform($value)
    {
        if (!$value) {
            return null;
        }

        $entity = $this->repo->find($value);

        if (!$entity) {
            throw new TransformationFailedException(sprintf(
                'Entity "%s" with id "%s" does not exist.',
                $this->repo->getClassName(),
                $value
            ));
        }

        return $entity;
    }

}