<?php

/** Textarea for extension */
require_once 'Zend/View/Helper/FormTextarea.php';

/**
 * Helper to generate a "Redactor" element
 *
 * @category Core
 * @package  Core_View_Helper_FormRedactor
 *
 * <code>
 *
 * required jQuery.js
 *
 * using as form element:
 *
 * $comment = new Core_Form_Element_Redactor('comment', array(
 *    'label' => 'Your comment:',
 *    'cols'  => '50',
 *    'rows'  => '5',
 *    'required' => true,
 *    'filters' => array('StringTrim'),
 *    'redactor' => array(...)
 * ));
 * $form->addElement($comment);
 *
 * $form->addElement('redactor', 'comment', array(
 *    'label' => 'Your comment:',
 *    'cols'  => '50',
 *    'rows'  => '5',
 *    'required' => true,
 *    'filters' => array('StringTrim'),
 *    'redactor' => array(..)
 * ));
 *
 * using as view helper:
 *
 * echo $view->formRedactor('comment', 'ula la', array(
 *    'label' => 'Your comment:',
 *    'cols'  => '50',
 *    'rows'  => '5',
 *    'redactor' => array(...)
 * ));
 *
 * </code>
 *
 */
class Core_View_Helper_FormRedactor extends Zend_View_Helper_FormTextarea
{
    /**
     * Helper to generate a "Redactor" element
     *
     * @param $name
     * @param null $value
     * @param null $attribs
     * @return string
     */
    public function formRedactor($name, $value = null, $attribs = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);
        $id = $this->view->escape($info['id']);
        $options = array(
            'lang' => Zend_Registry::get('Zend_Locale')->getLanguage(),
            'path' => '/scripts/jquery/redactor/' // w/o lang prefix
        );

        if (!empty($attribs['redactor'])) {
            $options = array_merge($options, $attribs['redactor']);
            unset($attribs['redactor']);
        }

        /** add plugin libraries */
        $this->view->plugins()->redactor();

        /** init plugin */
        $options = Zend_Json::encode($options, Zend_Json::TYPE_OBJECT);
        $this->view->headScript()
            ->appendScript('(function($){$(function(){$("#' . $id . '").redactor(' . $options . ');});})(jQuery)');

        /** render text area */
        return $this->formTextarea($name, $value, $attribs);
    }
}
