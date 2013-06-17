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
     * @param string        $defaultGroup
     * @param array         $order
     */
    public function __construct($form, $defaultGroup = 'Default', $order = array())
    {
        if ($form instanceof Form) {
            $form = $form->createView();
        }

        $this->form = $form;

        // use custom order
        foreach ($order as $item) {
            $this->groups[$item] = array();
        }

        // if no order is set, make default first group
        if (empty($this->groups)) {
            $this->groups[$defaultGroup] = array();
        }

        // add fields to groups
        $this->setGroupsFromForm($this->form->children, $defaultGroup);
        // filter empty groups
        $this->groups = array_filter($this->groups, function($fields) {
                return count($fields);
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

    public function getVars()
    {
        return $this->form->vars;
    }

    public function setGroupsFromForm($form, $defaultGroup)
    {
        foreach ($form as $field) {
            if ($field->count()) {
                $this->setGroupsFromForm($field->children, $defaultGroup);
            } else {
                if ($group = $field->vars['group']) {
                } elseif ($field->parent && $group = $field->getParent()->vars['group']) {
                } else {
                    $group = $defaultGroup;

                }
                $this->groups[$group][] = $field;
            }
        }
    }
}
