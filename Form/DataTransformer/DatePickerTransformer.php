<?php

namespace ZenstruckFormBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\Exception\UnexpectedTypeException;

class DatePickerTransformer implements DataTransformerInterface
{
    /**
     * Reverse transform
     *
     * @param variable $value value
     *
     * @return datetime
     */
    public function reverseTransform($value)
    {
        if (is_array($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if (!$value) {
            return new \DateTime();
        }

        if ($value === null) {
            return new \DateTime();
        }

        return new \DateTime($value);

    }

    /**
     * Transform
     *
     * @param \DateTime $value datetime
     *
     * @return array|string
     */
    public function transform($value)
    {

        if(!$value) return $value;

        if (!$value instanceof \DateTime) {
            throw new UnexpectedTypeException($value, '\DateTime');
        }

        return $value->format('d/m/Y');

    }
}
