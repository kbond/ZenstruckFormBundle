<?php

namespace Zenstruck\Bundle\FormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ThemeTypeExtension extends AbstractTypeExtension
{
    protected $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAttribute('theme_options', array_merge($this->options, $options['theme_options']));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['theme_options'] = $form->getConfig()->getAttribute('theme_options');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'theme_options' => $this->options,
            ));
    }

    public function getExtendedType()
    {
        return 'form';
    }
}
