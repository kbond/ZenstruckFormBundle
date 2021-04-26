<?php

namespace Zenstruck\Bundle\FormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class GroupTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAttribute('group', $options['group']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['group'] = $form->getConfig()->getAttribute('group');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'group' => null,
        ));
    }

    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
