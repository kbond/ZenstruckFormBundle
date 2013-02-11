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
        if (!$options['url']) {
            if (!$this->manager->isControllerEnabled()) {
                throw new MissingOptionsException('URL must be set if not using the ajax_entity_controller');
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

        $transformer = new AjaxEntityTransformer($this->registry, $options['class'], $options['separator']);

        $builder->addViewTransformer($transformer);

        $builder->setAttribute('separator', $options['separator']);
        $builder->setAttribute('placeholder', $options['empty_value']);
        $builder->setAttribute('url', $options['url']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['separator'] = $form->getConfig()->getAttribute('separator');
        $view->vars['placeholder'] = $form->getConfig()->getAttribute('placeholder');
        $view->vars['url'] = $form->getConfig()->getAttribute('url');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('class'));
        $resolver->setDefaults(array(
                'separator'     => '|',
                'empty_value'   => 'Choose an option',
                'url'           => null,
                'method'        => null,
                'property'      => null
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