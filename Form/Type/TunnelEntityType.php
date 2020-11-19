<?php

namespace Zenstruck\Bundle\FormBundle\Form\Type;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(array('class'));
        $resolver->setDefaults(array(
            'button_text'   => 'Select...',
            'callback' => null
        ));
    }

    public function getParent()
    {
        return TextType::class;
    }

    public function getBlockPrefix()
    {
        return 'zenstruck_tunnel_entity';
    }
}
