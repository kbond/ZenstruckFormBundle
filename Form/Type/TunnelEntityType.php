<?php

namespace Zenstruck\Bundle\FormBundle\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Zenstruck\Bundle\FormBundle\Form\DataTransformer\AjaxEntityTransformer;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class TunnelEntityType extends AbstractType
{
    protected $registry;
    protected $manager;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new AjaxEntityTransformer($this->registry, $options['class'], $options['separator']);
        $builder->addViewTransformer($transformer);

        $builder->setAttribute('separator', $options['separator']);
        $builder->setAttribute('button_text', $options['button_text']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $value = $view->vars['value'];
        $separator = $form->getConfig()->getAttribute('separator');

        if ($value) {
            $data = explode($separator, $value);
            $view->vars['value'] = $data[0];
            $view->vars['title'] = $data[1];
        } else {
            $view->vars['title'] = '';
        }

        $view->vars['button_text'] = $form->getConfig()->getAttribute('button_text');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('class'));
        $resolver->setDefaults(array(
                'separator'     => '|',
                'button_text'   => 'Select...'
            ));
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'zenstruck_tunnel_entity';
    }
}