<?php

namespace Zenstruck\Bundle\FormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\Router;
use Zenstruck\Bundle\FormBundle\Form\AjaxEntityManager;
use Zenstruck\Bundle\FormBundle\Form\DataTransformer\AjaxEntityTransformer;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class AjaxEntityType extends AbstractType
{
    protected $registry;
    protected $router;
    protected $manager;

    public function __construct(ManagerRegistry $registry, Router $router, AjaxEntityManager $manager = null)
    {
        $this->registry = $registry;
        $this->router = $router;
        $this->manager = $manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new AjaxEntityTransformer(
            $this->registry,
            $options['class'],
            $options['multiple'],
            $options['property']
        );

        $builder->addViewTransformer($transformer);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $value = $view->vars['value'];
        $url = $options['url'];
        $useController = $options['use_controller'];
        $multiple = $options['multiple'];

        if ($value) {
            if ($multiple) {
                // build id string
                $ids = array();
                foreach ($value as $entity) {
                    $ids[] = $entity['id'];
                }
                $view->vars['value'] = implode(',', $ids);
            } else {
                $view->vars['value'] = $value['id'];
            }

            $view->vars['attr']['data-initial'] = json_encode($value);
        }

        if ($useController || $url) {
            $class = 'zenstruck-ajax-entity';

            if (isset($view->vars['attr']['class'])) {
                $class = $view->vars['attr']['class'] . ' ' . $class;
            }

            $view->vars['attr']['class'] = $class . ($multiple ? ' multiple' : '');

            if ($useController) {
                if (null === $this->manager) {
                    throw new MissingOptionsException('Config "zenstruck_form.form_types.ajax_entity_controller" option must be enabled when "use_controller" is true.');
                }

                if (!$options['property'] && !$options['repo_method']) {
                    throw new MissingOptionsException('Either a property or method option must be set.');
                }

                if ($options['repo_method']) {
                    $view->vars['attr']['data-method'] = $this->manager->encriptString($options['repo_method']);
                } else {
                    $view->vars['attr']['data-property'] = $this->manager->encriptString($options['property']);
                }

                $view->vars['attr']['data-entity'] = $this->manager->encriptString($options['class']);
                $url = $this->router->generate('zenstruck_ajax_entity');
            }

            $view->vars['attr']['data-ajax-url'] = $url;
        }

        $view->vars['attr']['data-placeholder'] = $options['placeholder'];
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('class'));
        $resolver->setDefaults(array(
                'placeholder'   => 'Choose an option',
                'use_controller'=> false,
                'url'           => null,
                'repo_method'   => null,
                'property'      => null,
                'multiple'      => false
            ));
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'zenstruck_ajax_entity';
    }
}