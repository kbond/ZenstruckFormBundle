<?php

namespace Zenstruck\Bundle\FormBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormView;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class GroupedFormView
{
    protected $groups = array();
    protected $data = array();
    protected $form;

    /**
     * @param Form|FormView $form
     * @param string $defaultGroup
     */
    public function __construct($form, $defaultGroup = 'Default')
    {
        if ($form instanceof Form) {
            $form = $form->createView();
        }

        $this->form = $form;

        foreach ($this->form->children as $field) {
            $this->groups[$field->vars['group'] ?: $defaultGroup][] = $field;
        }

        uksort($this->groups, function($a, $b) use ($defaultGroup) {
                return $a !== $defaultGroup;
            });
    }

    public function getForm()
    {
        return $this->form;
    }

    public function isValid($group = null)
    {
        if (!$group) {
            return $this->form->vars['valid'];
        }

        $valid = true;

        foreach ($this->groups[$group] as $field) {
            if (!$field->vars['valid']) {
                $valid = false;
            }
        }

        return $valid;
    }

    public function getGroupNames()
    {
        return array_keys($this->groups);
    }

    public function getGroups()
    {
        return $this->groups;
    }

    public function setData($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function getData($name, $default = null)
    {
        if (!$this->hasData($name)) {
            return $default;
        }

        return $this->data[$name];
    }

    public function hasData($name)
    {
        return array_key_exists($name, $this->data);
    }
}