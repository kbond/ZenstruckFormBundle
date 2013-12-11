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
        $transformer = new AjaxEntityTransformer($this->registry, $options['class'], false, null);
        $builder->addViewTransformer($transformer);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $value = $view->vars['value'];

        if ($value) {
            $view->vars['value'] = $value['id'];
            $view->vars['title'] = $value['text'];
        } else {
            $view->vars['title'] = '';
        }

        $view->vars['attr']['class'] = 'zenstruck-tunnel-id';
        $view->vars['button_text'] = $options['button_text'];
        $view->vars['callback'] = $options['callback'];
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('class'));
        $resolver->setDefaults(array(
                'button_text'   => 'Select...',
                'callback' => null
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
