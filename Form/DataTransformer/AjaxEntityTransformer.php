<?php

namespace Zenstruck\Bundle\FormBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class AjaxEntityTransformer implements DataTransformerInterface
{
    protected $repo;
    protected $multiple;

    public function __construct(ManagerRegistry $registry, $class, $multiple)
    {
        $this->repo = $registry->getManager()->getRepository($class);
        $this->multiple = $multiple;
    }

    public function transform($value)
    {
        if (is_array($value) || $value instanceof Collection) {
            $ret = array();

            foreach ($value as $entity) {
                $ret[] = array(
                    'id' => $entity->getId(),
                    'text' => (string) $entity
                );
            }

            return $ret;
        }

        if (is_object($value)) {
            return array(
                'id' => $value->getId(),
                'text' => (string) $value
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
}