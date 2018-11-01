<?php

namespace Zenstruck\Bundle\FormBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class AjaxEntityTransformer implements DataTransformerInterface
{
    protected $repo;
    protected $multiple;
    protected $property;

    public function __construct(ManagerRegistry $registry, $class, $multiple, $property)
    {
        $this->repo = $registry->getManager()->getRepository($class);
        $this->multiple = $multiple;
        $this->property = $property;
    }

    public function transform($value)
    {
        if (is_array($value) || $value instanceof Collection) {
            $ret = array();

            foreach ($value as $entity) {
                $ret[] = array(
                    'id' => $entity->getId(),
                    'text' => $this->getText($entity)
                );
            }

            return $ret;
        }

        if (is_object($value)) {
            return array(
                'id' => $value->getId(),
                'text' => $this->getText($value)
            );
        }

        return null;
    }

    public function reverseTransform($value)
    {
        if (!$value) {
            return $this->multiple ? array() : null;
        }

        if ($this->multiple) {
            $ids = explode(',', $value);
            $ids = array_unique($ids);

            $qb = $this->repo->createQueryBuilder('entity');
            $qb->where('entity.id IN (:ids)')
                ->setParameter('ids', $ids)
            ;

            return new ArrayCollection($qb->getQuery()->execute());
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

    protected function getText($object)
    {
        if (!$this->property || !class_exists('Symfony\Component\PropertyAccess\PropertyAccess')) {
            return (string) $object;
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        return $accessor->getValue($object, $this->property);
    }
}
