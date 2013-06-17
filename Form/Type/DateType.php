<?php

namespace Zenstruck\Bundle\FormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class DateType extends AbstractType
{
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['format'] = $options['format'];
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'widget' => 'single_text'
            ));

        $resolver->setAllowedValues(array(
                'widget' => array('single_text')
            ));
    }

    public function getName()
    {
        return 'zenstruck_date';
    }

    public function getParent()
    {
        return 'date';
    }
}