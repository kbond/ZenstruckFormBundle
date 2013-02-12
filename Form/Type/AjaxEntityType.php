<?php

namespace Zenstruck\Bundle\FormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Zenstruck\Bundle\FormBundle\Form\AjaxEntityManager;
use Zenstruck\Bundle\FormBundle\Form\DataTransformer\AjaxEntityTransformer;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class AjaxEntityType extends AbstractType
{
    protected $registry;
    protected $manager;

    public function __construct(ManagerRegistry $registry, AjaxEntityManager $manager)
    {
        $this->registry = $registry;
        $this->manager = $manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new AjaxEntityTransformer($this->registry, $options['class'], $options['multiple']);

        $builder->addViewTransformer($transformer);

        if ($options['use_controller']) {
            if (!$this->manager->isControllerEnabled()) {
                throw new MissingOptionsException('Config "zenstruck_form.form_types.ajax_entity_controller" option must be enabled when "use_controller" is true.');
            }

            if (!$options['property'] && !$options['method']) {
                throw new MissingOptionsException('Either a property or method option must be set.');
            }

            if ($options['method']) {
                $options['url'] = $this->manager->generateMethodUrl($options['class'], $options['method']);
            } else {
                $options['url'] = $this->manager->generatePropertyUrl($options['class'], $options['property']);
            }
        }

        $builder->setAttribute('placeholder', $options['placeholder']);
        $builder->setAttribute('url', $options['url']);
        $builder->setAttribute('use_controller', $options['use_controller']);
        $builder->setAttribute('multiple', $options['multiple']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $value = $view->vars['value'];
        $url = $form->getConfig()->getAttribute('url');
        $useController = $form->getConfig()->getAttribute('use_controller');
        $multiple = $form->getConfig()->getAttribute('multiple');

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
            $view->vars['attr']['data-ajax-url'] = $url;
            $class = 'zenstruck-ajax-entity';

            if (isset($view->vars['attr']['class'])) {
                $class = $view->vars['attr']['class'] . ' ' . $class;
            }

            $view->vars['attr']['class'] = $class . ($multiple ? ' multiple' : '');
        }

        $view->vars['attr']['data-placeholder'] = $form->getConfig()->getAttribute('placeholder');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('class'));
        $resolver->setDefaults(array(
                'placeholder'   => 'Choose an option',
                'use_controller'=> false,
                'url'           => null,
                'method'        => null,
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