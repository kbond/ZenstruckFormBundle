<?php

namespace ZenstruckFormBundle\Form\Type;

use ZenstruckFormBundle\Form\DataTransformer\DateTimePickerTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class DateTimePickerType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->resetClientTransformers();
        $builder->appendClientTransformer(new DateTimePickerTransformer());

    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(\Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
            )
        );
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'zenstruck_datetimepicker';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->setAttribute('class', 'datetimepicker');
    }
}
